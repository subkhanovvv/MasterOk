<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];
    
    public function brand()
    {
        return $this->hasMany(Brand::class);
    }

    use HasFactory;
}
