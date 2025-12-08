<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all users with 'accountant' role to 'payroll_manager'
        DB::table('users')
            ->where('role', 'accountant')
            ->update(['role' => 'payroll_manager']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to 'accountant' if needed
        DB::table('users')
            ->where('role', 'payroll_manager')
            ->update(['role' => 'accountant']);
    }
};
