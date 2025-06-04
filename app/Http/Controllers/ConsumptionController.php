<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductActivity;
use App\Models\ProductActivityItems;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ConsumptionController extends Controller
{
    public function index()
    {
        $settings = Setting::find(1);

        $products = Product::orderBy('name')->get();
        $brands = Brand::all()->keyBy('id');
        $categories = Category::all()->keyBy('id');
        return view('pages.consumption.index', compact('products', 'brands', 'categories', 'settings'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:consume,loan,return',
            'return_reason' => 'nullable|string',
            'loan_direction' => 'nullable|in:given,taken',
            'loan_due_to' => 'nullable|date',
            'client_name' => 'nullable|string',
            'client_phone' => 'nullable|numeric',
            'loan_amount' => 'nullable|numeric',
            'payment_type' => 'nullable|in:cash,card,bank_transfer',
            'total_price' => 'numeric',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|numeric|min:0.01',
            'products.*.price' => 'nullable|numeric|min:0',
        ]);

        // Check product quantities before processing
        if (in_array($request->type, ['consume', 'loan'])) {
            foreach ($request->products as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) continue;

                $multiplier = $product->unit_per_stock ?? 1;
                $requiredQty = $item['qty'] * $multiplier;

                if ($product->qty < $requiredQty) {
                    return back()->withInput()->with('error', "Недостаточно товара на складе: {$product->name}. Доступно: {$product->qty}, Требуется: {$requiredQty}");
                }
            }
        }

        try {
            $activity = ProductActivity::create([
                'type' => $request->type,
                'loan_direction' => $request->loan_direction,
                'supplier_id' => $request->supplier_id ?? null,
                'note' => $request->note,
                'total_price' => $request->total_price ?? 0,
                'loan_due_to' => $request->loan_due_to,
                'client_name' => $request->client_name,
                'payment_type' => $request->payment_type,
                'client_phone' => $request->client_phone,
                'loan_amount' => $request->loan_amount,
                'return_reason' => $request->return_reason,
                'status' => $request->type === 'loan' ? 'incomplete' : 'complete',
            ]);

            $productLines = '';
            foreach ($request->products as $item) {
                $productLines .= "- Product ID: {$item['product_id']} | Qty: {$item['qty']} | Price: " . ($item['price'] ?? '0') . "\n";
            }

            $qrText = "Total price: {$activity->total_price}\n";

            $fileName = 'qrcodes/activity_' . $activity->id . '_' . Str::random(6) . '.png';
            $qrImage = QrCode::format('png')
                ->encoding('UTF-8')
                ->size(300)
                ->generate($qrText);

            Storage::disk('public')->put($fileName, $qrImage);

            $activity->qr_code = $fileName;
            $activity->save();

            foreach ($request->products as $item) {
                if (empty($item['product_id']) || $item['qty'] <= 0) continue;

                $product = Product::find($item['product_id']);
                if (!$product) continue;

                ProductActivityItems::create([
                    'product_activity_id' => $activity->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                ]);

                $multiplier = $product->unit_per_stock ?? 1;
                $adjustedQty = $item['qty'] * $multiplier;

                // Update product quantity based on activity type
                if ($activity->type === 'return') {
                    $product->increment('qty', $adjustedQty);
                } elseif (in_array($activity->type, ['consume', 'loan'])) {
                    $product->decrement('qty', $adjustedQty);
                }

                $product->save();
            }

            if ($request->print) {
                return redirect()->route('consumption.print', $activity->id);
            }

            return back()->with('success', 'Успешно сохранено с QR-кодом');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Произошла ошибка: ' . $e->getMessage());
        }
    }
    public function print(ProductActivity $activity)
    {
        $settings = Setting::find(1);

        return view('pages.consumption.partials.print', [
            'transaction' => $activity,
            'settings' => $settings
        ]);
    }
}
