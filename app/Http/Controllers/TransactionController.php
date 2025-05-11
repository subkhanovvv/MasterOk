<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductActivity;
use App\Models\ProductActivityItems;
use App\Models\Supplier;
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
    public function intakeIndex(Request $request)
    {
        $search = $request->input('search');

        $products = Product::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('barcode', 'like', "%$search%");
        })
            ->orderBy('name')
            ->paginate(20);
        $suppliers = Supplier::all() ?? '-';

        return view('pages.intake.intake', compact('products', 'search', 'suppliers'));
    }

    public function intakeStore(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_type' => 'required|in:cash,card',
            'paid_amount' => 'nullable|numeric',
            'note' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|numeric|min:0.01',
            'products.*.unit' => 'required|string',
            'products.*.price_uzs' => 'required|numeric|min:0',
            'products.*.price_usd' => 'required|numeric|min:0',
        ]);

        // Calculate total price
        $totalPrice = collect($request->products)->sum(function ($item) {
            return $item['qty'] * $item['price_uzs'];
        });

        // Create product activity
        $activity = ProductActivity::create([
            'type' => 'intake',
            'supplier_id' => $request->supplier_id,
            'payment_type' => $request->payment_type,
            'paid_amount' => $request->paid_amount,
            'total_price' => $totalPrice,
            'note' => $request->note,
        ]);

        // Create product activity items and update product quantities
        foreach ($request->products as $productData) {
            $product = Product::find($productData['product_id']);

            // Create activity item
            ProductActivityItems::create([
                'product_activity_id' => $activity->id,
                'product_id' => $product->id,
                'qty' => $productData['qty'],
                'unit' => $productData['unit'],
                'price' => $productData['price_uzs'],
            ]);

            // Update product quantity
            // You may need to convert units here based on your business logic
            $qtyToAdd = $productData['qty'];

            // Example conversion if stock unit is different
            if ($product->stock_unit && $product->units_per_stock && $productData['unit'] === $product->stock_unit) {
                $qtyToAdd = $productData['qty'] * $product->units_per_stock;
            }

            $product->qty += $qtyToAdd;

            // Update product status based on quantity
            if ($product->qty > 10) {
                $product->status = 'normal';
            } elseif ($product->qty > 0) {
                $product->status = 'low';
            } else {
                $product->status = 'out_of_stock';
            }

            // Update prices if needed
            $product->price_uzs = $productData['price_uzs'];
            $product->price_usd = $productData['price_usd'];

            $product->save();
        }

        return redirect()->route('intake.index')->with('success', 'Приход товаров успешно добавлен');
    }

    public function intakeAdd(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.001',
            'unit' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($request->product_id);
        $intakes = session('intakes', []);

        $found = false;

        foreach ($intakes as &$item) {
            if ($item['product_id'] == $product->id && $item['unit'] == $request->unit) {
                $item['qty'] += $request->qty;
                $item['total'] = $item['qty'] * $item['price'];
                $found = true;
                break;
            }
        }

        if (!$found) {
            $intakes[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'unit' => $request->unit,
                'qty' => $request->qty,
                'price' => $request->price,
                'total' => $request->price * $request->qty,
            ];
        }

        session(['intakes' => $intakes]);

        return back()->with('success', 'Продукт добавлен в приход');
    }

    public function intakeRemove($index)
    {
        $intakes = session('intakes', []);
        unset($intakes[$index]);
        session(['intakes' => array_values($intakes)]);

        return back()->with('success', 'Продукт удален из списка прихода');
    }

    public function intakeHistory()
    {
        $activities = ProductActivity::whereIn('type', ['intake', 'intake_loan', 'intake_return'])
            ->with(['supplier', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('intake.history', compact('activities'));
    }

    private function generateQrCode($productActivity)
    {
        $qrContent = "Transaction ID: {$productActivity->id}\n";
        $qrContent .= "Type: {$productActivity->type}\n";
        $qrContent .= "Total Price: {$productActivity->total_price}\n";
        $qrContent .= "Date: {$productActivity->created_at}\n";
        $qrContent .= "Items:\n";

        foreach ($productActivity->items as $item) {
            $qrContent .= "- {$item->product->name} | Qty: {$item->qty} {$item->unit}\n";
        }

        $qrCodeSvg = QrCode::format('svg')->size(150)->generate($qrContent);
        $qrCodePath = 'qrcodes/intake_' . $productActivity->id . '.svg';
        Storage::disk('public')->put($qrCodePath, $qrCodeSvg);

        $productActivity->update(['qr_code' => $qrCodePath]);
    }
    // public function transactions(Request $request)
    // {
    //     $query = ProductActivity::with('product')
    //         ->orderBy('created_at', 'desc');

    //     // Apply filters
    //     if ($request->filled('date_from')) {
    //         $query->whereDate('created_at', '>=', $request->date_from);
    //     }

    //     if ($request->filled('date_to')) {
    //         $query->whereDate('created_at', '<=', $request->date_to);
    //     }

    //     if ($request->filled('type')) {
    //         $query->where('type', $request->type);
    //     }

    //     if ($request->filled('product_id')) {
    //         $query->whereHas('product', function ($q) use ($request) {
    //             $q->where('id', $request->product_id);
    //         });
    //     }

    //     $transactions = $query->paginate(25);

    //     $products = Product::orderBy('name')->get();

    //     return view('pages.transaction.transactions', [
    //         'transactions' => $transactions,
    //         'products' => $products
    //     ]);
    // }
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

        return view('pages.consumption.index', compact('products', 'search'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Create the product activity WITHOUT user_id
            $productActivity = ProductActivity::create([
                'type' => 'consume',
                'total_price' => array_sum($request->total),
                'note' => $request->notes,
            ]);

            // Create product activity items
            foreach ($request->product_id as $index => $productId) {
                $productActivity->items()->create([
                    'product_id' => $productId,
                    'quantity' => $request->quantity[$index],
                    'unit' => $request->unit[$index],
                    'price' => $request->price[$index],
                ]);

                // Update product quantity
                $product = Product::find($productId);
                $product->decrement('qty', $request->quantity[$index]);
            }

            DB::commit();

            // Clear the session consumption data
            session()->forget('consumptions');

            return redirect()->route('transactions')->with('success', 'Расход успешно сохранен!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Произошла ошибка: ' . $e->getMessage()]);
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

        return back()->with('success', 'sucess');
    }


    public function remove($index)
    {
        $consumptions = session('consumptions', []);
        unset($consumptions[$index]);
        session(['consumptions' => array_values($consumptions)]);

        return back()->with('success', 'Продукт удален из списка');
    }
}
