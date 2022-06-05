<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name', 'mobile_no', 'address'
    ];

    public function getDueAttribute() {
        return SalesOrder::where('customer_id', $this->id)->sum('due');
    }

    public function getPaidAttribute() {
        return SalesOrder::where('customer_id', $this->id)->sum('paid');
    }

    public function getTotalAttribute() {
        return SalesOrder::where('customer_id', $this->id)->sum('total');
    }

    public function getRefundAttribute() {
        return SalesOrder::where('customer_id', $this->id)->sum('refund');
    }
}
