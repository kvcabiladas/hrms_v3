<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'designation'])->latest()->paginate(10);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        if (Auth::user()->role === 'employee') {
            abort(403, 'Unauthorized action.');
        }

        // FILTERED DROPDOWNS
        // 1. Exclude 'Administration' (Super Admin) and 'HR' (Since Super Admin creates HRs)
        $departments = Department::whereNotIn('name', ['Administration', 'HR', 'Human Resources'])->get();
        
        // 2. Exclude 'Super Admin' and 'HR Manager' positions
        $designations = Designation::whereNotIn('name', ['Super Admin', 'HR Manager'])->get();

        return view('employees.create', compact('departments', 'designations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|regex:/^[0-9]+$/',
            'address' => 'required|string',
            'joining_date' => 'required|date',
            'designation_id' => 'required',
            'hr_access_code' => 'required|string',
        ]);

        // Security Check
        $currentHr = Auth::user()->employee;
        if (!$currentHr || $currentHr->access_code !== $request->hr_access_code) {
            return back()->withErrors(['hr_access_code' => 'Invalid Access Code. Authorization failed.'])->withInput();
        }

        if ($request->department_id === 'other') {
            $request->validate(['new_department' => 'required|string|unique:departments,name']);
            $department = Department::create(['name' => $request->new_department]);
            $department_id = $department->id;
        } else {
            $request->validate(['department_id' => 'required|exists:departments,id']);
            $department_id = $request->department_id;
        }

        $baseUsername = strtolower(substr($request->first_name, 0, 1) . $request->last_name);
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername); 
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter++;
        }

        $tempPassword = Str::random(10);

        // ALWAYS create as 'employee' role
        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'username' => $username,
            'password' => Hash::make($tempPassword),
            'temp_password' => $tempPassword,
            'role' => 'employee',
        ]);

        $year = date('Y');
        $lastEmp = Employee::where('employee_id', 'like', "EMP-$year-%")->latest('id')->first();
        if ($lastEmp) {
            $parts = explode('-', $lastEmp->employee_id);
            $nextNum = intval(end($parts)) + 1;
        } else {
            $nextNum = 1;
        }
        $employeeId = "EMP-$year-" . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        Employee::create([
            'user_id' => $user->id,
            'employee_id' => $employeeId,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'joining_date' => $request->joining_date,
            'department_id' => $department_id,
            'designation_id' => $request->designation_id,
            'basic_salary' => $request->basic_salary ?? 0,
            'status' => 'probation',
            'gender' => $request->gender ?? 'other',
        ]);

        return view('employees.credentials', [
            'name' => $user->name,
            'username' => $username,
            'password' => $tempPassword,
            'employee_id' => $employeeId
        ]);
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'designation', 'attendance', 'leaves']);
        return view('employees.show', compact('employee'));
    }
}