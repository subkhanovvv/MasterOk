<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
   public function brand()
   {
      $brands = Brand::orderBy('id', 'desc')->paginate(5);
      return view('pages.brand.brand', compact('brand'));
   }
   public function new_brand()
   {
      return view('pages.brands.new-brand');
   }
   public function edit_brand($id)
   {
      $brand = Brand::find($id);
      return view('admin.brand.edit-brand', compact('brand'));
   }
}
