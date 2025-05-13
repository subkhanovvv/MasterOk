<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductActivity;
use App\Models\ProductActivityItems;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntakeController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        $items = session()->get('intake_items', []);

        return view('pages.intake.intake', compact('products', 'suppliers', 'items'));
    }
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.01',
            'unit' => 'required|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        $product = Product::find($validated['product_id']);
        $validated['product_name'] = $product->name;

        $items = session()->get('intake_items', []);
        $items[] = $validated;
        session()->put('intake_items', $items);

        return redirect()->route('intake.index');
    }

    public function update($key, Request $request)
    {
        $items = session()->get('intake_items', []);
        if ($request->input('action') === 'increase') {
            $items[$key]['qty'] += 1;
        } elseif ($request->input('action') === 'decrease' && $items[$key]['qty'] > 1) {
            $items[$key]['qty'] -= 1;
        }
        session()->put('intake_items', $items);
        return back();
    }

    public function remove($key)
    {
        $items = session()->get('intake_items', []);
        unset($items[$key]);
        session()->put('intake_items', array_values($items));
        return back();
    }



    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'payment_type' => 'required|in:cash,card',
            'paid_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['qty'] * $item['price'];
            }

            // Save main intake activity
            $activity = ProductActivity::create([
                'type' => 'intake',
                'supplier_id' => $request->supplier_id,
                'payment_type' => $request->payment_type,
                'paid_amount' => $request->paid_amount ?? 0,
                'total_price' => $total,
                'note' => $request->note,
            ]);

            // Save intake items and update product stock
            foreach ($request->items as $item) {
                ProductActivityItems::create([
                    'product_activity_id' => $activity->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'unit' => $item['unit'],
                    'price' => $item['price'],
                ]);

                // Update product quantity (assumes unit is same as base)
                $product = Product::find($item['product_id']);
                $product->qty += $item['qty'];
                $product->status = 'normal'; // Intake means it's in stock now
                $product->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Product intake successfully recorded.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to save product intake. ' . $e->getMessage());
        }
    }
}
