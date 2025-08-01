<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function get_brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }
    public function get_supplier()
    {
        return $this->hasOne(Supplier::class, 'id', 'brand_id');
    }
    public function get_category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
    public function barcode()
    {
        return $this->hasOne(Barcode::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    protected static function booted()
    {
        static::saving(function ($product) {
            if ($product->qty == 0) {
                $product->status = 'out_of_stock';
            } elseif ($product->qty < 10) {
                $product->status = 'low';
            } else {
                $product->status = 'normal';
            }
        });
    }
}
