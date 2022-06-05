<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'owner_name', 'mobile', 'alternative_mobile', 'email',
        'address', 'status'
    ];

    public function getDueAttribute() {
        return PurchaseOrder::where('supplier_id', $this->id)->sum('due');
    }

    public function getPaidAttribute() {
        return PurchaseOrder::where('supplier_id', $this->id)->sum('paid');
    }

    public function getTotalAttribute() {
        return PurchaseOrder::where('supplier_id', $this->id)->sum('total');
    }

    public function getRefundAttribute() {
        return PurchaseOrder::where('supplier_id', $this->id)->sum('refund');
    }
}
