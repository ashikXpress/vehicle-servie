<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchaseInventoryLog extends Model
{
    protected $fillable = [
        'purchase_product_id', 'type', 'date', 'warehouse_id', 'quantity', 'unit_price',
        'supplier_id', 'sales_order_id', 'note'
    ];

    protected $dates = ['date'];

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function product() {
        return $this->belongsTo(PurchaseProduct::class, 'purchase_product_id', 'id');
    }

    public function order() {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'id');
    }
}
