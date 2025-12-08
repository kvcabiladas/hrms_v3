<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollRule;
use App\Models\DesignationPayrollTemplate;
use App\Models\Designation;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();

            // Check if user has payroll_manager role OR has Payroll Manager designation
            $hasPayrollRole = $user->role === 'payroll_manager';
            $hasPayrollDesignation = $user->employee &&
                $user->employee->designation &&
                $user->employee->designation->name === 'Payroll Manager';

            if (!$hasPayrollRole && !$hasPayrollDesignation) {
                abort(403, 'Unauthorized action.');
            }

            return $next($request);
        });
    }

    /**
     * Payroll Manager Dashboard with Analytics
     */
    public function dashboard()
    {
        $currentMonth = Carbon::now()->format('F Y');
        $currentYear = Carbon::now()->year;

        // Total payroll expenses
        $totalMonthlyPayroll = Payroll::where('month_year', $currentMonth)->sum('net_salary');
        $totalYearlyPayroll = Payroll::whereYear('created_at', $currentYear)->sum('net_salary');

        // Payroll status breakdown
        $pendingPayrolls = Payroll::where('status', 'pending')->count();
        $paidPayrolls = Payroll::where('status', 'paid')
            ->where('month_year', $currentMonth)
            ->count();

        // Department-wise payroll breakdown for current month
        $departmentPayroll = Department::withCount('employees')
            ->with([
                'employees' => function ($query) use ($currentMonth) {
                    $query->with([
                        'payrolls' => function ($q) use ($currentMonth) {
                            $q->where('month_year', $currentMonth);
                        }
                    ]);
                }
            ])
            ->get()
            ->map(function ($dept) {
                $totalSalary = $dept->employees->sum(function ($emp) {
                    return $emp->payrolls->sum('net_salary');
                });
                return [
                    'name' => $dept->name,
                    'employee_count' => $dept->employees_count,
                    'total_salary' => $totalSalary,
                ];
            });

        // Recent payroll transactions
        $recentPayrolls = Payroll::with('employee')
            ->latest()
            ->take(10)
            ->get();

        // Monthly trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('F Y');
            $total = Payroll::where('month_year', $month)->sum('net_salary');
            $monthlyTrend[] = [
                'month' => $month,
                'total' => $total,
            ];
        }

        return view('payroll-manager.dashboard', compact(
            'totalMonthlyPayroll',
            'totalYearlyPayroll',
            'pendingPayrolls',
            'paidPayrolls',
            'departmentPayroll',
            'recentPayrolls',
            'monthlyTrend',
            'currentMonth'
        ));
    }

    /**
     * Employee List with Payroll Focus
     */
    public function employees(Request $request)
    {
        $query = Employee::with(['department', 'designation', 'payrolls']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->has('department') && $request->department != '') {
            $query->where('department_id', $request->department);
        }

        $employees = $query->orderBy('first_name')->paginate(15);
        $departments = Department::orderBy('name')->get();

        return view('payroll-manager.employees', compact('employees', 'departments'));
    }

    /**
     * View Employee Payroll Details
     */
    public function employeePayroll($id)
    {
        $employee = Employee::with(['department', 'designation', 'payrolls', 'user'])
            ->findOrFail($id);

        $payrollHistory = $employee->payrolls()
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get designation template if exists
        $template = DesignationPayrollTemplate::where('designation_id', $employee->designation_id)->first();

        return view('payroll-manager.employee-payroll', compact('employee', 'payrollHistory', 'template'));
    }

    /**
     * Update Employee Basic Salary
     */
    public function updateBasicSalary(Request $request, $id)
    {
        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->update([
            'basic_salary' => $request->basic_salary,
        ]);

        return back()->with('success', 'Basic salary updated successfully!');
    }

    /**
     * Payroll Rules Management
     */
    public function rules()
    {
        $rules = PayrollRule::orderBy('rule_name')->get();
        return view('payroll-manager.rules', compact('rules'));
    }

    /**
     * Store Payroll Rule
     */
    public function storeRule(Request $request)
    {
        $request->validate([
            'rule_name' => 'required|string|max:255',
            'rule_type' => 'required|in:percentage,fixed_amount,multiplier',
            'value' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        PayrollRule::create($request->all());

        return back()->with('success', 'Payroll rule created successfully!');
    }

    /**
     * Update Payroll Rule
     */
    public function updateRule(Request $request, $id)
    {
        $request->validate([
            'rule_name' => 'required|string|max:255',
            'rule_type' => 'required|in:percentage,fixed_amount,multiplier',
            'value' => 'required|numeric',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $rule = PayrollRule::findOrFail($id);
        $rule->update($request->all());

        return back()->with('success', 'Payroll rule updated successfully!');
    }

    /**
     * Delete Payroll Rule
     */
    public function destroyRule($id)
    {
        PayrollRule::findOrFail($id)->delete();
        return back()->with('success', 'Payroll rule deleted successfully!');
    }

    /**
     * Designation Templates Management
     */
    public function templates()
    {
        $templates = DesignationPayrollTemplate::with('designation')->get();
        $designations = Designation::with('department')->orderBy('name')->get();

        return view('payroll-manager.templates', compact('templates', 'designations'));
    }

    /**
     * Store Designation Template
     */
    public function storeTemplate(Request $request)
    {
        $request->validate([
            'designation_id' => 'required|exists:designations,id|unique:designation_payroll_templates,designation_id',
            'base_allowance' => 'required|numeric|min:0',
            'overtime_multiplier' => 'required|numeric|min:1',
            'benefits' => 'nullable|array',
            'description' => 'nullable|string',
        ]);

        DesignationPayrollTemplate::create($request->all());

        return back()->with('success', 'Designation template created successfully!');
    }

    /**
     * Update Designation Template
     */
    public function updateTemplate(Request $request, $id)
    {
        $request->validate([
            'base_allowance' => 'required|numeric|min:0',
            'overtime_multiplier' => 'required|numeric|min:1',
            'benefits' => 'nullable|array',
            'description' => 'nullable|string',
        ]);

        $template = DesignationPayrollTemplate::findOrFail($id);
        $template->update($request->all());

        return back()->with('success', 'Designation template updated successfully!');
    }

    /**
     * Delete Designation Template
     */
    public function destroyTemplate($id)
    {
        DesignationPayrollTemplate::findOrFail($id)->delete();
        return back()->with('success', 'Designation template deleted successfully!');
    }
}
