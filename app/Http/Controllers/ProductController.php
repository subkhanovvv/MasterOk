<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductActivity;
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
            'photo' => 'nullable|image',
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

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $validated['name'],
            'photo' => $photoPath,
            'unit' => $validated['unit'],
            'price_uzs' => $validated['price_uzs'],
            'price_usd' => $validated['price_usd'],
            'tax' => $validated['tax'],
            'short_description' => $validated['short_description'],
            'sale_price' => $validated['sale_price'],
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
        ]);

        $message = "🛒 Новый продукт добавлен:\n\n" .
            "📦 Название: {$product->name}\n" .
            "💰 Цена: {$product->price_uzs} UZS / {$product->price_usd} USD\n" .
            "📈 Налог: {$product->tax}%\n" .
            "📝 Описание: {$product->short_description}\n" .
            "🔥 Скидочная цена: {$product->sale_price}\n" .
            "📁 Категория: {$product->category_id}\n" .
            "🏷️ Бренд: {$product->brand_id}";

        $botToken = config('services.telegram.token');
        $chatIds = config('services.telegram.chat_ids');

        foreach ($chatIds as $chatId) {
            if ($photoPath) {
                Http::attach(
                    'photo',
                    file_get_contents(storage_path("app/public/{$photoPath}")),
                    basename($photoPath)
                )->post("https://api.telegram.org/bot{$botToken}/sendPhoto", [
                    'chat_id' => trim($chatId),
                    'caption' => $message,
                    'parse_mode' => 'HTML',
                ]);
            } else {
                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => trim($chatId),
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);
            }
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
    public function consume(Request $req)
    {
        ProductActivity::create([
            'product_id' => $req->product_id,
            'qty' => $req->qty,
            'type' => $req->type,
            'price' => $req->price,
            'date' => $req->date,
        ]);

        Product::find($req->product_id)->decrement('qty', $req->qty);

        return Product::all();
    }
}
