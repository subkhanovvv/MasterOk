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
}
