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
        $sortBy = $request->get('sort', '');

        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('first_name', 'desc')->orderBy('last_name', 'desc');
                break;
            case 'id_asc':
                $query->orderBy('employee_id', 'asc');
                break;
            case 'id_desc':
                $query->orderBy('employee_id', 'desc');
                break;
            case 'department_asc':
                $query->join('departments', 'employees.department_id', '=', 'departments.id')
                    ->orderBy('departments.name', 'asc')
                    ->select('employees.*');
                break;
            case 'department_desc':
                $query->join('departments', 'employees.department_id', '=', 'departments.id')
                    ->orderBy('departments.name', 'desc')
                    ->select('employees.*');
                break;
            default:
                $query->orderBy('created_at', 'desc');
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
        $employee->load(['department', 'designation', 'attendance', 'leaves', 'user']);
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
            'gender' => 'required|in:Male,Female',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'joining_date' => 'required|date',
            'hr_access_code' => 'required|string|size:8',
        ]);

        // Verify HR access code
        $hrUser = Auth::user();
        if (!$hrUser->employee || $validated['hr_access_code'] !== $hrUser->employee->access_code) {
            return back()->withErrors(['hr_access_code' => 'Invalid HR access code. Please enter your correct 8-digit access code.'])->withInput();
        }

        // Generate username (firstname.lastname format with uniqueness check)
        $baseUsername = strtolower($validated['first_name'] . '.' . $validated['last_name']);
        $baseUsername = preg_replace('/[^a-z0-9.]/', '', $baseUsername); // Remove special chars except dot
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter++;
        }

        // Generate temp password (10 characters for consistency)
        $tempPassword = Str::random(10);

        // Generate access code (8-digit numeric with uniqueness check)
        do {
            $accessCode = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        } while (Employee::where('access_code', $accessCode)->exists());

        // Generate employee ID (year-based format: EMP-2025-001)
        $year = date('Y');
        $lastEmp = Employee::where('employee_id', 'like', "EMP-$year-%")->latest('id')->first();
        if ($lastEmp) {
            $parts = explode('-', $lastEmp->employee_id);
            $nextNum = intval(end($parts)) + 1;
        } else {
            $nextNum = 1;
        }
        $employeeId = "EMP-$year-" . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        // Set employee data
        $validated['employee_id'] = $employeeId;
        $validated['access_code'] = $accessCode;
        $validated['status'] = 'active';

        // Remove hr_access_code from validated data before creating employee
        unset($validated['hr_access_code']);

        // Check if designation is "Payroll Manager" and assign appropriate role
        $designation = Designation::find($validated['designation_id']);
        $userRole = 'employee'; // Default role

        if ($designation && $designation->name === 'Payroll Manager') {
            $userRole = 'payroll_manager';
        }

        // Create user account first
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'username' => $username,
            'password' => Hash::make($tempPassword),
            'temp_password' => $tempPassword,
            'role' => $userRole,
        ]);

        // Link user to employee
        $validated['user_id'] = $user->id;

        // Create employee
        $employee = Employee::create($validated);

        return redirect()->route('employees.index', ['tab' => 'employees'])->with('success', 'Employee created successfully!');
    }

    public function update(Request $request, Employee $employee)
    {
        $action = $request->input('action', 'update');

        if ($action === 'terminate') {
            // Handle termination - DELETE the employee
            $request->validate([
                'termination_reason' => 'required|string|max:500',
                'access_code' => 'required|string|size:8',
            ]);

            // Get the logged-in HR user's access code
            $hrUser = Auth::user();

            // Check if access code matches the HR user's access code (not employee's)
            if (!$hrUser->employee || $request->access_code !== $hrUser->employee->access_code) {
                return back()->withErrors(['access_code' => 'Invalid HR access code. Please enter your correct 8-digit access code.'])->withInput();
            }

            // Store user reference before deleting employee
            $user = $employee->user;

            // Delete the employee FIRST (this removes the foreign key reference)
            $employee->delete();

            // Then delete the user account (if exists)
            if ($user) {
                $user->delete();
            }

            return redirect()->route('employees.index', ['tab' => 'employees'])->with('success', 'Employee has been terminated and removed from the system.');
        } else {
            // Handle regular update
            $validated = $request->validate([
                'joining_date' => 'required|date',
                'department_id' => 'required|exists:departments,id',
                'designation_id' => 'required|exists:designations,id',
                'access_code' => 'required|string|size:8',
            ]);

            // Get the logged-in HR user's access code
            $hrUser = Auth::user();

            // Check if access code matches the HR user's access code
            if (!$hrUser->employee || $request->access_code !== $hrUser->employee->access_code) {
                return back()->withErrors(['access_code' => 'Invalid HR access code. Please enter your correct 8-digit access code.'])->withInput();
            }

            $employee->update($validated);

            return redirect()->route('employees.show', $employee->id)->with('success', 'Employee information updated successfully!');
        }
    }
}