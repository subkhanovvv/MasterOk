<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductActivity;
use App\Models\ProductActivityItems;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $validated=$request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_type' => 'required|in:cash,card',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|numeric|min:0.01',
            'products.*.unit' => 'required|string',
            'products.*.price_uzs' => 'required|numeric|min:0',
            'products.*.price_usd' => 'required|numeric|min:0',
        ]);


        DB::beginTransaction();

        try {
            $totalPrice = 0;
            foreach ($validated['products'] as $productData) {
                $totalPrice += $productData['price_uzs'] * $productData['qty'];
            }

            $activity = ProductActivity::create([
                'type' => 'intake',
                'payment_type' => $validated['payment_type'],
                'total_price' => $totalPrice,
                'note' => $validated['note'],
                'supplier_id' => $validated['supplier_id'] ?? null,
            ]);

            foreach ($validated['products'] as $productData) {
                $product = Product::findOrFail($productData['id']);

                ProductActivityItems::create([
                    'product_activity_id' => $activity->id,
                    'product_id' => $product->id,
                    'qty' => $productData['qty'],
                    'unit' => $productData['unit'],
                    'price' => $productData['price_uzs'],
                    'price_usd' => $productData['price_usd'],
                ]);

                $product->increment('qty', $productData['qty']);
            }

            DB::commit();

            return redirect()
                ->route('intake.index')
                ->with('success', 'Product intake recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
