<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Expenses;
use App\Models\Product;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function expense()
    {
        $caregories = Category::orderBy('id', 'desc')->get();
        $brands = Brand::orderBy('id', 'desc')->get();
        $products = Product::orderBy('id', 'desc')->paginate('10');
        return view('pages.expenses.expense', compact('products', 'brands', 'caregories'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        // Validate and store the expense
        // Redirect or return a response
    }

    public function show($id)
    {
        return view('expenses.show', compact('id'));
    }

    public function consume(Request $req)
    {
        Expenses::create([
            'product_id' => $req->product_id,
            'qty' => $req->qty,
            'type' => $req->type,
            'price' => $req->price,
            'date' => $req->date,
        ]);

        Product::find($req->product_id)->decrement('qty', $req->qty);

        return Product::all();
    }
}
