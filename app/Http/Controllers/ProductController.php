<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function product()
    {
        $products = Product::orderBy('id', 'desc')->paginate(10);
        $brands = Brand::orderBy('id', 'desc')->get();
        $categories = Category::orderBy('id', 'desc')->get();
        return view('pages.products.product', compact('products', 'brands', 'categories'));
    }

    public function store_product(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image',  // Photo is now optional and should be an image if provided
            'unit' => 'required|string|max:50',
            'price_uzs' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'short_description' => 'nullable|string|max:1000',
            'sale_price' => 'required|numeric|min:0',
            'category_id' => 'required',
            'brand_id' => 'required',
        ]);
    
        $photoPath = null;
        
        // Check if a file is uploaded for the 'photo' field
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('products', 'public');
        }
    
        Product::create([
            'name'  => $validated['name'],
            'photo' => $photoPath,  // Save the photo path if a photo was uploaded
            'unit' => $validated['unit'],
            'price_uzs' => $validated['price_uzs'],
            'price_usd' => $validated['price_usd'],
            'tax' => $validated['tax'],
            'short_description' => $validated['short_description'],
            'sale_price' => $validated['sale_price'],
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
        ]);
        $message = "🛒 Новый продукт добавлен:\n\nНазвание: {$request->name}\n\nФото: {$request->file('photo')->getClientOriginalName()}\n\n
        Цена: {$request->price_uzs} UZS\n\nЦена: {$request->price_usd} USD\n\nНалог: {$request->tax}\n\nКраткое описание: {$request->short_description}\n\nСкидочная цена: {$request->sale_price}\n\nКатегория: {$request->category_id}\n\nБренд: {$request->brand_id}";
        $botToken = config('services.telegram.token');
        $chatIds = config('services.telegram.chat_ids');

        foreach ($chatIds as $chatId) {
            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => trim($chatId),
                'text' => $message
            ]);
        }
        return back()->with('success', 'Товар успешно сохранён!');
    }
    public function destroy_product($id)
{
    $product = Product::findOrFail($id);
    if ($product->photo) {
        Storage::disk('public')->delete($product->photo);
    }
    $product->delete();
    return response()->json([
        'message' => 'Товар успешно удален!',
        'id' => $id,
    ]);
    
}

}
