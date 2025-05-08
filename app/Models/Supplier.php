<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];
    
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    use HasFactory;
}
