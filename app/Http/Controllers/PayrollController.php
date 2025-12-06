<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\SalaryComponent;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        $query = Payroll::with('employee')->latest();

        // RESTRICTION: Employees see only their own
        if ($user->role === 'employee' && $user->employee) {
            $query->where('employee_id', $user->employee->id);
        }

        $payrolls = $query->paginate(10);
        return view('payroll.index', compact('payrolls'));
    }

    public function settings()
    {
        $allowances = SalaryComponent::where('type', 'allowance')->get();
        $deductions = SalaryComponent::where('type', 'deduction')->get();
        return view('payroll.settings', compact('allowances', 'deductions'));
    }

    public function updateSettings(Request $request)
    {
        SalaryComponent::truncate();

        if ($request->has('allowances')) {
            foreach (json_decode($request->allowances, true) as $item) {
                SalaryComponent::create(['name' => $item['name'], 'type' => 'allowance', 'value_type' => $item['value_type'], 'value' => $item['value']]);
            }
        }

        if ($request->has('deductions')) {
            foreach (json_decode($request->deductions, true) as $item) {
                SalaryComponent::create(['name' => $item['name'], 'type' => 'deduction', 'value_type' => $item['value_type'], 'value' => $item['value']]);
            }
        }

        return back()->with('success', 'Payroll settings saved successfully.');
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        $currentMonth = now()->format('Y-m');
        return view('payroll.create', compact('employees', 'currentMonth'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required',
            'selected_employees' => 'required|array',
            'salaries' => 'array', // Validate the new salaries array
        ]);

        $monthYear = Carbon::parse($request->month)->format('F Y');
        $count = 0;

        $allowanceSettings = SalaryComponent::where('type', 'allowance')->get();
        $deductionSettings = SalaryComponent::where('type', 'deduction')->get();

        foreach ($request->selected_employees as $empId) {
            $employee = Employee::find($empId);
            
            if ($employee) {
                // USE THE SUBMITTED SALARY (Or fallback to DB value)
                $basic = isset($request->salaries[$empId]) ? floatval($request->salaries[$empId]) : $employee->basic_salary;
                
                $totalAllowance = 0;
                $totalDeduction = 0;

                foreach ($allowanceSettings as $a) {
                    if ($a->value_type === 'percentage') {
                        $totalAllowance += ($basic * ($a->value / 100));
                    } else {
                        $totalAllowance += $a->value;
                    }
                }

                foreach ($deductionSettings as $d) {
                    if ($d->value_type === 'percentage') {
                        $totalDeduction += ($basic * ($d->value / 100));
                    } else {
                        $totalDeduction += $d->value;
                    }
                }

                $net = $basic + $totalAllowance - $totalDeduction;

                Payroll::create([
                    'employee_id' => $employee->id,
                    'month_year' => $monthYear,
                    'basic_salary' => $basic,
                    'total_allowance' => $totalAllowance,
                    'total_deduction' => $totalDeduction,
                    'net_salary' => $net,
                    'status' => 'paid',
                ]);
                $count++;
            }
        }

        return redirect()->route('payroll.index')->with('success', "Payroll processed for $count employees.");
    }
}