# Implementation Guide - Remaining Features

**Created:** 2025-12-08 20:38  
**For:** HRMS v3 Phase 2 Completion

---

## 🎯 CRITICAL USER-FACING FEATURES (Do First)

### 1. HR Dashboard Improvements ⭐ HIGH PRIORITY

**File:** `resources/views/dashboard_home.blade.php`

**Changes Needed:**

#### A. Add Date/Time Display (Top of page)
```blade
@section('content')
    <!-- Date/Time Display -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ now()->format('l, F j, Y • g:i A') }}
            </p>
        </div>
    </div>
```

#### B. Fix Card Layouts (Icon Left, Number Right)
**Current:** Icon on right, text on left  
**Change to:** Icon on left, number on right

```blade
<!-- Example for Total Employees card -->
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
    <div class="flex items-center gap-4">
        <!-- Icon LEFT -->
        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg flex-shrink-0">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <!-- Content RIGHT -->
        <div class="flex-1">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Employees</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1 text-right">{{ $totalEmployees }}</h3>
        </div>
    </div>
</div>
```

#### C. Change "Total Staff" to "Total Employees"
**Line 12:** Change label from "Total Staff" to "Total Employees"

#### D. Fix Pending Leave Requests UI
**Current:** Shows name, type, days, with Approve/Reject buttons  
**Change to:** Show name, leave type, "View" button only

```blade
@foreach($recentLeaves as $leave)
<div class="p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
    <div class="flex justify-between items-center">
        <div class="flex-1">
            <p class="text-sm font-bold text-gray-800">
                {{ $leave->employee->first_name }} {{ $leave->employee->last_name }}
            </p>
            <p class="text-xs text-gray-500 mt-1">{{ $leave->type }}</p>
        </div>
        <a href="{{ route('leaves.show', $leave->id) }}" 
           class="px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700 transition">
            View
        </a>
    </div>
</div>
@endforeach
```

#### E. Fix Recruitment Chart Logic
**File:** `app/Http/Controllers/DashboardController.php`

**Current Issue:** Uses `created_at` (account creation date)  
**Fix:** Use `joining_date` from employees table

```php
// In DashboardController index() method
// Find the recruitment chart data section and change:

// OLD (WRONG):
$employees = Employee::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
    ->whereYear('created_at', date('Y'))
    ->groupBy('month')
    ->get();

// NEW (CORRECT):
$employees = Employee::selectRaw('MONTH(joining_date) as month, COUNT(*) as count')
    ->whereYear('joining_date', date('Y'))
    ->groupBy('month')
    ->get();
```

---

### 2. Leave Management UI Improvements ⭐ HIGH PRIORITY

**File:** `resources/views/leaves/index.blade.php`

#### A. Leave History Tab - Remove Reason Column, Add View Action

**Find the Leave History table and update:**

```blade
<thead>
    <tr>
        <th>Employee</th>
        <th>Type</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Days</th>
        <th>Status</th>
        <!-- REMOVED: <th>Reason</th> -->
        <th>Action</th>
    </tr>
</thead>
<tbody>
    @foreach($leaves as $leave)
    <tr>
        <td>{{ $leave->employee->first_name }} {{ $leave->employee->last_name }}</td>
        <td>{{ $leave->type }}</td>
        <td>{{ $leave->start_date->format('M d, Y') }}</td>
        <td>{{ $leave->end_date->format('M d, Y') }}</td>
        <td>{{ $leave->start_date->diffInDays($leave->end_date) + 1 }}</td>
        <td><span class="badge">{{ $leave->status }}</span></td>
        <!-- Action column with View button -->
        <td>
            <button @click="viewLeave({{ $leave->id }})" 
                    class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                View
            </button>
        </td>
    </tr>
    @endforeach
</tbody>
```

#### B. Add View Leave Modal (Alpine.js)

**Add at bottom of leaves/index.blade.php:**

