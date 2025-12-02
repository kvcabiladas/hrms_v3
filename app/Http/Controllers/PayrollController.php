<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('employee')->latest()->paginate(10);
        return view('payroll.index', compact('payrolls'));
    }

    public function show(Payroll $payroll)
    {
        return view('payroll.show', compact('payroll'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('payroll.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month_year' => 'required|string',
            'basic_salary' => 'required|numeric',
            'total_allowance' => 'required|numeric',
            'total_deduction' => 'required|numeric',
        ]);

        $validated['net_salary'] = $validated['basic_salary'] + $validated['total_allowance'] - $validated['total_deduction'];
        $validated['status'] = 'pending';

        Payroll::create($validated);

        return redirect()->route('payroll.index')->with('success', 'Payroll generated successfully.');
    }
}