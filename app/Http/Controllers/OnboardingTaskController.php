<?php
namespace App\Http\Controllers;
use App\Models\OnboardingTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingTaskController extends Controller {
    public function index() {
        // Show tasks for the logged-in user's employee profile
        $employee = Auth::user()->employee;
        $tasks = $employee ? $employee->onboardingTasks : collect();
        return view('onboarding.index', compact('tasks'));
    }
}