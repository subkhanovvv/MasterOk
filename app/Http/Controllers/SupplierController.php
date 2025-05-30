<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Setting;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $sortOrder = $request->get('sort', 'desc');
        $settings = Setting::find(1);


        $suppliers = Supplier::with(['brand' => function ($query) {
            $query->withCount('products', 'suppliers');
        }])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('brand_id'), function ($query) use ($request) {
                $query->where('brand_id', $request->brand_id);
            })
            ->orderBy('id', $sortOrder)
            ->paginate(10)
            ->appends($request->only(['sort', 'search', 'brand_id']));

        $brands = Brand::withCount('products')->get();

        return view('pages.suppliers.index', compact('suppliers', 'brands', 'settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'note'     => 'nullable|string|max:255',
            'brand_id' => 'required|exists:brands,id',
        ]);

        try {
            Supplier::create($validated);

            return redirect()
                ->route('suppliers.index')
                ->with('success', 'Поставщик успешно добавлен!');
        } catch (\Exception $e) {
            Log::error('Supplier creation error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании поставщика');
        }
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Поставщик успешно обновлен!');
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();

            return redirect()
                ->route('suppliers.index')
                ->with('success', 'Поставщик успешно удален!');
        } catch (QueryException $e) {
            Log::error('Ошибка при удалении поставщика: ' . $e->getMessage());
            return redirect()
                ->route('suppliers.index')
                ->with('error', 'Ошибка при удалении поставщика.');
        }
    }
}
