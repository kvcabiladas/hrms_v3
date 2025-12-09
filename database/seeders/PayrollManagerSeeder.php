<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class PayrollManagerSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create Accounting department
        $accountingDept = Department::firstOrCreate(['name' => 'Accounting']);

        // Get or create Payroll Manager designation
        $payrollDesignation = Designation::firstOrCreate(
            ['name' => 'Payroll Manager'],
            ['department_id' => $accountingDept->id]
        );

        // Create Payroll Manager User
        $payrollUser = User::firstOrCreate(
            ['email' => 'payroll@hrms.com'],
            [
                'name' => 'Payroll Manager',
                'username' => 'payrollmanager',
                'password' => Hash::make('password'),
                'role' => 'payroll_manager',
            ]
        );

        // Create Employee Profile for Payroll Manager
        Employee::firstOrCreate(
            ['email' => 'payroll@hrms.com'],
            [
                'user_id' => $payrollUser->id,
                'employee_id' => 'PAY-001',
                'first_name' => 'Payroll',
                'last_name' => 'Manager',
                'department_id' => $accountingDept->id,
                'designation_id' => $payrollDesignation->id,
                'joining_date' => now(),
                'hourly_rate' => 500.00,
                'status' => 'active',
                'gender' => 'other',
                'access_code' => '12345678',
            ]
        );

        $this->command->info('✅ Payroll Manager user created successfully!');
        $this->command->info('📧 Email: payroll@hrms.com');
        $this->command->info('🔑 Password: password');
    }
}
