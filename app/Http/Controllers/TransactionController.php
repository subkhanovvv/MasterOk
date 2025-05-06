<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductActivity;
use App\Models\ProductActivityItems;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Validation\Validator;

class TransactionController extends Controller
{
    // public function consume(Request $request)
    // {
    //     $validated = $request->validate([
    //         'product_id' => 'required|exists:products,id',
    //         'qty' => 'required|integer|min:1',
    //         'type' => 'required|in:consume,loan,return,intake,intake_loan,intake_return',
    //         'total_price' => 'required|numeric|min:0',
    //         'payment_type' => 'required',
    //         'paid_amount' => 'nullable|numeric|min:0|lte:total_price',
    //         'client_phone' => 'nullable|string|max:20|required_if:type,loan',
    //         'client_name' => 'nullable|string|max:20|required_if:type,loan',
    //         'units_per_stock' => 'nullable|string|max:20',
    //         'return_reason' => 'nullable|string|max:500|required_if:type,return,intake_return',
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         $product = Product::findOrFail($validated['product_id']);

    //         if (in_array($validated['type'], ['consume', 'loan']) && $product->qty < $validated['qty']) {
    //             return back()->withErrors(['qty' => 'Недостаточно товара на складе. Доступно: ' . $product->qty]);
    //         }

    //         if (in_array($validated['type'], ['return', 'intake', 'intake_return'])) {
    //             $product->increment('qty', $validated['qty']);
    //         } elseif (in_array($validated['type'], ['consume', 'loan', 'intake_loan'])) {
    //             $product->decrement('qty', $validated['qty']);
    //         }

    //         if ($product->qty > 10) {
    //             $product->decrement('units_per_stock', $validated['units_per_stock']);
    //         } else {
    //         }

    //         $activityData = [
    //             'product_id' => $validated['product_id'],
    //             'qty' => $validated['qty'],
    //             'type' => $validated['type'],
    //             'total_price' => $validated['total_price'],
    //             'paid_amount' => $validated['paid_amount'] ?? 0,
    //             'payment_type' => $validated['payment_type'] ?? 0,
    //             'client_phone' => $validated['client_phone'] ?? null,
    //             'client_name' => $validated['client_name'] ?? null,
    //             'return_reason' => $validated['return_reason'] ?? null,
    //         ];

    //         $productActivity = ProductActivity::create($activityData);

    //         $qrContent = json_encode([
    //             'transaction_id' => $productActivity->id,
    //             'product_id' => $product->id,
    //             'product_name' => $product->name,
    //             'action' => $validated['type'],
    //             'quantity' => $validated['qty'],
    //             'payment_type' => $validated['payment_type'],
    //             'total_price' => $validated['total_price'],
    //             'date' => now()->toDateTimeString(),
    //         ]);

    //         $qrCodePath = 'qrcodes/transaction_' . $productActivity->id . '.svg';
    //         Storage::disk('public')->put(
    //             $qrCodePath,
    //             QrCode::format('svg')->size(150)->generate($qrContent)
    //         );

    //         $productActivity->update(['qr_code' => $qrCodePath]);

    //         DB::commit();

    //         return back()->with([
    //             'success' => 'Операция успешно сохранена!',
    //             'qr_code' => Storage::url($qrCodePath)
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->withErrors(['error' => 'Ошибка: ' . $e->getMessage()]);
    //     }
    // }
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
    public function consumption(Request $request)
    {
        $search = $request->input('search');

        $products = Product::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('barcode', 'like', "%$search%");
        })
            ->where('qty', '>', 0)
            ->orderBy('name')
            ->paginate(20);

        return view('pages.consumption', compact('products', 'search'));
    }

    /**
     * Store new consumption (No AJAX)
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:0.001',
            'unit' => 'required|array',
            'unit.*' => 'required|string',
            'price' => 'required|array',
            'price.*' => 'required|numeric|min:0',
            'total' => 'required|array',
            'total.*' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $items = [];
            $count = count($request->product_id);
            for ($i = 0; $i < $count; $i++) {
                $items[] = [
                    'product_id' => $request->product_id[$i],
                    'quantity' => $request->quantity[$i],
                    'unit' => $request->unit[$i],
                    'price' => $request->price[$i],
                    'total' => $request->total[$i]
                ];
            }

            $activity = ProductActivity::create([
                'type' => 'consume',
                'total_price' => collect($items)->sum('total'),
                'note' => $request->notes,
                'user_id' => auth()->id(),
            ]);

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $multiplier = $item['unit'] === $product->unit ? 1 : ($product->units_per_stock ?? 1);
                $baseQuantity = $item['quantity'] * $multiplier;

                if ($product->qty < $baseQuantity) {
                    throw new \Exception("Недостаточно товара: {$product->name}. Доступно: {$product->qty} {$product->unit}");
                }

                ProductActivityItems::create([
                    'product_activity_id' => $activity->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                    'base_quantity' => $baseQuantity,
                ]);

                $product->decrement('qty', $baseQuantity);
                $this->updateProductStatus($product);
            }

            DB::commit();
            return redirect()->route('consumption.history')->with('success', 'Расход успешно сохранен');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    protected function updateProductStatus(Product $product)
    {
        if ($product->qty <= 0) {
            $status = 'out_of_stock';
        } elseif ($product->qty < ($product->min_stock_level ?? 5)) {
            $status = 'low';
        } else {
            $status = 'normal';
        }

        if ($product->status !== $status) {
            $product->update(['status' => $status]);
        }
    }

    public function history()
    {
        $activities = ProductActivity::where('type', 'consume')
            ->with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('consumption.history', compact('activities'));
    }

    public function show($id)
    {
        $activity = ProductActivity::with(['items.product', 'user'])->findOrFail($id);

        return view('consumption.partials.details', compact('activity'));
    }

    public function print($id)
    {
        $activity = ProductActivity::with(['items.product', 'user'])->findOrFail($id);

        return view('consumption.print', compact('activity'));
    }
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.001',
            'unit' => 'required|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $consumptions = session('consumptions', []);

        $found = false;

        foreach ($consumptions as &$item) {
            if ($item['product_id'] == $product->id && $item['unit'] == $request->unit) {
                $item['quantity'] += $request->quantity;
                $item['total'] = $item['quantity'] * $item['price'];
                $found = true;
                break;
            }
        }

        if (!$found) {
            $price = $product->price_uzs; // optionally adjust by unit
            $consumptions[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'unit' => $request->unit,
                'quantity' => $request->quantity,
                'price' => $price,
                'total' => $price * $request->quantity,
            ];
        }

        session(['consumptions' => $consumptions]);

        $tableHtml = view('pages.consumption_table', [
            'consumptions' => $consumptions
        ])->render();

        return back()->with('success' , 'sucess');
    }


    public function remove($index)
    {
        $consumptions = session('consumptions', []);
        unset($consumptions[$index]);
        session(['consumptions' => array_values($consumptions)]);

        return back()->with('success', 'Продукт удален из списка');
    }
}