```blade
<!-- View Leave Modal -->
<div x-show="showViewModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <div class="relative bg-white rounded-lg max-w-2xl w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Leave Request Details</h3>
            
            <!-- Leave details here -->
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-bold text-gray-500">Employee</label>
                    <p class="text-sm text-gray-900" x-text="selectedLeave.employee_name"></p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500">Leave Type</label>
                    <p class="text-sm text-gray-900" x-text="selectedLeave.type"></p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500">Reason</label>
                    <p class="text-sm text-gray-900" x-text="selectedLeave.reason"></p>
                </div>
                <!-- Add more fields as needed -->
            </div>
            
            <!-- Approve/Reject buttons -->
            <div class="mt-6 flex gap-3">
                <button @click="approveLeave()" 
                        class="flex-1 bg-green-600 text-white py-2 rounded hover:bg-green-700">
                    Approve
                </button>
                <button @click="rejectLeave()" 
                        class="flex-1 bg-red-600 text-white py-2 rounded hover:bg-red-700">
                    Reject
                </button>
                <button @click="showViewModal = false" 
                        class="px-6 bg-gray-200 text-gray-700 py-2 rounded hover:bg-gray-300">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
```

#### C. Leave Recall Tab - Implement Figma Design

**Reference Image:** `/Users/kvcabiladas/.gemini/antigravity/brain/81169dbc-605e-4247-b8c6-cea805fef0a7/uploaded_image_1765194676848.png`

**Key Features from Figma:**
- Employee name display
- Department and leave type
- Start/End date fields
- Days remaining
- New resumption date selector
- Relief officer dropdown
- Green "Initiate Recall" button
- White "Cancel" button

```blade
<!-- Leave Recall Modal (based on Figma) -->
<div class="bg-white rounded-lg p-6 max-w-md">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Recall Leave</h3>
    
    <div class="space-y-4">
        <!-- Employee Info -->
        <div class="bg-gray-50 p-3 rounded">
            <p class="text-sm font-bold text-gray-900">John Doe</p>
            <p class="text-xs text-gray-500">HR Department • Sick Leave</p>
        </div>
        
        <!-- Leave Dates -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs font-bold text-gray-700">Start Date</label>
                <input type="date" class="w-full px-3 py-2 border rounded" disabled>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-700">End Date</label>
                <input type="date" class="w-full px-3 py-2 border rounded" disabled>
            </div>
        </div>
        
        <!-- Days Remaining -->
        <div>
            <label class="text-xs font-bold text-gray-700">Days Remaining</label>
            <input type="number" class="w-full px-3 py-2 border rounded bg-gray-50" disabled value="5">
        </div>
        
        <!-- New Resumption Date -->
        <div>
            <label class="text-xs font-bold text-gray-700">New Resumption Date*</label>
            <input type="date" name="new_resumption_date" class="w-full px-3 py-2 border border-gray-300 rounded focus:border-green-500">
        </div>
        
        <!-- Relief Officer -->
        <div>
            <label class="text-xs font-bold text-gray-700">Relief Officer*</label>
            <select name="relief_officer_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:border-green-500">
                <option value="">Select Relief Officer</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- Buttons -->
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
                Initiate Recall
            </button>
            <button type="button" @click="closeModal()" class="flex-1 bg-white border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 font-medium">
                Cancel
            </button>
        </div>
    </div>
</div>
```

#### D. Remove Relief Officers Tab

**In leaves/index.blade.php, find the tabs section and remove:**

```blade
<!-- REMOVE THIS TAB -->
<button @click="activeTab = 'relief'" 
        :class="activeTab === 'relief' ? 'active-tab-class' : 'inactive-tab-class'">
    Relief Officers
</button>

<!-- REMOVE THIS TAB CONTENT -->
<div x-show="activeTab === 'relief'">
    <!-- Relief officers content -->
</div>
```

---

