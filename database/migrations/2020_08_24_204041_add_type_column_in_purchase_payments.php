<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeColumnInPurchasePayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->tinyInteger('type')->default(1)->after('purchase_order_id')->comment('1=Pay; 2=Refund');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
