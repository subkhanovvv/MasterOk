<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductActivity;
use App\Models\ProductActivityItems;
use App\Models\Supplier;
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
        'supplier_id' => 'required_if:type,intake,intake_loan|exists:suppliers,id',
        'payment_type' => 'required|in:cash,card,bank_transfer',
        'products' => 'required|array|min:1',
        'type' => 'required|in:intake,intake_loan,intake_return',

        // Loan-specific fields
        'loan_amount' => 'required_if:type,intake_loan|numeric|min:0',
        'loan_due_to' => 'required_if:type,intake_loan|date|after_or_equal:today',

        // Product item validations
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.qty' => 'required|numeric|min:0.01',
        'products.*.unit' => 'required|string',
        'products.*.price_uzs' => 'required|numeric|min:0',
        'products.*.price_usd' => 'required|numeric|min:0',

        'note' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $totalPrice = collect($validated['products'])->sum(function ($product) {
            return $product['price_uzs'] * $product['qty'];
        });

        // Determine status
        $status = $validated['type'] === 'intake_loan' ? 'incomplete' : 'complete';

        // Base activity data
        $activityData = [
            'type' => $validated['type'],
            'payment_type' => $validated['payment_type'],
            'total_price' => $totalPrice,
            'supplier_id' => in_array($validated['type'], ['intake', 'intake_loan']) ? $validated['supplier_id'] : null,
            'note' => $validated['note'] ?? null,
            'status' => $status,
        ];

        // Add loan-specific fields if applicable
        if ($validated['type'] === 'intake_loan') {
            $activityData['loan_amount'] = $validated['loan_amount'];
            $activityData['loan_due_to'] = $validated['loan_due_to'];
            $activityData['paid_amount'] = $validated['loan_amount'] - $totalPrice;
        }

        $productActivity = ProductActivity::create($activityData);

        // Generate and save QR code
        $qrContent = "Intake ID: {$productActivity->id}\n";
        $qrContent .= "Type: " . ucfirst(str_replace('_', ' ', $productActivity->type)) . "\n";
        $qrContent .= "Date: " . now()->format('Y-m-d H:i') . "\n";
        $qrContent .= "Total: " . number_format($totalPrice, 2) . " UZS";

        $qrCodeSvg = QrCode::format('svg')->size(150)->generate($qrContent);
        $qrCodePath = 'qrcodes/intake_' . $productActivity->id . '.svg';
        Storage::disk('public')->put($qrCodePath, $qrCodeSvg);
        $productActivity->update(['qr_code' => $qrCodePath]);

        // Save product items and update stock
        foreach ($validated['products'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            ProductActivityItems::create([
                'product_activity_id' => $productActivity->id,
                'product_id' => $product->id,
                'qty' => $item['qty'],
                'unit' => $item['unit'],
                'price' => $item['price_uzs'],
                'price_usd' => $item['price_usd'],
            ]);

            // Stock adjustment
            if ($validated['type'] === 'intake_return') {
                $product->decrement('qty', $item['qty']);
            } else {
                $product->increment('qty', $item['qty']);
            }
        }

        DB::commit();

        return redirect()
            ->route('intake.index')
            ->with('success', 'Product intake recorded successfully!')
            ->with('qr_code_path', $qrCodePath);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}

}
