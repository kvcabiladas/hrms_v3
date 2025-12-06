<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Transport"
            $table->enum('type', ['allowance', 'deduction']);
            $table->enum('value_type', ['fixed', 'percentage']); // Is it $100 or 10%?
            $table->decimal('value', 10, 2); // The amount or percent
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salary_components');
    }
};