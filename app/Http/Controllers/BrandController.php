<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
   public function brand()
   {
      $brands = Brand::orderBy('id', 'desc')->paginate(10);
      return view('pages.brands.brand', compact('brands'));
   }
   public function new_brand()
   {
      return view('pages.brands.new-brand');
   }
 
}
