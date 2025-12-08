<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Store a newly created department
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name'
        ]);

        Department::create($validated);

        return redirect()->route('employees.index', ['tab' => 'departments'])->with('success', 'Department created successfully!');
    }

    /**
     * Update the specified department
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id
        ]);

        $department->update($validated);

        return redirect()->route('employees.index', ['tab' => 'departments'])->with('success', 'Department updated successfully!');
    }

    /**
     * Remove the specified department
     */
    public function destroy(Department $department)
    {
        // Delete associated designations
        $department->designations()->delete();

        // Set employees' department to null
        $department->employees()->update(['department_id' => null]);

        $department->delete();

        return redirect()->route('employees.index', ['tab' => 'departments'])->with('success', 'Department deleted successfully!');
    }
}
