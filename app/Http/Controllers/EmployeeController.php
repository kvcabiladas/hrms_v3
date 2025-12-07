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
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Sort
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        
        // Allowed sort columns
        if (in_array($sort, ['first_name', 'last_name', 'employee_id', 'created_at'])) {
            $query->orderBy($sort, $direction);
        }

        $employees = $query->paginate(10);
        return view('employees.index', compact('employees'));
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
    public function create() { /* ... */ }
    public function store(Request $request) { /* ... */ }
}