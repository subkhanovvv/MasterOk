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
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('id', $sortOrder)
            ->paginate(10)
            ->appends(['sort' => $sortOrder]);
        
        return view('pages.history.index', compact('transactions'));
    }
}
