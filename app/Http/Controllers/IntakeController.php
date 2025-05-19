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
    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.01',
        ]);

        $productId = $request->product_id;
        $qty = $request->qty;

        $items = session()->get('intake_products', []);

        if (isset($items[$productId])) {
            $items[$productId]['qty'] += $qty;
        } else {
            $items[$productId] = [
                'product_id' => $productId,
                'qty' => $qty,
            ];
        }

        session(['intake_products' => $items]);
        return redirect()->back();
    }


    public function incrementItem($productId)
    {
        $items = session()->get('intake_products', []);
        if (isset($items[$productId])) {
            $items[$productId]['qty'] += 1;
        }
        session(['intake_products' => $items]);
        return redirect()->back();
    }

    public function decrementItem($productId)
    {
        $items = session()->get('intake_products', []);
        if (isset($items[$productId])) {
            $items[$productId]['qty'] -= 1;
            if ($items[$productId]['qty'] <= 0) {
                unset($items[$productId]);
            }
        }
        session(['intake_products' => $items]);
        return redirect()->back();
    }

    public function removeItem($productId)
    {
        $items = session()->get('intake_products', []);
        unset($items[$productId]);
        session(['intake_products' => $items]);
        return redirect()->back();
    }

    public function clearItems()
    {
        session()->forget('intake_products');
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'payment_type' => 'required',
        ]);

        $activity = ProductActivity::create([
            'type' => $request->type,
            'loan_direction' => $request->loan_direction,
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'loan_amount' => $request->loan_amount,
            'loan_due_to' => $request->loan_due_to,
            'return_reason' => $request->return_reason,
            'payment_type' => $request->payment_type,
            'paid_amount' => $request->paid_amount,
            'note' => $request->note,
        ]);

        foreach (session('intake_products', []) as $productId => $item) {
            ProductActivityItems::create([
                'product_activity_id' => $activity->id,
                'product_id' => $productId,
                'qty' => $item['qty'],
                'unit' => Product::find($productId)?->unit ?? 'шт',
            ]);
        }


        session()->forget('intake_products');
        return redirect()->route('intake.index')->with('success', 'Приход успешно сохранён.');
    }
}
