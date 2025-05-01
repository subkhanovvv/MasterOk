<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductActivity;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function consume(Request $req)
    {
        $validated = $req->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'type' => 'required|string|max:50',
            'total_price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'client_phone' => 'nullable|string|max:20',
            'return_reason' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($validated['product_id']);

            if ($validated['type'] === 'consume' && $product->qty < $validated['qty']) {
                return back()->withErrors(['qty' => 'Недостаточно товара на складе для расхода.']);
            }
            if (in_array($validated['type'], ['return', 'intake'])) {
                $product->increment('qty', $validated['qty']);
            } else {
                $product->decrement('qty', $validated['qty']);
            }
            $product->save();

            $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

            // Create Product Activity entry
            $productActivity = ProductActivity::create([
                'product_id' => $validated['product_id'],
                'qty' => $validated['qty'],
                'type' => $validated['type'],
                'total_price' => $validated['total_price'],
                'paid_amount' => $validated['paid_amount'],
                'client_phone' => $validated['client_phone'],
                'return_reason' => $validated['return_reason'],
            ]);

            // Generate the QR code content
            $qrContent = "Transaction ID: {$productActivity->id}\nProduct ID: {$productActivity->product_id}\nAction: {$productActivity->type}\nQty: {$productActivity->qty}\nTotal Price: {$productActivity->total_price}\nPaid Amount: {$productActivity->paid_amount}\nDate : {$productActivity->created_at}";

            // Generate QR code in SVG format
            $qrCodeSvg = QrCode::format('svg')->size(150)->generate($qrContent);

            // Save the SVG to the storage folder
            $qrCodePath = 'qrcodes/transaction_' . $productActivity->id . '.svg';
            Storage::disk('public')->put($qrCodePath, $qrCodeSvg);

            // Save the path of the QR code in the product activity
            $productActivity->qr_code = $qrCodePath;
            $productActivity->save();

            DB::commit();

            return back()->with('success', 'Операция успешно сохранена!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Произошла ошибка: ' . $e->getMessage()]);
        }
    }
    public function intake(Request $req)
    {
        $validated = $req->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'type' => 'required|string|max:50',
            'total_price' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'return_reason' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($validated['product_id']);

            // Adjust product quantity based on type (intake or intake_loan)
            if (in_array($validated['type'], ['intake_loan', 'intake'])) {
                $product->increment('qty', $validated['qty']);
            } else {
                $product->decrement('qty', $validated['qty']);
            }
            $product->save();

            // Ensure 'paid_amount' is not null
            $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

            // Create Product Activity entry
            $productActivity = ProductActivity::create([
                'product_id' => $validated['product_id'],
                'qty' => $validated['qty'],
                'type' => $validated['type'],
                'total_price' => $validated['total_price'],
                'paid_amount' => $validated['paid_amount'],
                'return_reason' => $validated['return_reason'],
            ]);

            // Step 1: Generate the content for the QR code
            $qrContent = "Transaction ID: {$productActivity->id}\nProduct ID: {$productActivity->product_id}\nAction: {$productActivity->type}\nQty: {$productActivity->qty}\nTotal Price: {$productActivity->total_price}\nPaid Amount: {$productActivity->paid_amount}";

            // Step 2: Generate the signature
            $secret = env('QR_SECRET', 'default-secret');
            $signatureData = "{$productActivity->id}|{$productActivity->product_id}|{$productActivity->qty}|{$productActivity->total_price}|{$productActivity->paid_amount}|{$secret}";
            $signature = hash('sha256', $signatureData);

            // Step 3: Append the signature to the QR content
            $qrContent .= "\nSignature: {$signature}";

            // Step 4: Generate QR code in SVG format
            $qrCodeSvg = QrCode::format('svg')->size(150)->generate($qrContent);

            // Step 5: Save the QR code to the storage folder
            $qrCodePath = 'qrcodes/transaction_' . $productActivity->id . '.svg';
            Storage::disk('public')->put($qrCodePath, $qrCodeSvg);

            // Step 6: Save the QR code path in the product activity
            $productActivity->qr_code = $qrCodePath;
            $productActivity->save();

            DB::commit();

            return back()->with('success', 'Операция успешно сохранена!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Произошла ошибка: ' . $e->getMessage()]);
        }
    }
    public function transactions()
    {
        $brands = Brand::orderBy('id', 'desc')->get();
        $categories = Category::orderBy('id', 'desc')->get();
        $transaction = ProductActivity::orderBy('id', 'desc')->paginate(10);
        return view('pages.transaction.transactions', compact('transaction', 'brands', 'categories'));
    }
}
