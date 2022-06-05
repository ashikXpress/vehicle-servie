<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'order_no', 'customer_id', 'warehouse_id', 'date', 'sub_total', 'vat_percentage',
        'vat', 'discount', 'total', 'paid', 'due', 'next_payment', 'received_by', 'created_by',
        'service_sub_total', 'service_vat_percentage', 'service_vat', 'service_discount',
        'refund'
    ];

    protected $dates = ['date', 'next_payment'];

    public function products() {
        return $this->belongsToMany(PurchaseProduct::class)
            ->withPivot('id', 'name', 'serial', 'warranty', 'quantity', 'unit_price', 'total');
    }

    public function payments() {
        return $this->hasMany(SalePayment::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function services() {
        return $this->hasMany(Service::class);
    }
}
