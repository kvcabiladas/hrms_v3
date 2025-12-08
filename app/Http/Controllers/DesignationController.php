<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    /**
     * Store a newly created designation/job
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id'
        ]);

        Designation::create($validated);

        return back()->with('success', 'Job created successfully!');
    }

    /**
     * Update the specified designation/job
     */
    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id'
        ]);

        $designation->update($validated);

        return back()->with('success', 'Job updated successfully!');
    }

    /**
     * Remove the specified designation/job
     */
    public function destroy(Designation $designation)
    {
        // Set employees' designation to null
        $designation->employees()->update(['designation_id' => null]);

        $designation->delete();

        return back()->with('success', 'Job deleted successfully!');
    }
}
