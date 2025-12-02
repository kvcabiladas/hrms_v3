<?php

use Illuminate\Support\Facades\Route;
// 1. Import ALL Controllers here
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\OnboardingTaskController;
use App\Http\Controllers\AnnouncementController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard_home', [
            'totalEmployees' => \App\Models\Employee::count(),
            'presentToday' => \App\Models\Attendance::whereDate('date', now())->count(),
            'pendingLeaves' => \App\Models\Leave::where('status', 'pending')->count(),
        ]);
    })->name('dashboard');

    // Core Modules
    Route::resource('employees', EmployeeController::class);
    Route::resource('attendance', AttendanceController::class)->only(['index', 'store', 'update']);
    Route::resource('leaves', LeaveController::class);
    Route::resource('payroll', PayrollController::class);
    Route::resource('recruitment', RecruitmentController::class);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Additional Modules
    Route::resource('documents', DocumentController::class);
    Route::resource('onboarding', OnboardingTaskController::class);
    Route::resource('announcements', AnnouncementController::class);
});

require __DIR__.'/auth.php';