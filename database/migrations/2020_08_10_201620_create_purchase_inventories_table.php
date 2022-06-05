<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_inventories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('purchase_product_id');
            $table->unsignedInteger('warehouse_id');
            $table->float('quantity', 20);
            $table->string('serial_no');
            $table->string('warranty');
            $table->float('unit_price');
            $table->float('including_price');
            $table->float('selling_price');
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
        Schema::dropIfExists('purchase_inventories');
    }
}
