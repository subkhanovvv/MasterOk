<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;



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

        $barcodeValue = str_pad($product->category_id, 2, STR_PAD_LEFT) . str_pad($product->id, 5, '0', STR_PAD_LEFT);

        $barcodeDir = storage_path('app/public/barcodes');
        if (!file_exists($barcodeDir)) {
            mkdir($barcodeDir, 0755, true);
        }

        $dns1d = new DNS1D();
        $barcodeSVG = $dns1d->getBarcodeSVG($barcodeValue, 'C39', 1, 60);
        $barcodeImagePath = 'barcodes/' . $barcodeValue . '.svg';
        file_put_contents(storage_path('app/public/' . $barcodeImagePath), $barcodeSVG);

        $product->update([
            'barcode_value' => $barcodeValue,
            'barcode' => $barcodeImagePath,
        ]);

        // ✅ Telegram Notification
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

        return back()->with('success', 'Товар и штрихкод успешно сохранены!');
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

    public function barcode()
    {
        $barcodes = Product::orderBy('id', 'desc')->paginate(12);
        return view('pages.barcodes.barcode', compact('barcodes'));
    }
    public function history()
    {
        $brands = Brand::orderBy('id', 'desc')->get();
        $categories = Category::orderBy('id', 'desc')->get();
        $product_act = ProductActivity::orderBy('id', 'desc')->paginate(10);
        return view('pages.transactions.history', compact('product_act', 'brands', 'categories'));
    }
    public function verifyAjax(Request $request)
    {
        $text = $request->input('scanned_data');

        $lines = preg_split('/\r\n|\r|\n/', $text);
        $data = [];

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);
                $data[trim(strtolower(str_replace(' ', '_', $key)))] = trim($value);
            }
        }

        if (!isset($data['signature'])) {
            return response()->json(['success' => false, 'message' => 'Signature missing']);
        }

        try {
            $secret = env('QR_SECRET', 'default-key');
            $expectedSignature = hash('sha256', "{$data['transaction_id']}|{$data['product_id']}|{$data['qty']}|{$data['total_price']}|{$data['paid_amount']}|{$secret}");

            if ($expectedSignature !== $data['signature']) {
                return response()->json(['success' => false, 'message' => 'Invalid signature']);
            }

            return response()->json(['success' => true, 'message' => 'Valid transaction']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error processing']);
        }
    }
    public function scanTransaction(Request $request)
    {
        $validated = $request->validate([
            'scanned_data' => 'required|string',
        ]);

        $scannedData = $validated['scanned_data'];

        $product = Product::where('barcode', $scannedData)->first();

        if ($product) {
            // Log the response before returning it
            Log::info('Product found:', $product->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Product found.',
                'product' => $product
            ]);
        } else {
            // Log the error
            Log::info('Product not found:', ['scanned_data' => $scannedData]);

            return response()->json([
                'success' => false,
                'message' => 'Product not found or invalid QR code.'
            ]);
        }
    }
}