### 3. Employee Management Tabs ⭐⭐ HIGHEST PRIORITY (BIGGEST FEATURE)

**File:** `resources/views/employees/index.blade.php`

**This is the LARGEST remaining feature - estimated 4-5 hours**

#### Overview
Create a tabbed interface with 4 tabs:
1. Employees List
2. Employee Attendance  
3. Departments
4. Jobs/Designations

#### Implementation Structure

```blade
@extends('layouts.hrms')

@section('content')
<div x-data="{ activeTab: 'employees' }">
    <!-- Tab Navigation -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex space-x-8">
            <button @click="activeTab = 'employees'" 
                    :class="activeTab === 'employees' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500'"
                    class="py-4 px-1 border-b-2 font-medium text-sm">
                Employees List
            </button>
            <button @click="activeTab = 'attendance'" 
                    :class="activeTab === 'attendance' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500'"
                    class="py-4 px-1 border-b-2 font-medium text-sm">
                Employee Attendance
            </button>
            <button @click="activeTab = 'departments'" 
                    :class="activeTab === 'departments' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500'"
                    class="py-4 px-1 border-b-2 font-medium text-sm">
                Departments
            </button>
            <button @click="activeTab = 'jobs'" 
                    :class="activeTab === 'jobs' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500'"
                    class="py-4 px-1 border-b-2 font-medium text-sm">
                Jobs
            </button>
        </nav>
    </div>

    <!-- Tab 1: Employees List -->
    <div x-show="activeTab === 'employees'">
        <!-- Search bar -->
        <div class="mb-4">
            <input type="text" 
                   placeholder="Search employees..." 
                   class="w-full px-4 py-2 border rounded-lg"
                   @input="searchEmployees($event.target.value)">
        </div>
        
        <!-- Employees table (remove status column) -->
        <table class="w-full">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Job</th>
                    <!-- REMOVED: Status column -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td>{{ $employee->employee_id }}</td>
                    <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->department->name ?? 'N/A' }}</td>
                    <td>{{ $employee->designation->name ?? 'N/A' }}</td>
                    <td>
                        <button @click="viewEmployee({{ $employee->id }})" class="text-blue-600">View</button>
                        <button @click="editEmployee({{ $employee->id }})" class="text-green-600">Edit</button>
                        <button @click="deleteEmployee({{ $employee->id }})" class="text-red-600">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Tab 2: Employee Attendance -->
    <div x-show="activeTab === 'attendance'">
        <!-- Copy content from hr/attendance.blade.php -->
        <!-- Change bar chart to line chart -->
        <!-- Add date range selector -->
        <!-- Default to last 7 days -->
    </div>

    <!-- Tab 3: Departments -->
    <div x-show="activeTab === 'departments'">
        <!-- Department CRUD -->
        <button @click="showAddDepartment = true" class="mb-4 bg-green-600 text-white px-4 py-2 rounded">
            Add Department
        </button>
        
        <table class="w-full">
            <thead>
                <tr>
                    <th>Department Name</th>
                    <th>Employees Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $dept)
                <tr>
                    <td>{{ $dept->name }}</td>
                    <td>{{ $dept->employees_count }}</td>
                    <td>
                        <button @click="editDepartment({{ $dept->id }})">Edit</button>
                        <button @click="deleteDepartment({{ $dept->id }})">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Tab 4: Jobs -->
    <div x-show="activeTab === 'jobs'">
        <!-- Jobs CRUD with department selection -->
        <button @click="showAddJob = true" class="mb-4 bg-green-600 text-white px-4 py-2 rounded">
            Add Job
        </button>
        
        <table class="w-full">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Department</th>
                    <th>Employees Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($designations as $job)
                <tr>
                    <td>{{ $job->name }}</td>
                    <td>{{ $job->department->name ?? 'N/A' }}</td>
                    <td>{{ $job->employees_count }}</td>
                    <td>
                        <button @click="editJob({{ $job->id }})">Edit</button>
                        <button @click="deleteJob({{ $job->id }})">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
```

