<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->user()->role !== 'super_admin') {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'hr_count' => User::where('role', 'hr')->count(),
            'employee_count' => User::where('role', 'employee')->count(),
            'dept_count' => Department::count(),
        ];

        $hrPersonnel = User::where('role', 'hr')
            ->with('employee.department')
            ->latest()
            ->get();
        
        return view('superadmin.dashboard', compact('stats', 'hrPersonnel'));
    }

    public function createHr()
    {
        // No longer need to fetch departments/designations
        return view('superadmin.create_hr');
    }

    public function storeHr(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'joining_date' => 'required|date',
        ]);

        // 1. AUTO-ASSIGN ROLE: Fetch the specific HR IDs
        // Note: These names must match what is in your DatabaseSeeder
        $hrDept = Department::where('name', 'Human Resources')->orWhere('name', 'HR')->first();
        $hrDesig = Designation::where('name', 'HR Manager')->first();

        if (!$hrDept || !$hrDesig) {
            return back()->with('error', 'System Error: "Human Resources" department or "HR Manager" position not found in database. Please run seeders.');
        }

        // 2. Generate Credentials
        $initials = substr($request->first_name, 0, 1);
        $baseUsername = strtolower($initials . $request->last_name);
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername); 
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter++;
        }

        $tempPassword = Str::random(10); 

        do {
            $accessCode = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        } while (Employee::where('access_code', $accessCode)->exists());

        $year = date('Y');
        $lastEmp = Employee::where('employee_id', 'like', "HR-$year-%")->latest('id')->first();
        if ($lastEmp) {
            $parts = explode('-', $lastEmp->employee_id);
            $nextNum = intval(end($parts)) + 1;
        } else {
            $nextNum = 1;
        }
        $employeeId = "HR-$year-" . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        // 3. Create User
        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'username' => $username,
            'password' => Hash::make($tempPassword),
            'temp_password' => $tempPassword,
            'role' => 'hr',
        ]);

        // 4. Create Employee Profile (With Auto-assigned Dept/Position)
        Employee::create([
            'user_id' => $user->id,
            'employee_id' => $employeeId,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'joining_date' => $request->joining_date,
            'department_id' => $hrDept->id,      // AUTO-ASSIGNED
            'designation_id' => $hrDesig->id,    // AUTO-ASSIGNED
            'basic_salary' => 0, 
            'status' => 'active',
            'gender' => 'other', 
            'access_code' => $accessCode,
        ]);

        return view('superadmin.credentials', [
            'name' => $user->name,
            'username' => $username,
            'password' => $tempPassword,
            'access_code' => $accessCode,
            'employee_id' => $employeeId,
            'role' => 'HR Personnel'
        ]);
    }
}