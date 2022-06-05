<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchaseInventory extends Model
{
    protected $fillable = [
        'purchase_product_id', 'warehouse_id', 'quantity', 'serial_no', 'warranty',
        'selling_price', 'unit_price', 'including_price'
    ];

    public function product() {
        return $this->belongsTo(PurchaseProduct::class, 'purchase_product_id', 'id');
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }
}