#### Controllers Needed

**Create:** `app/Http/Controllers/DepartmentController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name'
        ]);
        
        Department::create($validated);
        
        return back()->with('success', 'Department created successfully!');
    }
    
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id
        ]);
        
        $department->update($validated);
        
        return back()->with('success', 'Department updated successfully!');
    }
    
    public function destroy(Department $department)
    {
        // Delete associated jobs
        $department->designations()->delete();
        
        // Set employees' department to null
        $department->employees()->update(['department_id' => null]);
        
        $department->delete();
        
        return back()->with('success', 'Department deleted successfully!');
    }
}
```

**Update:** `app/Http/Controllers/DesignationController.php` (if not exists, create it)

```php
<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id'
        ]);
        
        Designation::create($validated);
        
        return back()->with('success', 'Job created successfully!');
    }
    
    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id'
        ]);
        
        $designation->update($validated);
        
        return back()->with('success', 'Job updated successfully!');
    }
    
    public function destroy(Designation $designation)
    {
        // Set employees' designation to null
        $designation->employees()->update(['designation_id' => null]);
        
        $designation->delete();
        
        return back()->with('success', 'Job deleted successfully!');
    }
}
```

#### Routes to Add

**In `routes/web.php`:**

```php
// Department Management
Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

// Designation/Job Management
Route::post('/designations', [DesignationController::class, 'store'])->name('designations.store');
Route::put('/designations/{designation}', [DesignationController::class, 'update'])->name('designations.update');
Route::delete('/designations/{designation}', [DesignationController::class, 'destroy'])->name('designations.destroy');
```

#### Search Functionality

**Add to EmployeeController:**

```php
public function search(Request $request)
{
    $query = $request->get('q');
    
    $employees = Employee::where('first_name', 'like', "%{$query}%")
        ->orWhere('last_name', 'like', "%{$query}%")
        ->orWhere('email', 'like', "%{$query}%")
        ->orWhere('employee_id', 'like', "%{$query}%")
        ->with(['department', 'designation'])
        ->get();
    
    return response()->json($employees);
}
```

---

## 🔧 ADVANCED FEATURES (Do After Critical Features)

### 4. Notifications System

**Estimated Time:** 3-4 hours

#### Step 1: Create Migration

```bash
php artisan make:migration create_notifications_table
```

```php
Schema::create('notifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('type'); // leave_approved, leave_rejected, leave_recalled, payroll_posted, relief_officer
    $table->string('title');
    $table->text('message');
    $table->json('data')->nullable(); // Additional data
    $table->boolean('read')->default(false);
    $table->timestamps();
});
```

#### Step 2: Create Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'data' => 'array',
        'read' => 'boolean'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

#### Step 3: Create Helper Function

**In `app/Helpers/NotificationHelper.php`:**

```php
<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    public static function create($userId, $type, $title, $message, $data = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }
    
    public static function leaveApproved($leave)
    {
        return self::create(
            $leave->employee->user_id,
            'leave_approved',
            'Leave Approved',
            "Your {$leave->type} from {$leave->start_date->format('M d')} to {$leave->end_date->format('M d')} has been approved."
        );
    }
    
    public static function leaveRejected($leave)
    {
        return self::create(
            $leave->employee->user_id,
            'leave_rejected',
            'Leave Rejected',
            "Your {$leave->type} request has been rejected."
        );
    }
    
    public static function leaveRecalled($leave)
    {
        return self::create(
            $leave->employee->user_id,
            'leave_recalled',
            'Leave Recalled',
            "Your leave has been recalled. New resumption date: {$leave->new_resumption_date}"
        );
    }
    
    public static function payrollPosted($payroll)
    {
        return self::create(
            $payroll->employee->user_id,
            'payroll_posted',
            'Payroll Posted',
            "Your payroll for {$payroll->month_year} is now available."
        );
    }
}
```

