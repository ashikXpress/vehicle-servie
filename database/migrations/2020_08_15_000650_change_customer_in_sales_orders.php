<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCustomerInSalesOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('customer_name');
            $table->dropColumn('phone');
            $table->unsignedInteger('customer_id')->after('order_no');
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
            $table->string('phone')->after('order_no');
            $table->string('customer_name')->after('order_no');
            $table->dropColumn('customer_id');
        });
    }
}
