<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Company
        Company::firstOrCreate(['name' => 'TechNova Inc.', 'email' => 'contact@technova.com']);

        // 2. Departments (The specific list you requested)
        $hr = Department::create(['name' => 'HR']);
        $it = Department::create(['name' => 'IT']);
        $acct = Department::create(['name' => 'Accounting']);
        $mkt = Department::create(['name' => 'Marketing']);
        $sales = Department::create(['name' => 'Sales']);
        $ops = Department::create(['name' => 'Operations']);
        // Create Administration separately for the Super Admin logic
        $adminDept = Department::create(['name' => 'Administration']);

        // 3. Designations (Mapped to new departments)
        $superAdminPos = Designation::create(['name' => 'Super Admin', 'department_id' => $adminDept->id]);
        $hrMgr = Designation::create(['name' => 'HR Manager', 'department_id' => $hr->id]);
        $dev = Designation::create(['name' => 'Developer', 'department_id' => $it->id]);
        $accountant = Designation::create(['name' => 'Accountant', 'department_id' => $acct->id]);
        $marketer = Designation::create(['name' => 'Marketing Specialist', 'department_id' => $mkt->id]);
        $salesRep = Designation::create(['name' => 'Sales Representative', 'department_id' => $sales->id]);
        $opsMgr = Designation::create(['name' => 'Operations Manager', 'department_id' => $ops->id]);

        // 4. SUPER ADMIN USER
        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@hrms.com',
            'password' => Hash::make('password'), 
            'role' => 'super_admin',
        ]);

        Employee::create([
            'user_id' => $superAdminUser->id,
            'employee_id' => 'ADMIN-001',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@hrms.com',
            'department_id' => $adminDept->id,
            'designation_id' => $superAdminPos->id,
            'joining_date' => now(),
            'basic_salary' => 0,
            'status' => 'active',
            'gender' => 'other',
            'access_code' => '99999999',
        ]);
    }
}