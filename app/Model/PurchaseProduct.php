<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PurchaseProduct extends Model
{
    protected $fillable = [
        'name', 'code', 'description', 'status'
    ];
}
