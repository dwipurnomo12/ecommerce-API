<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public function posted_by()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function product_galleries()
    {
        return $this->hasMany(ProductGallery::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
