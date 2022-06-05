<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'order_no', 'supplier_id', 'warehouse_id', 'date', 'total', 'paid', 'due',
        'refund'
    ];

    protected $dates = ['date', 'next_payment'];

    public function products() {
        return $this->belongsToMany(PurchaseProduct::class)
            ->withPivot('id', 'name', 'type', 'serial_no', 'warranty', 'quantity',
                'unit_price', 'including_price', 'selling_price', 'total');
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function payments() {
        return $this->hasMany(PurchasePayment::class);
    }
}
