<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. COMPANY SETTINGS
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->timestamps();
        });

        // 2. DEPARTMENTS
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('head_of_department')->nullable();
            $table->timestamps();
        });

        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        // 3. EMPLOYEES
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->foreignId('department_id')->constrained();
            $table->foreignId('designation_id')->constrained();
            $table->date('joining_date');
            $table->decimal('basic_salary', 10, 2);
            $table->enum('status', ['active', 'probation', 'terminated', 'resigned'])->default('active');
            $table->string('avatar')->nullable();
            $table->timestamps();
        });

        // 4. ATTENDANCE
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('clock_in');
            $table->time('clock_out')->nullable();
            $table->string('status')->default('present');
            $table->timestamps();
        });

        // 5. LEAVES
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });

        // 6. PAYROLL
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->string('month_year');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('total_allowance', 10, 2)->default(0);
            $table->decimal('total_deduction', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamps();
        });

        // 7. RECRUITMENT (Fixed: 'jobs' -> 'job_listings')
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('department_id')->constrained();
            $table->string('location');
            $table->string('type');
            $table->date('closing_date');
            $table->enum('status', ['active', 'closed', 'draft'])->default('active');
            $table->timestamps();
        });

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_listing_id')->constrained('job_listings')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('cv_path')->nullable();
            $table->string('stage')->default('applied');
            $table->timestamps();
        });

        // 8. DOCUMENTS (Added back)
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            // nullable because some docs are company policies, not specific to one employee
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('cascade'); 
            $table->string('title');
            $table->string('file_path');
            $table->string('category')->default('general'); // e.g., 'contract', 'id_proof', 'policy'
            $table->timestamps();
        });

        // 9. ONBOARDING TASKS (Added back)
        Schema::create('onboarding_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('task_name');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });

        // 10. ANNOUNCEMENTS / NEWS (Added back)
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            // Assuming the logged-in user (admin/HR) creates the announcement
            $table->foreignId('created_by')->constrained('users'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('onboarding_tasks');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('job_listings');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('companies');
    }
};