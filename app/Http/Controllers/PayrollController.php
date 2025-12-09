<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Allowance;
use App\Models\Deduction;
use App\Models\Attendance;
use App\Models\DesignationPayrollTemplate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Payroll::with('employee')->latest();

        // Check if user is Payroll Manager or Accountant
        $isPayrollManager = $user->role === 'payroll_manager' ||
            ($user->employee && $user->employee->designation && $user->employee->designation->name === 'Payroll Manager');

        $isAccountant = $user->employee &&
            $user->employee->designation &&
            $user->employee->designation->name === 'Accountant';

        // IF PAYROLL MANAGER OR ACCOUNTANT: Show All
        if ($isPayrollManager || $isAccountant) {
            // Do nothing (shows all)
        }
        // IF ANYONE ELSE: Show Only Own
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
        $this->authorizePayrollManager();
        $allowances = Allowance::orderBy('name')->get();
        $deductions = Deduction::orderBy('name')->get();
        return view('payroll.settings', compact('allowances', 'deductions'));
    }

    public function storeAllowance(Request $request)
    {
        $this->authorizePayrollManager();

        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Allowance::create($request->all());

        return back()->with('success', 'Allowance added successfully!');
    }

    public function updateAllowance(Request $request, $id)
    {
        $this->authorizePayrollManager();

        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $allowance = Allowance::findOrFail($id);
        $allowance->update($request->all());

        return back()->with('success', 'Allowance updated successfully!');
    }

    public function destroyAllowance($id)
    {
        $this->authorizePayrollManager();
        Allowance::findOrFail($id)->delete();
        return back()->with('success', 'Allowance deleted successfully!');
    }

    public function storeDeduction(Request $request)
    {
        $this->authorizePayrollManager();

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        Deduction::create($request->all());

        return back()->with('success', 'Deduction added successfully!');
    }

    public function updateDeduction(Request $request, $id)
    {
        $this->authorizePayrollManager();

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $deduction = Deduction::findOrFail($id);
        $deduction->update($request->all());

        return back()->with('success', 'Deduction updated successfully!');
    }

    public function destroyDeduction($id)
    {
        $this->authorizePayrollManager();
        Deduction::findOrFail($id)->delete();
        return back()->with('success', 'Deduction deleted successfully!');
    }

    public function create()
    {
        $this->authorizePayrollManager();
        $employees = Employee::where('status', 'active')->with('designation')->get();
        $allowances = Allowance::where('is_active', true)->get();
        $deductions = Deduction::where('is_active', true)->get();
        $currentMonth = now()->format('F Y');

        return view('payroll.create', compact('employees', 'allowances', 'deductions', 'currentMonth'));
    }

    public function store(Request $request)
    {
        $this->authorizePayrollManager();

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month_year' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'allowances' => 'nullable|array',
            'deductions' => 'nullable|array',
        ]);

        $employee = Employee::with('designation')->findOrFail($request->employee_id);

        // 1. Calculate total hours from attendance
        $totalHours = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$request->start_date, $request->end_date])
            ->whereNotNull('total_hours')
            ->sum('total_hours');

        // 2. Get hourly rate
        $hourlyRate = $employee->hourly_rate ?? 0;

        // 3. Calculate base pay
        $basePay = $hourlyRate * $totalHours;

        // 4. Get allowances
        $allowancesBreakdown = [];
        $totalAllowances = 0;

        // Add selected global allowances
        if ($request->allowances) {
            foreach ($request->allowances as $allowanceId) {
                $allowance = Allowance::find($allowanceId);
                if ($allowance) {
                    $allowancesBreakdown[] = [
                        'name' => $allowance->name,
                        'amount' => $allowance->amount,
                    ];
                    $totalAllowances += $allowance->amount;
                }
            }
        }

        // Add designation template allowances
        $template = DesignationPayrollTemplate::where('designation_id', $employee->designation_id)->first();
        if ($template && $template->base_allowance > 0) {
            $allowancesBreakdown[] = [
                'name' => 'Designation Allowance (' . $employee->designation->name . ')',
                'amount' => $template->base_allowance,
            ];
            $totalAllowances += $template->base_allowance;
        }

        // 5. Calculate gross pay
        $grossPay = $basePay + $totalAllowances;

        // 6. Calculate deductions
        $deductionsBreakdown = [];
        $totalDeductions = 0;

        if ($request->deductions) {
            foreach ($request->deductions as $deductionId) {
                $deduction = Deduction::find($deductionId);
                if ($deduction) {
                    $amount = 0;
                    if ($deduction->type === 'percentage') {
                        $amount = ($grossPay * $deduction->value) / 100;
                    } else {
                        $amount = $deduction->value;
                    }

                    $deductionsBreakdown[] = [
                        'name' => $deduction->name,
                        'type' => $deduction->type,
                        'value' => $deduction->value,
                        'amount' => $amount,
                    ];
                    $totalDeductions += $amount;
                }
            }
        }

        // 7. Calculate net salary
        $netSalary = max(0, $grossPay - $totalDeductions);

        // 8. Create payroll record
        Payroll::create([
            'employee_id' => $employee->id,
            'month_year' => $request->month_year,
            'basic_salary' => $basePay, // Store base pay here for compatibility
            'hourly_rate' => $hourlyRate,
            'total_hours' => $totalHours,
            'total_allowance' => $totalAllowances,
            'allowances_breakdown' => $allowancesBreakdown,
            'total_deduction' => $totalDeductions,
            'deductions_breakdown' => $deductionsBreakdown,
            'net_salary' => $netSalary,
            'status' => 'pending',
        ]);

        return redirect()->route('payroll.index')->with('success', 'Payroll created successfully!');
    }

    // Helper to secure methods
    private function authorizePayrollManager()
    {
        $user = Auth::user();

        $isPayrollManager = $user->role === 'payroll_manager' ||
            ($user->employee && $user->employee->designation && $user->employee->designation->name === 'Payroll Manager');

        $isAccountant = $user->employee &&
            $user->employee->designation &&
            $user->employee->designation->name === 'Accountant';

        if (!$isPayrollManager && !$isAccountant) {
            abort(403, 'Unauthorized action. Only Payroll Managers and Accountants can access this.');
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

        // Load relationships
        $payroll->load(['employee.department', 'employee.designation']);

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.payslip-pdf', compact('payroll'));

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        // Download the PDF
        return $pdf->download('payslip_' . $payroll->month_year . '_' . $payroll->employee->employee_id . '.pdf');
    }
}