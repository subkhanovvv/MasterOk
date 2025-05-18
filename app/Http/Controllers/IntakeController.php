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
    public function addProduct(Request $request)
    {
        $request->validate([
            'action' => 'required|in:add_barcode,add_manual'
        ]);

        $products = Session::get('products', []);

        if ($request->action === 'add_barcode' && $request->filled('barcode')) {
            $product = Product::where('barcode', $request->barcode)->first();

            if (!$product) {
                return back()->with('error', 'Product not found with this barcode');
            }

            $products[] = [
                'id' => $product->id,
                'name' => $product->name,
                'qty' => 1, // Default quantity
                'unit' => $product->unit,
                'price_uzs' => $product->price_uzs
            ];
        } elseif ($request->action === 'add_manual' && $request->filled('product_name')) {
            $products[] = [
                'id' => null, // Will be looked up or created on final submission
                'name' => $request->product_name,
                'qty' => $request->product_qty ?? 1,
                'unit' => 'шт', // Default unit
                'price_uzs' => 0 // Will be set on final submission
            ];
        } else {
            return back()->with('error', 'Please provide product information');
        }

        Session::put('products', $products);
        return back();
    }

    // Remove product from session
    public function removeProduct($index)
    {
        $products = Session::get('products', []);

        if (isset($products[$index])) {
            unset($products[$index]);
            Session::put('products', array_values($products)); // Reindex array
        }

        return back();
    }

    // Store the final intake
    public function storeIntake(Request $request)
    {
        $request->validate([
            'type' => 'required|in:intake,intake_loan,intake_return',
            'payment_type' => 'required|in:cash,card,bank_transfer',
            'paid_amount' => 'required|numeric|min:0'
        ]);

        $products = Session::get('products', []);

        if (empty($products)) {
            return back()->with('error', 'Please add at least one product');
        }

        DB::transaction(function () use ($request, $products) {
            // Calculate total price
            $totalPrice = collect($products)->sum(function ($product) {
                return $product['qty'] * $product['price_uzs'];
            });

            // Create the product activity
            $activity = ProductActivity::create([
                'type' => $request->type,
                'loan_direction' => $request->type === 'intake_loan' ? $request->loan_direction : null,
                'client_name' => $request->client_name,
                'client_phone' => $request->client_phone,
                'status' => 'incomplete',
                'loan_amount' => $request->loan_amount ?? 0,
                'loan_due_to' => $request->type === 'intake_loan'
                    ? $totalPrice - ($request->loan_amount ?? 0)
                    : null,
                'payment_type' => $request->payment_type,
                'paid_amount' => $request->paid_amount,
                'total_price' => $totalPrice,
                'return_reason' => $request->type === 'intake_return' ? $request->return_reason : null,
                'note' => $request->note,
                'supplier_id' => $request->supplier_id
            ]);

            // Create activity items and update product quantities
            foreach ($products as $productData) {
                $product = $this->getOrCreateProduct($productData);

                ProductActivityItems::create([
                    'product_activity_id' => $activity->id,
                    'product_id' => $product->id,
                    'qty' => $productData['qty'],
                    'unit' => $product->unit,
                    'price' => $product->price_uzs
                ]);

                // Update product quantity based on intake type
                $this->updateProductQuantity($product, $productData['qty'], $request->type);
            }
        });

        Session::forget('products');
        return redirect()->route('product-activities.intake.create')
            ->with('success', 'Product intake recorded successfully');
    }

    protected function getOrCreateProduct(array $productData)
    {
        if ($productData['id']) {
            return Product::find($productData['id']);
        }

        // Create new product if not found by barcode
        return Product::create([
            'name' => $productData['name'],
            'qty' => 0,
            'unit' => $productData['unit'],
            'price_uzs' => $productData['price_uzs'] ?? 0,
            'price_usd' => 0,
            'category_id' => 1, // Default category
            'brand_id' => 1, // Default brand
            'status' => 'normal'
        ]);
    }

    protected function updateProductQuantity(Product $product, $qty, $type)
    {
        switch ($type) {
            case 'intake_return':
                $product->decrement('qty', $qty);
                break;
            case 'intake':
            case 'intake_loan':
                $product->increment('qty', $qty);
                break;
        }

        // Update status based on new quantity
        $newStatus = $product->qty <= 0 ? 'out_of_stock' : ($product->qty < 10 ? 'low' : 'normal');
        $product->update(['status' => $newStatus]);
    }
}
