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
        try {
            // Delete all designations/jobs under this department
            $department->designations()->delete();

            // Set employees' department_id to null (will show as N/A in views)
            $department->employees()->update(['department_id' => null]);

            // Delete the department
            $department->delete();

            return redirect()->route('employees.index', ['tab' => 'departments'])->with('success', 'Department and all associated jobs deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('employees.index', ['tab' => 'departments'])->with('error', 'Failed to delete department: ' . $e->getMessage());
        }
    }
}
