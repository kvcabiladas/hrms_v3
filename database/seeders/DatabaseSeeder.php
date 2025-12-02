<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\JobListing;
use App\Models\Candidate;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Company Settings
        Company::create([
            'name' => 'TechNova Inc.',
            'email' => 'contact@technova.com',
            'website' => 'https://technova.com',
            'logo' => null,
        ]);

        // 2. Admin User (Login Credentials)
        // You will use this email/password to login to the dashboard
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@hrms.com',
            'password' => Hash::make('password'), // Password is "password"
        ]);

        // 3. Departments
        $tech = Department::create(['name' => 'Engineering', 'head_of_department' => 'Sarah Connor']);
        $hr = Department::create(['name' => 'Human Resources', 'head_of_department' => 'Toby Flenderson']);
        $design = Department::create(['name' => 'Design', 'head_of_department' => 'Don Draper']);
        $marketing = Department::create(['name' => 'Marketing', 'head_of_department' => 'Peggy Olson']);

        // 4. Designations
        $senDev = Designation::create(['name' => 'Senior Developer', 'department_id' => $tech->id]);
        $junDev = Designation::create(['name' => 'Junior Developer', 'department_id' => $tech->id]);
        $hrMgr = Designation::create(['name' => 'HR Manager', 'department_id' => $hr->id]);
        $uiDes = Designation::create(['name' => 'UI/UX Designer', 'department_id' => $design->id]);

        // 5. Employees
        // Employee 1: The Admin User (Linked to the User account created above)
        $emp1 = Employee::create([
            'user_id' => $user->id,
            'employee_id' => 'EMP-001',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@hrms.com',
            'phone' => '123-456-7890',
            'department_id' => $hr->id,
            'designation_id' => $hrMgr->id,
            'joining_date' => '2020-01-01',
            'basic_salary' => 90000,
            'status' => 'active',
            'gender' => 'male',
        ]);

        // Employee 2: Standard Employee
        $emp2 = Employee::create([
            'employee_id' => 'EMP-002',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@technova.com',
            'phone' => '987-654-3210',
            'department_id' => $tech->id,
            'designation_id' => $senDev->id,
            'joining_date' => '2023-03-15',
            'basic_salary' => 120000,
            'status' => 'active',
            'gender' => 'male',
        ]);

        // Employee 3: Probation Employee
        $emp3 = Employee::create([
            'employee_id' => 'EMP-003',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@technova.com',
            'department_id' => $design->id,
            'designation_id' => $uiDes->id,
            'joining_date' => '2024-06-01',
            'basic_salary' => 95000,
            'status' => 'probation',
            'gender' => 'female',
        ]);

        // 6. Attendance (Today's Logs)
        Attendance::create(['employee_id' => $emp1->id, 'date' => now(), 'clock_in' => '08:55:00', 'status' => 'present']);
        Attendance::create(['employee_id' => $emp2->id, 'date' => now(), 'clock_in' => '09:10:00', 'status' => 'present']);
        
        // 7. Leaves (Past & Future)
        Leave::create([
            'employee_id' => $emp2->id,
            'type' => 'Sick Leave',
            'start_date' => now()->subDays(5),
            'end_date' => now()->subDays(3),
            'reason' => 'Seasonal Flu',
            'status' => 'approved'
        ]);

        Leave::create([
            'employee_id' => $emp3->id,
            'type' => 'Casual Leave',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(3),
            'reason' => 'Family event',
            'status' => 'pending'
        ]);

        // 8. Payroll (Last Month Data)
        Payroll::create([
            'employee_id' => $emp2->id,
            'month_year' => now()->subMonth()->format('F Y'),
            'basic_salary' => 120000,
            'total_allowance' => 5000,
            'total_deduction' => 2000,
            'net_salary' => 123000,
            'status' => 'paid',
        ]);

        // 9. Recruitment (Job Listings & Candidates)
        $job1 = JobListing::create([
            'title' => 'Frontend React Developer',
            'department_id' => $tech->id,
            'location' => 'Remote',
            'type' => 'Full Time',
            'closing_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        Candidate::create([
            'job_listing_id' => $job1->id,
            'name' => 'Michael Scott',
            'email' => 'michael@paper.com',
            'phone' => '111-222-3333',
            'stage' => 'interview',
        ]);

        Candidate::create([
            'job_listing_id' => $job1->id,
            'name' => 'Dwight Schrute',
            'email' => 'dwight@farms.com',
            'phone' => '444-555-6666',
            'stage' => 'applied',
        ]);
    }
}