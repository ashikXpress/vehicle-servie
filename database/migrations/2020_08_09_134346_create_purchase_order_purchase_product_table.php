<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderPurchaseProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_purchase_product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('purchase_order_id');
            $table->unsignedInteger('purchase_product_id');
            $table->string('name');
            $table->tinyInteger('type')->comment('1=Single; 2=Multiple');
            $table->string('serial_no');
            $table->string('warranty');
            $table->float('quantity');
            $table->float('unit_price', 20);
            $table->float('including_price', 20);
            $table->float('selling_price', 20);
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
        Schema::dropIfExists('purchase_order_purchase_product');
    }
}
