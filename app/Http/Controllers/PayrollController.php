<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\SalaryComponent;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Payroll::with('employee')->latest();

        // Check if user is an Accountant
        $isAccountant = $user->employee &&
            $user->employee->designation &&
            $user->employee->designation->name === 'Accountant';

        // IF ACCOUNTANT: Show All
        if ($isAccountant) {
            // Do nothing (shows all)
        }
        // IF ANYONE ELSE (Super Admin, HR, Regular Employee): Show Only Own
        else {
            if ($user->employee) {
                $query->where('employee_id', $user->employee->id);
            } else {
                $query->where('id', 0); // Show nothing if no profile
            }
        }

        $payrolls = $query->paginate(10);
        return view('payroll.index', compact('payrolls'));
    }

    public function settings()
    {
        $this->authorizeAccountant();
        $allowances = SalaryComponent::where('type', 'allowance')->get();
        $deductions = SalaryComponent::where('type', 'deduction')->get();
        return view('payroll.settings', compact('allowances', 'deductions'));
    }

    public function updateSettings(Request $request)
    {
        $this->authorizeAccountant();
        // ... (Keep existing logic)
        SalaryComponent::truncate();
        // ... (Keep existing logic)
        return back()->with('success', 'Payroll settings saved successfully.');
    }

    public function create()
    {
        $this->authorizeAccountant();
        $employees = Employee::where('status', 'active')->get();
        $currentMonth = now()->format('Y-m');
        return view('payroll.create', compact('employees', 'currentMonth'));
    }

    public function store(Request $request)
    {
        $this->authorizeAccountant();
        // ... (Keep existing logic)
        // ...
        return redirect()->route('payroll.index')->with('success', "Payroll processed.");
    }

    // Helper to secure methods
    private function authorizeAccountant()
    {
        $user = Auth::user();
        $isAccountant = $user->employee &&
            $user->employee->designation &&
            $user->employee->designation->name === 'Accountant';

        if (!$isAccountant) {
            abort(403, 'Unauthorized action. Only Accountants can access this.');
        }
    }

    /**
     * Personal payroll view - shows only the logged-in user's payroll history
     */
    public function personalPayroll()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('dashboard')->with('error', 'No employee profile linked.');
        }

        $payrolls = Payroll::where('employee_id', $employee->id)
            ->latest('created_at')
            ->paginate(15);

        $lastPayroll = Payroll::where('employee_id', $employee->id)
            ->latest('created_at')
            ->first();

        return view('personal.payroll', compact('payrolls', 'employee', 'lastPayroll'));
    }

    /**
     * View detailed payslip
     */
    public function viewPayslip(Payroll $payroll)
    {
        $user = Auth::user();

        // Ensure user can only view their own payslip
        if ($payroll->employee_id !== $user->employee->id) {
            abort(403, 'Unauthorized access.');
        }

        return view('personal.payslip', compact('payroll'));
    }

    /**
     * Download payslip as PDF
     */
    public function downloadPayslip(Payroll $payroll)
    {
        $user = Auth::user();

        // Ensure user can only download their own payslip
        if ($payroll->employee_id !== $user->employee->id) {
            abort(403, 'Unauthorized access.');
        }

        // For now, return a simple text response
        // In production, you would use a PDF library like DomPDF or TCPDF
        $content = "PAYSLIP\n\n";
        $content .= "Employee: " . $payroll->employee->first_name . " " . $payroll->employee->last_name . "\n";
        $content .= "Payment Date: " . $payroll->payment_date->format('M d, Y') . "\n";
        $content .= "Period: " . $payroll->period_start->format('M d') . " - " . $payroll->period_end->format('M d, Y') . "\n\n";
        $content .= "Basic Salary: ₱" . number_format($payroll->basic_salary, 2) . "\n";
        $content .= "Deductions: ₱" . number_format($payroll->total_deductions, 2) . "\n";
        $content .= "Net Pay: ₱" . number_format($payroll->net_salary, 2) . "\n";

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="payslip_' . $payroll->id . '.txt"');
    }
}