<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $sortOrder = $request->get('sort', 'desc');

        $suppliers = Supplier::with(['brand' => function ($query) {
            $query->withCount('products');
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

        return view('pages.suppliers.index', compact('suppliers', 'brands'));
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


    public function update(Request $request, Supplier $s)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'note' => 'nullable',
        ]);

        try {
            $s->update($validated);

            return redirect()
                ->route('suppliers.index')
                ->with('success', 'Категория успешно обновлена!');
        } catch (\Exception $e) {
            Log::error('s update error: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении категории');
        }
    }

    public function destroy(Supplier $s)
    {
        try {
            // Check if category has associated products
            if ($s->products_count > 0) {
                return redirect()
                    ->route('suppliers.index')
                    ->with('error', 'Невозможно удалить категорию, так как она используется в продуктах.');
            }

            $s->delete();

            return redirect()
                ->route('suppliers.index')
                ->with('success', 'Категория успешно удалена!');
        } catch (QueryException $e) {
            Log::error('s deletion error: ' . $e->getMessage());
            return redirect()
                ->route('suppliers.index')
                ->with('error', 'Ошибка при удалении категории: ' . $e->getMessage());
        }
    }
}
