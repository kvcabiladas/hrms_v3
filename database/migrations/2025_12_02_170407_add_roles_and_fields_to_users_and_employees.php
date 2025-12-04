<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Add Role & Username to Users
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('employee')->after('email'); // super_admin, hr, employee
            $table->string('username')->nullable()->unique()->after('name');
        });

        // 2. Add Address & Access Code to Employees
        Schema::table('employees', function (Blueprint $table) {
            $table->text('address')->nullable()->after('phone');
            $table->string('access_code', 8)->nullable()->unique()->after('status'); // Unique 8-digit code
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'username']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['address', 'access_code']);
        });
    }
};