#### Step 4: Add Notification Bell to Layout

**In `resources/views/layouts/hrms.blade.php` header:**

```blade
<!-- Notification Bell -->
<div class="relative" x-data="{ showNotifications: false }">
    <button @click="showNotifications = !showNotifications" class="relative p-2 text-gray-600 hover:text-gray-900">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        @if(auth()->user()->unreadNotifications()->count() > 0)
            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
        @endif
    </button>
    
    <!-- Dropdown -->
    <div x-show="showNotifications" 
         @click.away="showNotifications = false"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
         style="display: none;">
        <div class="p-4 border-b">
            <h3 class="font-bold text-gray-900">Notifications</h3>
        </div>
        <div class="max-h-96 overflow-y-auto">
            @forelse(auth()->user()->notifications()->latest()->limit(10)->get() as $notification)
                <div class="p-4 border-b hover:bg-gray-50 {{ $notification->read ? 'bg-white' : 'bg-blue-50' }}">
                    <p class="text-sm font-semibold text-gray-900">{{ $notification->title }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ $notification->message }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <div class="p-8 text-center text-gray-400">
                    <p class="text-sm">No notifications</p>
                </div>
            @endforelse
        </div>
        <div class="p-3 border-t text-center">
            <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
        </div>
    </div>
</div>
```

#### Step 5: Trigger Notifications

**In LeaveController update method:**

```php
use App\Helpers\NotificationHelper;

public function update(Request $request, Leave $leave)
{
    $leave->update(['status' => $request->status]);
    
    // Send notification
    if ($request->status === 'approved') {
        NotificationHelper::leaveApproved($leave);
    } elseif ($request->status === 'rejected') {
        NotificationHelper::leaveRejected($leave);
    }
    
    return back()->with('success', 'Leave updated successfully!');
}
```

---

### 5. PDF Payslip Generation

**Estimated Time:** 2 hours

#### Step 1: Install DomPDF

```bash
composer require barryvdh/laravel-dompdf
```

#### Step 2: Create PDF Template

