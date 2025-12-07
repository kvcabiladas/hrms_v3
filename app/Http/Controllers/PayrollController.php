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
}