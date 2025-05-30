<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductActivity;
use App\Models\ProductActivityItems;
use App\Models\Setting;
use App\Models\Supplier;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class IntakeController extends Controller
{
    public function index()
    {
        $products = Product::with(['brand', 'category'])->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $brands = Brand::all()->keyBy('id');
        $categories = Category::all()->keyBy('id');
        $settings = Setting::find(1);


        return view('pages.intake.intake', compact('products', 'suppliers', 'brands', 'categories', 'settings'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:intake,intake_loan,intake_return',
            'return_reason' => 'nullable|string',
            'loan_direction' => 'nullable|in:given,taken',
            'loan_due_to' => 'nullable|date',
            'loan_amount' => 'nullable|numeric',
            'total_price' => 'numeric',
            'brand_id' => 'nullable|exists:brands,id',
            'payment_type' => 'nullable|in:cash,card,bank_transfer',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|numeric|min:0.01',
            'products.*.price' => 'nullable|numeric|min:0',
        ]);

        $activity = ProductActivity::create([
            'type' => $request->type,
            'loan_direction' => $request->loan_direction,
            'supplier_id' => $request->supplier_id ?? null,
            'payment_type' => $request->payment_type,
            'note' => $request->note,
            'brand_id' => $request->brand_id ?? null,
            'total_price' => $request->total_price ?? 0,
            'loan_due_to' => $request->loan_due_to,
            'loan_amount' => $request->loan_amount,
            'return_reason' => $request->return_reason,
            'status' => $request->type === 'intake_loan' ? 'incomplete' : 'complete',

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

            if (in_array($activity->type, ['intake', 'intake_loan'])) {
                $product->qty += $adjustedQty;
            } elseif ($activity->type === 'intake_return') {
                $product->qty -= $adjustedQty;
            }

            $product->save();
        }

        return back()->with('success', 'Saved successfully with QR code');
    }
}
