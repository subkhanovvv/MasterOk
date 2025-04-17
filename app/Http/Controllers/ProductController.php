<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function product()
    {
        $products = Product::orderBy('id', 'desc')->paginate(10);
        return view('pages.products.product', compact('products'));
    }
    public function new_product()
    {
        $brands = Brand::orderBy('id', 'desc')->get();
        $categories = Category::orderBy('id', 'desc')->get();
        return view('pages.products.new-product',compact('brands' , 'categories'));
    }
    
    
}
