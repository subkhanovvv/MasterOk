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
        return view('pages.products.new-product', compact('brands', 'categories'));
    }

    public function store_product(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'photo' => 'nullable',
            'unit' => 'required|string|max:50',
            'price_uzs' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0',
            'short_description' => 'nullable|string|max:1000',
            'sale_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ]);

        if ($validated['qty'] == 0) {
            $validated['status'] = 'out_of_stock';
        } elseif ($validated['qty'] <= 10) {
            $validated['status'] = 'low';
        } else {
            $validated['status'] = 'normal';
        }

        if ($request->hasFile('photo')) {
            $fileName = time() . '_' . $request->file('photo')->getClientOriginalName();
            $path = $request->file('photo')->storeAs('products', $fileName, 'public');
            $validated['photo'] = $path;
        }

        $validated['short_description'] = $validated['short_description'] ?? null;
        $validated['sale_price'] = $validated['sale_price'] ?? null;

        Product::create($validated);

        return redirect()->route('product')->with('success', 'Product created successfully.');
    }
}
