<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'product_id',
        'retail_price',
        'reseller_price',
        'reseller_min_qty',
        'wholesale_price',
        'wholesale_min_qty',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}