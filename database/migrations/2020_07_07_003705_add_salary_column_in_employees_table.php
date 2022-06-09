<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalaryColumnInEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->float('gross_salary', 20)->default(0)->after('cv');
            $table->float('medical', 20)->default(0)->after('cv');
            $table->float('travel', 20)->default(0)->after('cv');
            $table->float('house_rent', 20)->default(0)->after('cv');
            $table->float('tax', 20)->default(0)->after('cv');
            $table->float('others_deduct', 20)->default(0)->after('cv');
            $table->float('basic_salary', 20)->default(0)->after('cv');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('gross_salary');
            $table->dropColumn('medical');
            $table->dropColumn('travel');
            $table->dropColumn('house_rent');
            $table->dropColumn('tax');
            $table->dropColumn('others_deduct');
            $table->dropColumn('basic_salary');
        });
    }
}
