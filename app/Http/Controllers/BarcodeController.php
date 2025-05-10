<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BarcodeController extends Controller
{
    public function index(Request $request)
    {
        $sortOrder = $request->get('sort', 'desc');

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
        return view('pages.barcodes.barcode', compact('barcodes', 'categories', 'brands'));
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
            'barcodeSvg' => file_get_contents(storage_path('app/public/' . $product->barcode))
        ]);
    }

  public function download(Request $request, $id)
{
    $product = Product::findOrFail($id);

    // Safe filename generation
    $filename = $product->barcode_value ?: 'barcode_' . $product->id;
    $filename = trim($filename) !== '' ? $filename : 'barcode_' . $product->id;
    $filename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $filename) . '.pdf';

    // Get number of copies and barcodes per row
    $copies = max((int) $request->get('copies', 1), 1);
    $perRow = max(2, min(4, (int) $request->get('per_row', 3)));

    // Barcode path check
    $barcodePath = storage_path('app/public/' . $product->barcode);
    if (!file_exists($barcodePath)) {
        abort(404, 'Barcode image not found');
    }

    // Load barcode SVG as base64
    $barcodeData = file_get_contents($barcodePath);
    $barcodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($barcodeData);

    // Generate the PDF using the barcode partial view
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.barcodes.partials.barcode-pdf', [
        'product' => $product,
        'barcodeImage' => $barcodeBase64,
        'copies' => $copies,
        'perRow' => $perRow
    ])->setPaper('a4', 'portrait');

    return $pdf->download($filename);
}

}
