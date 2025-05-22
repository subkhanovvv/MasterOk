<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductActivity;
use App\Models\ProductActivityItems;
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
        $products = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        return view('pages.intake.intake', compact('products', 'suppliers'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:consume,loan,return,intake,intake_loan,intake_return',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|numeric|min:0.01',
            'products.*.unit' => 'required|string',
            'products.*.price' => 'nullable|numeric|min:0',
        ]);

        // Step 1: Create activity (initially without QR)
        $activity = ProductActivity::create([
            'type' => $request->type,
            'supplier_id' => $request->supplier_id,
            'note' => $request->note,
            'status' => 'incomplete',
        ]);

        // Step 2: Prepare product lines for QR content
        $productLines = '';
        foreach ($request->products as $item) {
            $productLines .= "- Product ID: {$item['product_id']} | Qty: {$item['qty']} | Unit: {$item['unit']} | Price: " . ($item['price'] ?? '0') . "\n";
        }

        // Step 3: Compose QR code content as readable text
        $qrText = "Activity ID: {$activity->id}\n";
        $qrText .= "Type: {$activity->type}\n";
        $qrText .= "Supplier ID: " . ($request->supplier_id ?? 'N/A') . "\n";
        $qrText .= "Status: incomplete\n";
        $qrText .= "Note: " . ($request->note ?? '-') . "\n";
        $qrText .= "Products:\n" . $productLines;

        // Step 4: Generate and store QR code image
        $fileName = 'qrcodes/activity_' . $activity->id . '_' . Str::random(6) . '.png';
        $qrImage = QrCode::format('png')
            ->encoding('UTF-8') // fixes Unicode characters like Russian/Uzbek
            ->size(300)
            ->generate($qrText);

        Storage::disk('public')->put($fileName, $qrImage);

        // Step 5: Save QR code path
        $activity->qr_code = $fileName;
        $activity->save();

        // Step 6: Save product activity items & update stock
        foreach ($request->products as $item) {
            if (empty($item['product_id']) || $item['qty'] <= 0) continue;

            $product = Product::find($item['product_id']);
            if (!$product) continue;

            ProductActivityItems::create([
                'product_activity_id' => $activity->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'unit' => $item['unit'],
                'price' => $item['price'] ?? null,
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
