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

        $suppliers = Supplier::when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('id', $sortOrder)
            ->paginate(10)
            ->appends(['sort' => $sortOrder]);
        $brands = Brand::all();

        return view('pages.suppliers.index', compact('suppliers' ,'brands'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'note'=>'nullable',
            'brand_id' => 'required|exists:brands,id',

        ]);

        try {
            Supplier::create([
                'name'  => $validated['name'],
                'note' => $validated['note']?? null,
                'brand_id' => $validated['brand_id'],
            ]);

            return redirect()
                ->route('suppliers.index')
                ->with('success', 's успешно добавлена!');
        } catch (\Exception $e) {
            Log::error('Category creation error: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании категории');
        }
    }

    public function update(Request $request, Supplier $s)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'note'=>'nullable',
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
