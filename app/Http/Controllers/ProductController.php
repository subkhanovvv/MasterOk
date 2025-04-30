<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


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

        // ✅ Generate barcode
        $barcodeValue = str_pad($product->category_id, 2, '0', STR_PAD_LEFT) . str_pad($product->id, 5, '0', STR_PAD_LEFT);

        $barcodeDir = storage_path('app/public/barcodes');

        if (!file_exists($barcodeDir)) {
            mkdir($barcodeDir, 0755, true);
        }

        $dns1d = new DNS1D();
        $barcodeSVG = $dns1d->getBarcodeSVG($barcodeValue, 'C39', 1, 60);
        $barcodeImagePath = 'barcodes/' . $barcodeValue . '.svg';

        // ✅ Save the SVG directly (no base64 decoding)
        file_put_contents(storage_path('app/public/' . $barcodeImagePath), $barcodeSVG);

        // ✅ Save barcode info to DB
        Barcode::create([
            'barcode' => $barcodeValue,
            'product_id' => $product->id,
            'barcode_path' => $barcodeImagePath,
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
    public function consume(Request $req)
    {
        $validated = $req->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'type' => 'required|string|max:50',
            'total_price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'client_phone' => 'nullable|string|max:20',
            'return_reason' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($validated['product_id']);

            if ($validated['type'] === 'consume' && $product->qty < $validated['qty']) {
                return back()->withErrors(['qty' => 'Недостаточно товара на складе для расхода.']);
            }
            if (in_array($validated['type'], ['return', 'intake'])) {
                $product->increment('qty', $validated['qty']);
            } else {
                $product->decrement('qty', $validated['qty']);
            }
            $product->save();

            $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

            // Create Product Activity entry
            $productActivity = ProductActivity::create([
                'product_id' => $validated['product_id'],
                'qty' => $validated['qty'],
                'type' => $validated['type'],
                'total_price' => $validated['total_price'],
                'paid_amount' => $validated['paid_amount'],
                'client_phone' => $validated['client_phone'],
                'return_reason' => $validated['return_reason'],
            ]);

            // Generate the QR code content
            $qrContent = "Transaction ID: {$productActivity->id}\nProduct ID: {$productActivity->product_id}\nAction: {$productActivity->type}\nQty: {$productActivity->qty}\nTotal Price: {$productActivity->total_price}\nPaid Amount: {$productActivity->paid_amount}";

            // Generate QR code in SVG format
            $qrCodeSvg = QrCode::format('svg')->size(150)->generate($qrContent);

            // Save the SVG to the storage folder
            $qrCodePath = 'qrcodes/transaction_' . $productActivity->id . '.svg';
            Storage::disk('public')->put($qrCodePath, $qrCodeSvg);

            // Save the path of the QR code in the product activity
            $productActivity->qr_code = $qrCodePath;
            $productActivity->save();

            DB::commit();

            return back()->with('success', 'Операция успешно сохранена!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Произошла ошибка: ' . $e->getMessage()]);
        }
    }
    public function intake(Request $req)
    {
        $validated = $req->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'type' => 'required|string|max:50',
            'total_price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'return_reason' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($validated['product_id']);

            // Adjust product quantity based on type (intake or intake_loan)
            if (in_array($validated['type'], ['intake_loan', 'intake'])) {
                $product->increment('qty', $validated['qty']);
            } else {
                $product->decrement('qty', $validated['qty']);
            }
            $product->save();

            // Ensure 'paid_amount' is not null
            $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

            // Create Product Activity entry
            $productActivity = ProductActivity::create([
                'product_id' => $validated['product_id'],
                'qty' => $validated['qty'],
                'type' => $validated['type'],
                'total_price' => $validated['total_price'],
                'paid_amount' => $validated['paid_amount'],
                'return_reason' => $validated['return_reason'],
            ]);

            // Step 1: Generate the content for the QR code
            $qrContent = "Transaction ID: {$productActivity->id}\nProduct ID: {$productActivity->product_id}\nAction: {$productActivity->type}\nQty: {$productActivity->qty}\nTotal Price: {$productActivity->total_price}\nPaid Amount: {$productActivity->paid_amount}";

            // Step 2: Generate the signature
            $secret = env('QR_SECRET', 'default-secret');
            $signatureData = "{$productActivity->id}|{$productActivity->product_id}|{$productActivity->qty}|{$productActivity->total_price}|{$productActivity->paid_amount}|{$secret}";
            $signature = hash('sha256', $signatureData);

            // Step 3: Append the signature to the QR content
            $qrContent .= "\nSignature: {$signature}";

            // Step 4: Generate QR code in SVG format
            $qrCodeSvg = QrCode::format('svg')->size(150)->generate($qrContent);

            // Step 5: Save the QR code to the storage folder
            $qrCodePath = 'qrcodes/transaction_' . $productActivity->id . '.svg';
            Storage::disk('public')->put($qrCodePath, $qrCodeSvg);

            // Step 6: Save the QR code path in the product activity
            $productActivity->qr_code = $qrCodePath;
            $productActivity->save();

            DB::commit();

            return back()->with('success', 'Операция успешно сохранена!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Произошла ошибка: ' . $e->getMessage()]);
        }
    }


    public function barcode()
    {
        $barcodes = Barcode::orderBy('id', 'desc')->paginate(12);
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
