<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServicesColumnsInSalesOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->float('service_sub_total')->after('sub_total');
            $table->float('service_vat_percentage')->after('vat_percentage');
            $table->float('service_vat')->after('vat');
            $table->float('service_discount')->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('service_sub_total');
            $table->dropColumn('service_vat_percentage');
            $table->dropColumn('service_vat');
            $table->dropColumn('service_discount');
        });
    }
}
