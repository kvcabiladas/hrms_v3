<?php

use Illuminate\Support\Facades\Route;
// Import ALL Controllers
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\RecruitmentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\OnboardingTaskController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // =========================================================================
    // MAIN DASHBOARD ROUTER
    // =========================================================================
    Route::get('/dashboard', function () {
        $user = request()->user(); // FIXED: Get user from request helper

        // 1. Redirect Super Admin
        if ($user->role === 'super_admin') {
            return redirect()->route('superadmin.dashboard');
        }

        // 2. Normal Employee -> Employee Dashboard
        if ($user->role === 'employee') {
            // Ensure employee profile exists
            if (!$user->employee) {
                abort(403, 'No employee profile linked to this account.');
            }

            $empId = $user->employee->id;
            $attendanceToday = \App\Models\Attendance::where('employee_id', $empId)->whereDate('date', now())->first();

            // Calculate Attendance Stats (This Month)
            $daysPresent = \App\Models\Attendance::where('employee_id', $empId)
                ->whereMonth('date', now()->month)
                ->count();

            $pendingLeaves = \App\Models\Leave::where('employee_id', $empId)->where('status', 'pending')->count();

            // Get My Recent Payroll
            $lastPayroll = \App\Models\Payroll::where('employee_id', $empId)->latest()->first();

            return view('dashboard_employee', compact('attendanceToday', 'daysPresent', 'pendingLeaves', 'lastPayroll'));
        }

        // 3. HR Manager -> Standard HR Dashboard

        // --- FETCH STATS ---
        $today = now()->toDateString();
        $selectedYear = request('year', date('Y'));

        // Employee Counts - Count ALL users (employees, HR, accountants, super admins)
        $totalEmployees = \App\Models\User::count();

        // Attendance Stats
        $presentToday = \App\Models\Attendance::whereDate('date', $today)->count();
        $lateToday = \App\Models\Attendance::whereDate('date', $today)->where('status', 'late')->count();

        // Leave Stats
        $pendingLeavesCount = \App\Models\Leave::where('status', 'pending')->count();

        $onLeaveToday = \App\Models\Leave::where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        // Action Items
        $recentLeaves = \App\Models\Leave::with('employee')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Recruitment Growth Chart (Use joining_date, not created_at) - Support year parameter
        $employeesPerMonth = \App\Models\Employee::selectRaw('MONTH(joining_date) as month, COUNT(*) as count')
            ->whereYear('joining_date', $selectedYear)
            ->whereNotNull('joining_date')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $chartLabels = [];
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartLabels[] = date('F', mktime(0, 0, 0, $i, 1));
            $chartData[] = $employeesPerMonth[$i] ?? 0;
        }

        return view('dashboard_home', [
            'totalEmployees' => $totalEmployees,
            'presentToday' => $presentToday,
            'lateToday' => $lateToday,
            'onLeaveToday' => $onLeaveToday,
            'pendingLeavesCount' => $pendingLeavesCount,
            'pendingLeaves' => $pendingLeavesCount, // Alias
            'recentLeaves' => $recentLeaves,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
        ]);
    })->name('dashboard');


    // =========================================================================
    // CORE HR MODULES
    // =========================================================================

    // Employees
    Route::resource('employees', EmployeeController::class);

    // Attendance
    Route::resource('attendance', AttendanceController::class)->only(['index', 'store', 'update']);

    // LEAVE MANAGEMENT
    // 1. Specific Actions (Must be before resource)
    Route::put('/leaves/{leave}/cancel', [LeaveController::class, 'cancel'])->name('leaves.cancel');
    Route::get('/leaves/settings', [LeaveController::class, 'settings'])->name('leaves.settings');
    Route::post('/leaves/settings', [LeaveController::class, 'storeType'])->name('leaves.store_type');
    Route::put('/leaves/types/{type}', [LeaveController::class, 'updateType'])->name('leaves.update_type');
    Route::delete('/leaves/types/{type}', [LeaveController::class, 'destroyType'])->name('leaves.destroy_type');
    // 2. Resource
    Route::resource('leaves', LeaveController::class);


    // PAYROLL MANAGEMENT
    Route::get('/payroll/settings', [PayrollController::class, 'settings'])->name('payroll.settings');

    // Allowances
    Route::post('/payroll/allowances', [PayrollController::class, 'storeAllowance'])->name('payroll.allowances.store');
    Route::put('/payroll/allowances/{id}', [PayrollController::class, 'updateAllowance'])->name('payroll.allowances.update');
    Route::delete('/payroll/allowances/{id}', [PayrollController::class, 'destroyAllowance'])->name('payroll.allowances.destroy');

    // Deductions
    Route::post('/payroll/deductions', [PayrollController::class, 'storeDeduction'])->name('payroll.deductions.store');
    Route::put('/payroll/deductions/{id}', [PayrollController::class, 'updateDeduction'])->name('payroll.deductions.update');
    Route::delete('/payroll/deductions/{id}', [PayrollController::class, 'destroyDeduction'])->name('payroll.deductions.destroy');

    Route::resource('payroll', PayrollController::class);


    // DEPARTMENT MANAGEMENT
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

    // DESIGNATION/JOB MANAGEMENT
    Route::post('/designations', [DesignationController::class, 'store'])->name('designations.store');
    Route::put('/designations/{designation}', [DesignationController::class, 'update'])->name('designations.update');
    Route::delete('/designations/{designation}', [DesignationController::class, 'destroy'])->name('designations.destroy');

    // Recruitment
    Route::resource('recruitment', RecruitmentController::class);

    // =========================================================================
    // ADDITIONAL MODULES
    // =========================================================================
    Route::resource('documents', DocumentController::class);
    Route::resource('onboarding', OnboardingTaskController::class);
    Route::resource('announcements', AnnouncementController::class);

    // =========================================================================
    // PERSONAL ROUTES (For all authenticated users)
    // =========================================================================
    Route::prefix('personal')->name('personal.')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'personalAttendance'])->name('attendance');
        Route::get('/leaves', [LeaveController::class, 'personalLeaves'])->name('leaves');
        Route::get('/payroll', [PayrollController::class, 'personalPayroll'])->name('payroll');
        Route::get('/payroll/{payroll}/payslip', [PayrollController::class, 'viewPayslip'])->name('payslip');
        Route::get('/payroll/{payroll}/download', [PayrollController::class, 'downloadPayslip'])->name('payslip.download');
    });

    // =========================================================================
    // HR ROUTES (For HR personnel)
    // =========================================================================
    Route::prefix('hr')->name('hr.')->middleware('role:hr')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'hrAttendance'])->name('attendance');
    });

    // =========================================================================
    // SETTINGS MODULE
    // =========================================================================
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/update-company', [SettingsController::class, 'updateCompany'])->name('update_company');
        Route::post('/update-profile', [SettingsController::class, 'updateProfile'])->name('update_profile');
        Route::put('/update-password', [SettingsController::class, 'updatePassword'])->name('update_password');
        Route::post('/emergency-contacts', [SettingsController::class, 'updateEmergencyContacts'])->name('update_emergency_contacts');
        Route::post('/financial-details', [SettingsController::class, 'updateFinancialDetails'])->name('update_financial_details');
        Route::post('/profile-picture', [SettingsController::class, 'updateProfilePicture'])->name('update_profile_picture');
    });

    // =========================================================================
    // PAYROLL MANAGER ROUTES
    // =========================================================================
    Route::prefix('payroll-manager')->name('payroll-manager.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\PayrollManagerController::class, 'dashboard'])->name('dashboard');
        Route::get('/payroll-management', [App\Http\Controllers\PayrollManagerController::class, 'payrollManagement'])->name('payroll-management');
        Route::get('/employees', [App\Http\Controllers\PayrollManagerController::class, 'employees'])->name('employees');
        Route::get('/employees/{id}/payroll', [App\Http\Controllers\PayrollManagerController::class, 'employeePayroll'])->name('employee.payroll');
        Route::put('/employees/{id}/hourly-rate', [App\Http\Controllers\PayrollManagerController::class, 'updateHourlyRate'])->name('employee.update-hourly-rate');

        // Payroll Rules
        Route::get('/rules', [App\Http\Controllers\PayrollManagerController::class, 'rules'])->name('rules');
        Route::post('/rules', [App\Http\Controllers\PayrollManagerController::class, 'storeRule'])->name('rules.store');
        Route::put('/rules/{id}', [App\Http\Controllers\PayrollManagerController::class, 'updateRule'])->name('rules.update');
        Route::delete('/rules/{id}', [App\Http\Controllers\PayrollManagerController::class, 'destroyRule'])->name('rules.destroy');

        // Designation Templates
        Route::get('/templates', [App\Http\Controllers\PayrollManagerController::class, 'templates'])->name('templates');
        Route::post('/templates', [App\Http\Controllers\PayrollManagerController::class, 'storeTemplate'])->name('templates.store');
        Route::put('/templates/{id}', [App\Http\Controllers\PayrollManagerController::class, 'updateTemplate'])->name('templates.update');
        Route::delete('/templates/{id}', [App\Http\Controllers\PayrollManagerController::class, 'destroyTemplate'])->name('templates.destroy');

        // Run Payroll
        Route::post('/run-payroll', [App\Http\Controllers\PayrollManagerController::class, 'runPayroll'])->name('run-payroll');
    });

    // =========================================================================
    // SUPER ADMIN ROUTES
    // =========================================================================
    Route::prefix('super-admin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/create-hr', [SuperAdminController::class, 'createHr'])->name('createHr');
        Route::post('/store-hr', [SuperAdminController::class, 'storeHr'])->name('storeHr');
    });
});

require __DIR__ . '/auth.php';