**Create:** `resources/views/payroll/payslip-pdf.blade.php`

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { background: #10b981; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .total { font-weight: bold; font-size: 16px; background: #f3f4f6; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PAYSLIP</h1>
        <p>{{ $payroll->month_year }}</p>
    </div>
    
    <div class="content">
        <h3>Employee Information</h3>
        <p><strong>Name:</strong> {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</p>
        <p><strong>Employee ID:</strong> {{ $payroll->employee->employee_id }}</p>
        <p><strong>Department:</strong> {{ $payroll->employee->department->name ?? 'N/A' }}</p>
        <p><strong>Position:</strong> {{ $payroll->employee->designation->name ?? 'N/A' }}</p>
        
        <h3>Earnings</h3>
        <table>
            <tr>
                <td>Basic Salary</td>
                <td style="text-align: right;">₱{{ number_format($payroll->basic_salary, 2) }}</td>
            </tr>
            @if($payroll->allowances > 0)
            <tr>
                <td>Allowances</td>
                <td style="text-align: right;">₱{{ number_format($payroll->allowances, 2) }}</td>
            </tr>
            @endif
        </table>
        
        <h3>Deductions</h3>
        <table>
            <tr>
                <td>Total Deductions</td>
                <td style="text-align: right;">₱{{ number_format($payroll->total_deductions ?? 0, 2) }}</td>
            </tr>
        </table>
        
        <table>
            <tr class="total">
                <td>NET PAY</td>
                <td style="text-align: right;">₱{{ number_format($payroll->net_salary, 2) }}</td>
            </tr>
        </table>
        
        <p style="margin-top: 40px; text-align: center; color: #666; font-size: 10px;">
            This is a computer-generated payslip and does not require a signature.
        </p>
    </div>
</body>
</html>
```

#### Step 3: Update Controller

**In PayrollController:**

```php
use Barryvdh\DomPDF\Facade\Pdf;

public function downloadPayslip(Payroll $payroll)
{
    $user = Auth::user();
    
    if ($payroll->employee_id !== $user->employee->id) {
        abort(403, 'Unauthorized access.');
    }
    
    $pdf = Pdf::loadView('payroll.payslip-pdf', compact('payroll'));
    
    return $pdf->download('payslip_' . $payroll->month_year . '.pdf');
}
```

---

### 6. Chart.js Local Installation

**Estimated Time:** 1 hour

#### Step 1: Download Chart.js

```bash
# Download Chart.js from https://github.com/chartjs/Chart.js/releases
# Or use npm
npm install chart.js
```

#### Step 2: Copy to Public Directory

```bash
cp node_modules/chart.js/dist/chart.min.js public/js/chart.min.js
```

#### Step 3: Update All Views Using Charts

**Find and replace in all blade files:**

```blade
<!-- OLD (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- NEW (Local) -->
<script src="{{ asset('js/chart.min.js') }}"></script>
```

**Files to update:**
- `resources/views/hr/attendance.blade.php`
- `resources/views/dashboard_home.blade.php`
- Any other files using Chart.js

---

### 7. Export Functionality (CSV/Excel)

**Estimated Time:** 2 hours

#### Step 1: Install Package

```bash
composer require maatwebsite/excel
```

#### Step 2: Create Export Classes

**Create:** `app/Exports/EmployeesExport.php`

```php
<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Employee::select('employee_id', 'first_name', 'last_name', 'email', 'phone', 'department_id', 'designation_id')->get();
    }
    
    public function headings(): array
    {
        return ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Department', 'Designation'];
    }
}
```

#### Step 3: Add Export Routes

```php
Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
```

#### Step 4: Add Export Method to Controller

```php
use App\Exports\EmployeesExport;
use Maatwebsite\Excel\Facades\Excel;

public function export()
{
    return Excel::download(new EmployeesExport, 'employees.xlsx');
}
```

#### Step 5: Add Export Button to Views

```blade
<a href="{{ route('employees.export') }}" 
   class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    Export to Excel
</a>
```

---

## 📝 SUMMARY CHECKLIST

### Critical User-Facing (Do First)
- [ ] HR Dashboard: Add date/time
- [ ] HR Dashboard: Fix card layouts
- [ ] HR Dashboard: Change "Total Staff" to "Total Employees"
- [ ] HR Dashboard: Fix pending leaves UI
- [ ] HR Dashboard: Fix recruitment chart logic
- [ ] Leave Management: Remove reason column
- [ ] Leave Management: Add view modal
- [ ] Leave Management: Implement Figma recall UI
- [ ] Leave Management: Remove relief officers tab
- [ ] Employee Management: Create 4-tab interface
- [ ] Employee Management: Remove status column
- [ ] Employee Management: Add search functionality
- [ ] Employee Management: Department CRUD
- [ ] Employee Management: Jobs CRUD

### Advanced Features (Do After)
- [ ] Notifications: Database + Model
- [ ] Notifications: Bell icon + dropdown
- [ ] Notifications: Trigger on events
- [ ] PDF: Install DomPDF
- [ ] PDF: Create template
- [ ] PDF: Update controller
- [ ] Charts: Download Chart.js
- [ ] Charts: Update all views
- [ ] Export: Install package
- [ ] Export: Create export classes
- [ ] Export: Add buttons

---

**Total Estimated Time:** 12-16 hours

**Priority Order:**
1. HR Dashboard (2 hours)
2. Leave Management (1 hour)
3. Employee Management (4-5 hours)
4. Notifications (3 hours)
5. PDF Generation (2 hours)
6. Charts Local (1 hour)
7. Export Functionality (2 hours)

---

**Good luck! This guide should help you complete all remaining features systematically.** 🚀
