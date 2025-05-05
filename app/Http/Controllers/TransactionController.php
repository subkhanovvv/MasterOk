<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductActivity;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function consume(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'type' => 'required|in:consume,loan,return,intake,intake_loan,intake_return',
            'total_price' => 'required|numeric|min:0',
            'payment_type' => 'required',
            'paid_amount' => 'nullable|numeric|min:0|lte:total_price',
            'client_phone' => 'nullable|string|max:20|required_if:type,loan',
            'client_name' => 'nullable|string|max:20|required_if:type,loan',
            'units_per_stock' => 'nullable|string|max:20',
            'return_reason' => 'nullable|string|max:500|required_if:type,return,intake_return',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($validated['product_id']);

            if (in_array($validated['type'], ['consume', 'loan']) && $product->qty < $validated['qty']) {
                return back()->withErrors(['qty' => 'Недостаточно товара на складе. Доступно: ' . $product->qty]);
            }

            if (in_array($validated['type'], ['return', 'intake', 'intake_return'])) {
                $product->increment('qty', $validated['qty']);
            } elseif (in_array($validated['type'], ['consume', 'loan', 'intake_loan'])) {
                $product->decrement('qty', $validated['qty']);
            }

            if ($product->qty > 10) {
                $product->decrement('units_per_stock', $validated['units_per_stock']);
            } else {
               
            }

            $activityData = [
                'product_id' => $validated['product_id'],
                'qty' => $validated['qty'],
                'type' => $validated['type'],
                'total_price' => $validated['total_price'],
                'paid_amount' => $validated['paid_amount'] ?? 0,
                'payment_type' => $validated['payment_type'] ?? 0,
                'client_phone' => $validated['client_phone'] ?? null,
                'client_name' => $validated['client_name'] ?? null,
                'return_reason' => $validated['return_reason'] ?? null,
            ];

            $productActivity = ProductActivity::create($activityData);

            $qrContent = json_encode([
                'transaction_id' => $productActivity->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'action' => $validated['type'],
                'quantity' => $validated['qty'],
                'payment_type' => $validated['payment_type'],
                'total_price' => $validated['total_price'],
                'date' => now()->toDateTimeString(),
            ]);

            $qrCodePath = 'qrcodes/transaction_' . $productActivity->id . '.svg';
            Storage::disk('public')->put(
                $qrCodePath,
                QrCode::format('svg')->size(150)->generate($qrContent)
            );

            $productActivity->update(['qr_code' => $qrCodePath]);

            DB::commit();

            return back()->with([
                'success' => 'Операция успешно сохранена!',
                'qr_code' => Storage::url($qrCodePath)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Ошибка: ' . $e->getMessage()]);
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
    public function report(Request $request)
    {
        // Date filters (default to current month)
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Transaction type filter
        $type = $request->input('type', 'all');

        // Previous period for comparison
        $daysDiff = Carbon::parse($startDate)->diffInDays($endDate);
        $previousStartDate = Carbon::parse($startDate)->subDays($daysDiff)->format('Y-m-d');
        $previousEndDate = Carbon::parse($startDate)->subDay()->format('Y-m-d');

        // Get totals for current period
        $currentTotals = $this->getTotals($startDate, $endDate, $type);
        $previousTotals = $this->getTotals($previousStartDate, $previousEndDate, $type);

        // Calculate percentage changes
        $revenueChange = $this->calculateChange($currentTotals->total_revenue, $previousTotals->total_revenue);
        $costChange = $this->calculateChange($currentTotals->total_cost, $previousTotals->total_cost);
        $profitChange = $this->calculateChange($currentTotals->total_profit, $previousTotals->total_profit);

        // Get chart data
        $chartData = $this->getChartData($startDate, $endDate, $type);

        // Get transactions with calculated profit
        $transactions = ProductActivity::with('product')
            ->when($type !== 'all', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(function ($transaction) {
                $transaction->profit = $this->calculateTransactionProfit($transaction);
                return $transaction;
            });

        return view('pages.report', [
            'totalRevenue' => $currentTotals->total_revenue ?? 0,
            'totalCost' => $currentTotals->total_cost ?? 0,
            'totalProfit' => $currentTotals->total_profit ?? 0,
            'revenueChange' => $revenueChange,
            'costChange' => $costChange,
            'profitChange' => $profitChange,
            'chartData' => $chartData,
            'transactions' => $transactions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedType' => $type,
            'transactionTypes' => [
                'all' => 'All Types',
                'consume' => 'Sales',
                'intake' => 'Purchases',
                'return' => 'Customer Returns',
                'loan' => 'Customer Credits',
                'intake_return' => 'Supplier Returns',
                'intake_loan' => 'Supplier Credits'
            ]
        ]);
    }

    protected function getTotals($startDate, $endDate, $type = 'all')
    {
        return ProductActivity::query()
            ->when($type !== 'all', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
         SUM(total_price) as total_revenue,
         SUM(CASE 
             WHEN type IN ("consume", "return") THEN 
                 (SELECT price_uzs FROM products WHERE products.id = product_activities.product_id) * qty 
             ELSE 0 
         END) as total_cost,
         SUM(total_price) - SUM(CASE 
             WHEN type IN ("consume", "return") THEN 
                 (SELECT price_uzs FROM products WHERE products.id = product_activities.product_id) * qty 
             ELSE 0 
         END) as total_profit
     ')
            ->first();
    }

    protected function calculateChange($current, $previous)
    {
        if ($previous == 0) {
            return $current == 0 ? 0 : 100;
        }
        return (($current - $previous) / $previous) * 100;
    }

    protected function calculateTransactionProfit($transaction)
    {
        if (!$transaction->product) {
            return 0;
        }

        // For sales and returns, profit = total_price - (product cost * quantity)
        if (in_array($transaction->type, ['consume', 'return'])) {
            return $transaction->total_price - ($transaction->product->price_uzs * $transaction->qty);
        }

        // For other transaction types, profit is just the total price
        return $transaction->total_price;
    }

    protected function getChartData($startDate, $endDate, $type = 'all')
    {
        $data = ProductActivity::query()
            ->when($type !== 'all', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
         DATE(created_at) as date,
         SUM(total_price) as revenue,
         SUM(CASE 
             WHEN type IN ("consume", "return") THEN 
                 (SELECT price_uzs FROM products WHERE products.id = product_activities.product_id) * qty 
             ELSE 0 
         END) as cost,
         SUM(total_price) - SUM(CASE 
             WHEN type IN ("consume", "return") THEN 
                 (SELECT price_uzs FROM products WHERE products.id = product_activities.product_id) * qty 
             ELSE 0 
         END) as profit,
         COUNT(*) as transaction_count
     ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            }),
            'revenue' => $data->pluck('revenue'),
            'cost' => $data->pluck('cost'),
            'profit' => $data->pluck('profit'),
            'transaction_count' => $data->pluck('transaction_count')
        ];
    }
}
