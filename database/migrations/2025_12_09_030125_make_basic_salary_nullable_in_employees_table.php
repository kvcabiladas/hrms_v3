<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Modify the column to be nullable
            $table->decimal('basic_salary', 10, 2)->nullable()->change();
            // OR if you want it to have a default value of 0 instead of NULL:
            // $table->decimal('basic_salary', 10, 2)->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Revert changes if necessary (make it required again)
            $table->decimal('basic_salary', 10, 2)->nullable(false)->change();
        });
    }
};
