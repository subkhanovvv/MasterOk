<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function expense()
    {
        $caregories = Category::orderBy('id', 'desc')->get();
        $brands = Brand::orderBy('id', 'desc')->get();
        $products = Product::orderBy('id', 'desc')->paginate('10');
        return view('pages.expenses.expense', compact('products' , 'brands', 'caregories'));
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

    public function edit($id)
    {
        return view('expenses.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Validate and update the expense
        // Redirect or return a response
    }
}
