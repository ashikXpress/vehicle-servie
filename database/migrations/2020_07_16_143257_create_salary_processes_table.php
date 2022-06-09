<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_processes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->unsignedInteger('month');
            $table->unsignedInteger('year');
            $table->unsignedInteger('bank_id');
            $table->unsignedInteger('branch_id');
            $table->unsignedInteger('bank_account_id');
            $table->float('total', 20);
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
        Schema::dropIfExists('salary_processes');
    }
}
