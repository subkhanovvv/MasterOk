<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductActivity;
use App\Models\ProductActivityItems;
use App\Models\Supplier;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'note' => 'nullable|string',
        ]);

        $items = session()->get('intake_items', []);
        if (count($items) == 0) {
            return back()->with('error', 'No products to intake.');
        }

        $total = collect($items)->sum(fn($item) => $item['qty'] * $item['price']);

        $activity = ProductActivity::create([
            'type' => 'intake',
            'supplier_id' => $validated['supplier_id'],
            'note' => $validated['note'],
            'total_price' => $total,
        ]);

        foreach ($items as $item) {
            ProductActivityItems::create([
                'product_activity_id' => $activity->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'unit' => $item['unit'],
                'price' => $item['price'],
            ]);

            // Optional: update product quantity
            $product = Product::find($item['product_id']);
            $product->qty += $item['qty'];
            $product->save();
        }

        session()->forget('intake_items');

        return redirect()->route('intake.index')->with('success', 'Intake saved!');
    }
}
