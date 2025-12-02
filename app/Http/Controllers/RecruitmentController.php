<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Models\Candidate;
use App\Models\Department;
use Illuminate\Http\Request;

class RecruitmentController extends Controller
{
    public function index()
    {
        $jobs = JobListing::with('department')->withCount('candidates')->latest()->get();
        return view('recruitment.index', compact('jobs'));
    }

    public function show(JobListing $job)
    {
        // Show job details and list of candidates applied
        $job->load(['candidates', 'department']);
        return view('recruitment.show', compact('job'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('recruitment.create', compact('departments'));
    }

    public function store(Request $request)
    {
        // Logic to create a new job posting
        $validated = $request->validate([
            'title' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'location' => 'required|string',
            'type' => 'required|string',
            'closing_date' => 'required|date',
        ]);

        JobListing::create($validated);
        return redirect()->route('recruitment.index')->with('success', 'Job posted successfully.');
    }
}