<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Create 'leave_types' table (For settings)
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Sick Leave
            $table->integer('days_allowed'); // e.g., 15
            $table->timestamps();
        });

        // 2. Update 'leaves' table
        Schema::table('leaves', function (Blueprint $table) {
            // Drop the old string column if it exists, we will use a relationship
            if (Schema::hasColumn('leaves', 'type')) {
                $table->dropColumn('type');
            }
            
            // Add new columns
            $table->foreignId('leave_type_id')->nullable()->after('employee_id')->constrained();
            $table->foreignId('relief_officer_id')->nullable()->after('reason')->constrained('employees');
            $table->date('recalled_date')->nullable()->after('end_date'); // For Leave Recall
            $table->integer('days')->default(0)->after('end_date'); // To store duration
        });
    }

    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['leave_type_id']);
            $table->dropForeign(['relief_officer_id']);
            $table->dropColumn(['leave_type_id', 'relief_officer_id', 'recalled_date', 'days']);
            $table->string('type')->nullable(); // Restore old column
        });

        Schema::dropIfExists('leave_types');
    }
};