<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public function product_galleries()
    {
        return $this->hasMany(ProductGallery::class);
    }
}