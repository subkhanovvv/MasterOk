<?php

namespace App\Http\Controllers;

use App\Models\ProductActivity;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $sortOrder = $request->get('sort', 'desc');

        $transactions = ProductActivity::withCount('items')
            ->with(['items.product.brand', 'items.product.category', 'supplier', 'brand'])


            // Search by client_name, client_phone, supplier name, brand name, category name
            ->when($request->filled('brand_id'), function ($q) use ($request) {
                $q->where('brand_id', $request->brand_id);
            })

            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('brand', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                });
            })
            // Filter by start_date and end_date on created_at
            ->when($request->filled('start_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->end_date);
            })

            // Handle loan_filter first, if given
            ->when($request->filled('loan_filter'), function ($query) use ($request) {
                if ($request->loan_filter === 'all') {
                    $query->whereIn('type', ['loan', 'intake_loan']);
                } elseif (in_array($request->loan_filter, ['loan', 'intake_loan'])) {
                    $query->where('type', $request->loan_filter);
                }
                // else no loan filter applied
            })

            // Otherwise filter by multiple types if loan_filter not used
            ->unless($request->filled('loan_filter'), function ($query) use ($request) {
                if ($request->filled('type')) {
                    $types = is_array($request->type) ? $request->type : explode(',', $request->type);
                    $query->whereIn('type', $types);
                }
            })

            // Filter by side (consume or intake groups)
            ->when($request->filled('side'), function ($query) use ($request) {
                if ($request->side === 'consume') {
                    $query->whereIn('type', ['consume', 'loan', 'return']);
                } elseif ($request->side === 'intake') {
                    $query->whereIn('type', ['intake', 'intake_loan', 'intake_return']);
                } else if ($request->side === 'return') {
                    $query->whereIn('type', ['return', 'intake_return']);
                }
            })

            // Filter by status
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })

            // Filter by loan_direction
            ->when($request->filled('loan_direction'), function ($query) use ($request) {
                $query->where('loan_direction', $request->loan_direction);
            })

            // Filter by payment_type
            ->when($request->filled('payment_type'), function ($query) use ($request) {
                $query->where('payment_type', $request->payment_type);
            })

            ->orderBy('id', $sortOrder)
            ->paginate(10)
            ->appends($request->except('page'));

        $brands = \App\Models\Brand::all();

        return view('pages.history.index', compact('transactions', 'brands', 'sortOrder'));
    }

    public function print($id)
    {
        $transaction = ProductActivity::with(['items.product', 'supplier'])->findOrFail($id);
        return view('pages.history.partials.history-print', compact('transaction'));
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:complete,incomplete',
        ]);

        $transaction = ProductActivity::findOrFail($id);

        if ($transaction->status === 'incomplete' && $request->status === 'complete') {
            $transaction->status = 'complete';
            $transaction->save();
        }

        return redirect()->back()->with('success', 'Статус обновлен');
    }
}
