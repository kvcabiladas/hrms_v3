<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Payroll Rules table
        Schema::create('payroll_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name');
            $table->string('rule_type'); // 'percentage', 'fixed_amount', 'multiplier'
            $table->decimal('value', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Designation Payroll Templates table
        Schema::create('designation_payroll_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('designation_id')->constrained()->cascadeOnDelete();
            $table->decimal('base_allowance', 10, 2)->default(0);
            $table->decimal('overtime_multiplier', 5, 2)->default(1.5);
            $table->json('benefits')->nullable(); // Flexible JSON structure for various benefits
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designation_payroll_templates');
        Schema::dropIfExists('payroll_rules');
    }
};
