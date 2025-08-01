<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductActivityItems extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
{
    return $this->belongsTo(Product::class);
}
     public function productActivity()
    {
        return $this->belongsTo(ProductActivity::class);
    }


}
