<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sortOrder = $request->get('sort', 'desc');

        $products = Product::query()
            ->with('get_brand')
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('barcode', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->brand_id, function ($query) use ($request) {
                $query->where('brand_id', $request->brand_id);
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('id', $sortOrder)
            ->paginate(10)
            ->appends(['sort' => $sortOrder]);

        $categories = Category::all();
        $brands = Brand::all();

        return view('pages.products.index', compact('products', 'categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image',
            'stock_unit' => 'nullable|string|max:50',
            'units_per_stock' => 'nullable|integer|min:1',
            'unit' => 'required|string|max:50',
            'price_uzs' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0',
            'short_description' => 'nullable|string|max:1000',
            'sale_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $validated['name'],
            'photo' => $photoPath,
            'units_per_stock' => $validated['units_per_stock'],
            'stock_unit' => $validated['stock_unit'] ?? null,
            'unit' => $validated['unit'],
            'price_uzs' => $validated['price_uzs'],
            'price_usd' => $validated['price_usd'],
            'short_description' => $validated['short_description'] ?? null,
            'sale_price' => $validated['sale_price'] ?? null,
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
        ]);

        $firstLetter = strtoupper(substr($validated['name'], 0, 1)); 

        $categoryPart = str_pad($product->category_id, 2, '0', STR_PAD_LEFT);
        $productPart = str_pad($product->id, 5, '0', STR_PAD_LEFT);
        $numericPart = $categoryPart . $productPart;

        $barcodeValue = $firstLetter . $numericPart;

        $barcodeDir = storage_path('app/public/barcodes');
        if (!file_exists($barcodeDir)) {
            mkdir($barcodeDir, 0755, true);
        }

        $dns1d = new \Milon\Barcode\DNS1D();
        $barcodeSVG = $dns1d->getBarcodeSVG($barcodeValue, 'C128', 1, 60, false);
        $barcodeImagePath = 'barcodes/' . $barcodeValue . '.svg';
        file_put_contents(storage_path('app/public/' . $barcodeImagePath), $barcodeSVG);

        $product->update([
            'barcode_value' => $barcodeValue, 
            'barcode' => $barcodeImagePath,   
        ]);

        return back()->with('success', 'Товар и штрихкод успешно сохранены!');
    }
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return redirect()->back()->with('success', 'Продукт успешно удален');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка при удалении продукта');
        }
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image',
            'stock_unit' => 'nullable|string|max:50',
            'units_per_stock' => 'nullable|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'price_uzs' => 'required|numeric|min:0',
            'price_usd' => 'required|numeric|min:0',
            'short_description' => 'nullable|string|max:1000',
            'sale_price' => 'nullable|numeric|min:0',
        ]);

        $product->name = $validated['name'];
        $product->short_description = $validated['short_description'] ?? null;
        // $product->category_id = $validated['category_id'] ?? null;
        // $product->brand_id = $validated['brand_id'] ?? null;
        $product->unit = $validated['unit'];
        $product->stock_unit = $validated['stock_unit'];
        $product->units_per_stock = $validated['units_per_stock'];
        $product->price_usd = $validated['price_usd'] ?? 0;
        $product->price_uzs = $validated['price_uzs'] ?? 0;
        $product->sale_price = $validated['sale_price'];

        if ($request->hasFile('photo')) {
            // Only delete if file exists on disk
            if ($product->photo && Storage::disk('public')->exists($product->photo)) {
                Storage::disk('public')->delete($product->photo);
            }

            $path = $request->file('photo')->store('products', 'public');
            $product->photo = $path;
        }

        $product->save();

        return redirect()->back()->with('success', 'Товар успешно обновлен.');
    }
}
