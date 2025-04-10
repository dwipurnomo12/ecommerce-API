<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id'];

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}