<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryChangeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_change_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('employee_id');
            $table->date('date');
            $table->float('basic_salary', 20);
            $table->float('house_rent', 20);
            $table->float('travel', 20);
            $table->float('medical', 20);
            $table->float('tax', 20);
            $table->float('others_deduct', 20);
            $table->float('gross_salary', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_change_logs');
    }
}
