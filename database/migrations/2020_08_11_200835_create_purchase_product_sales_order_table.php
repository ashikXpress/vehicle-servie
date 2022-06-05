<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseProductSalesOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_product_sales_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('sales_order_id');
            $table->unsignedInteger('purchase_product_id');
            $table->string('name');
            $table->string('warranty');
            $table->string('serial');
            $table->float('quantity');
            $table->float('unit_price', 20);
            $table->float('total', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_product_sales_order');
    }
}
