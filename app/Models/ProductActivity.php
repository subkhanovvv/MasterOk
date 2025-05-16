<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductActivity extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function products()
    {
        return $this->belongsTo(Product::class);
    }
    public function items()
    {
        return $this->hasMany(ProductActivityItems::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
