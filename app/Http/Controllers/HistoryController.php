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
            ->with(['items.product'])

            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('client_name', 'like', "%$search%")
                        ->orWhere('client_phone', 'like', "%$search%");
                });
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

            // The rest of filters remain unchanged
            ->when($request->filled('side'), function ($query) use ($request) {
                if ($request->side === 'consume') {
                    $query->whereIn('type', ['consume', 'loan', 'return']);
                } elseif ($request->side === 'intake') {
                    $query->whereIn('type', ['intake', 'intake_loan', 'intake_return']);
                }
            })

            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })

            ->when($request->filled('loan_direction'), function ($query) use ($request) {
                $query->where('loan_direction', $request->loan_direction);
            })

            ->when($request->filled('payment_type'), function ($query) use ($request) {
                $query->where('payment_type', $request->payment_type);
            })

            ->orderBy('id', $sortOrder)
            ->paginate(10)
            ->appends($request->except('page'));

        return view('pages.history.index', compact('transactions'));
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
