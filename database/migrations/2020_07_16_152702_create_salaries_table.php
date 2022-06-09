<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('salary_process_id');
            $table->unsignedInteger('employee_id');
            $table->date('date');
            $table->unsignedInteger('month');
            $table->unsignedInteger('year');
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
        Schema::dropIfExists('salaries');
    }
}
