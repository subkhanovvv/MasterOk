<?php

namespace App\Http\Controllers;

use App\Models\ProductActivity;
use App\Models\Setting;
use Illuminate\Http\Request;

class ChequeController extends Controller
{
    public function printCheque($id)
    {
        $transaction = ProductActivity::with('products')->findOrFail($id);
        $settings = Setting::id(1);
        return view('pages.cheques.transactions', compact('transaction','settings'));
    }
    
}
