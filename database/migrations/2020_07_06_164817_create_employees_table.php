<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('employee_id')->unique();
            $table->date('dob')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('confirmation_date')->nullable();
            $table->date('retirement_date')->nullable();
            $table->tinyInteger('employee_category')->comment('1=Army; 2=Civilian');
            $table->unsignedInteger('department_id');
            $table->unsignedInteger('designation_id');
            $table->tinyInteger('employee_type')->comment('1=Permanent; 2=Temporary');
            $table->string('reporting_to')->nullable();
            $table->tinyInteger('gender')->comment('1=Male; 2=Female');
            $table->string('blood_group')->nullable();
            $table->string('disease')->nullable();
            $table->tinyInteger('marital_status')->comment('1=Single; 2=Married');
            $table->string('mobile_no');
            $table->string('wife_name')->nullable();
            $table->string('number_of_children')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('emergency_contact');
            $table->string('signature')->nullable();
            $table->string('photo')->nullable();
            $table->string('present_address')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('email')->nullable();
            $table->integer('religion')->comment('1=Muslim; 2=Hindu; 3=Christian; 4=Other');
            $table->string('cv')->nullable();
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
        Schema::dropIfExists('employees');
    }
}
