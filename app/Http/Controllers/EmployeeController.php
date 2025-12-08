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
    public function index(Request $request)
    {
        $query = Employee::with(['department', 'designation']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sort
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        // Allowed sort columns
        if (in_array($sort, ['first_name', 'last_name', 'employee_id', 'created_at'])) {
            $query->orderBy($sort, $direction);
        }

        $employees = $query->paginate(15);

        // Get departments and designations for the tabs
        $departments = Department::withCount('employees')->orderBy('name')->get();
        $designations = Designation::with('department')->withCount('employees')->orderBy('name')->get();

        return view('employees.index', compact('employees', 'departments', 'designations'));
    }

    // ... create, store, show methods remain as defined previously ...
    // Copy the store method with the 'Other' department logic from previous response if needed.

    // I will include the show method here as it needs the Profile Banner update
    public function show(Employee $employee)
    {
        $employee->load(['department', 'designation', 'attendance', 'leaves']);
        return view('employees.show', compact('employee'));
    }

    // Stub for create/store to keep file valid (assume you have them from previous steps)
    public function create()
    {
        $departments = Department::all();
        $designations = Designation::all();
        return view('employees.create', compact('departments', 'designations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'joining_date' => 'required|date',
            'basic_salary' => 'nullable|numeric|min:0',
        ]);

        // Generate employee ID
        $lastEmployee = Employee::latest('id')->first();
        $nextId = $lastEmployee ? $lastEmployee->id + 1 : 1;
        $validated['employee_id'] = 'EMP' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'active';

        // Generate access code
        $validated['access_code'] = strtoupper(Str::random(6));

        // Create employee
        $employee = Employee::create($validated);

        // Create user account
        $username = strtolower($validated['first_name'] . '.' . $validated['last_name']);
        $tempPassword = Str::random(8);

        User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'username' => $username,
            'password' => Hash::make($tempPassword),
            'temp_password' => $tempPassword,
            'role' => 'employee',
            'employee_id' => $employee->id,
        ]);

        return redirect()->route('employees.index', ['tab' => 'employees'])->with('success', 'Employee created successfully!');
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'joining_date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.show', $employee->id)->with('success', 'Employee information updated successfully!');
    }
}