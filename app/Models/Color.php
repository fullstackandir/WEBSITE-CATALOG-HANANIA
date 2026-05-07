<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = ['product_id', 'name', 'hex_code', 'stock', 'image_path'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getStatusAttribute(): string
    {
        return $this->stock > 0 ? 'Ready' : 'Habis';
    }

    public function getStatusClassAttribute(): string
    {
        return $this->stock > 0 ? 'bg-success' : 'bg-danger';
    }
}