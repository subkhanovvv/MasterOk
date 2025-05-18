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
            // Поиск по имени клиента или номеру
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('client_name', 'like', "%$search%")
                        ->orWhere('client_phone', 'like', "%$search%");
                });
            })

            // Фильтрация по типу операции
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->type);
            })

            // Фильтрация по стороне операции
            ->when($request->filled('side'), function ($query) use ($request) {
                if ($request->side === 'consume') {
                    $query->whereIn('type', ['consume', 'loan', 'return']);
                } elseif ($request->side === 'intake') {
                    $query->whereIn('type', ['intake', 'intake_loan', 'intake_return']);
                }
            })

            // Фильтрация по статусу займа
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })

            // Фильтрация по направлению займа
            ->when($request->filled('loan_direction'), function ($query) use ($request) {
                $query->where('loan_direction', $request->loan_direction);
            })

            // Фильтрация по типу оплаты
            ->when($request->filled('payment_type'), function ($query) use ($request) {
                $query->where('payment_type', $request->payment_type);
            })

            // Сортировка
            ->orderBy('id', $sortOrder)

            // Пагинация и сохранение параметров фильтра
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
