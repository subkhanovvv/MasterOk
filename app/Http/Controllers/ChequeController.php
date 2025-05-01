<?php

namespace App\Http\Controllers;

use App\Models\ProductActivity;
use Illuminate\Http\Request;

class ChequeController extends Controller
{
    public function printCheque($id)
    {
        $transaction = ProductActivity::with('products')->findOrFail($id);
        return view('pages.cheques.transactions', compact('transaction'));
    }
    
}
