<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class BarcodeController extends Controller
{
    public function index(Request $request)
    {
        $sortOrder = $request->get('sort', 'desc');
        $settings = Setting::all();

        $barcodes = Product::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('barcode_value', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->filled('brand_id'), function ($query) use ($request) {
                $query->where('brand_id', $request->brand_id);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('id', $sortOrder)
            ->whereNotNull('barcode')
            ->paginate(9)
            ->appends(['sort' => $sortOrder]);

        $categories = Category::all();
        $brands = Brand::all();
        return view('pages.barcodes.barcode', compact('barcodes', 'categories', 'brands', 'settings'));
    }
    public function print(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $copies = max((int)$request->get('copies', 1), 1);
        $perPage = min(max((int)$request->get('per_page', 20), 20), 5); // 3-12 barcodes per page

        return view('pages.barcodes.partials.barcode-print', [
            'product' => $product,
            'copies' => $copies,
            'perPage' => $perPage,
        ]);
    }

    public function printAll(Request $request)
    {
        $products = Product::all(); // Changed variable name to plural for clarity
        $copies = max((int)$request->get('copies', 1), 1);
        $perPage = min(max((int)$request->get('per_page', 20), 20), 5); // 3-12 barcodes per page

        return view('pages.barcodes.partials.barcode-print-all', [
            'products' => $products, // Changed to plural
            'copies' => $copies,
            'perPage' => $perPage,

        ]);
    }





    //    public function download(Request $request, $id)
    // {
    //     $product = Product::findOrFail($id);
    //     $copies = max((int)$request->get('copies', 1), 1);
    //     $barcodeSvg = $product->barcode; // Assuming you have raw SVG data in the 'barcode' column

    //     // Pass data to the view
    //     $pdf = Pdf::loadView('pages.barcodes.partials.barcode-pdf', [
    //         'product' => $product,
    //         'copies' => $copies,
    //         'barcodeSvg' => $barcodeSvg,  // Pass the raw SVG
    //     ]);

    //     // Set paper size to A4, portrait orientation
    //     $pdf->setPaper('a4', 'portrait');

    //     // Return the generated PDF as a downloadable file
    //     return $pdf->download("barcode_grid_{$product->id}.pdf");
    // }

}
