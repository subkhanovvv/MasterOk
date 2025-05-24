<?php

namespace App\Http\Controllers;

use App\Models\ProductActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IndexController extends Controller
{
   public function index()
   {
    $startOfWeek = Carbon::now()->startOfWeek(); // Monday
    $endOfWeek = Carbon::now()->endOfWeek();     // Sunday

    $activities = ProductActivity::whereNotNull('loan_due_to')
        ->whereBetween('loan_due_to', [$startOfWeek, $endOfWeek])
        ->where('status', '!=', 'complete')
        ->get();

    return view('pages.index', compact('activities'));
   }
}
