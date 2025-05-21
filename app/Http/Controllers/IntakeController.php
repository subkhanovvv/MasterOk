<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductActivity;
use App\Models\ProductActivityItems;
use App\Models\Supplier;
use Illuminate\Support\Facades\Session;
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
        $validated = $request->validate([
            'type' => 'required|in:consume,loan,return,intake,intake_loan,intake_return',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|numeric|min:0.01',
            'products.*.unit' => 'required|string',
            'products.*.price' => 'nullable|numeric|min:0',
        ]);

        $activity = ProductActivity::create([
            'type' => $request->type,
            'supplier_id' => $request->supplier_id,
            'note' => $request->note,
            'status' => 'incomplete',
        ]);

        foreach ($request->products as $item) {
            if (empty($item['product_id']) || $item['qty'] <= 0) {
                continue; // Skip invalid product
            }

            $product = Product::find($item['product_id']);
            if (!$product) continue;

            // Save item
            ProductActivityItems::create([
                'product_activity_id' => $activity->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'unit' => $item['unit'],
                'price' => $item['price'] ?? null,
            ]);

            // Stock update logic
            $multiplier = $product->unit_per_stock ?? 1; // optional: handle conversion

            $adjustedQty = $item['qty'] * $multiplier;

            if (in_array($activity->type, ['intake', 'intake_loan'])) {
                $product->qty += $adjustedQty;
            } elseif ($activity->type === 'intake_return') {
                $product->qty -= $adjustedQty;
            }

            $product->save();
        }

        return back()->with('success', 'Saved successfully');
    }
}
