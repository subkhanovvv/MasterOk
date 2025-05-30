<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $sortOrder = $request->get('sort', 'desc');
        $settings = Setting::all();

        $categories = Category::withCount('products')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('id', $sortOrder)
            ->paginate(10)
            ->appends(['sort' => $sortOrder]);

        return view('pages.categories.category', compact('categories', 'settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('categories', 'public');
            }

            Category::create([
                'name'  => $validated['name'],
                'photo' => $photoPath,
            ]);

            return redirect()
                ->route('categories.index')
                ->with('success', 'Категория успешно добавлена!');
        } catch (\Exception $e) {
            Log::error('Category creation error: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании категории');
        }
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($category->photo) {
                    Storage::delete('public/' . $category->photo);
                }
                // Store new photo
                $validated['photo'] = $request->file('photo')->store('categories', 'public');
            } else {
                // Keep existing photo if no new one uploaded
                $validated['photo'] = $category->photo;
            }

            $category->update($validated);

            return redirect()
                ->route('categories.index')
                ->with('success', 'Категория успешно обновлена!');
        } catch (\Exception $e) {
            Log::error('Category update error: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении категории');
        }
    }

    public function destroy(Category $category)
    {
        try {
            // Check if category has associated products
            if ($category->products_count > 0) {
                return redirect()
                    ->route('categories.index')
                    ->with('error', 'Невозможно удалить категорию, так как она используется в продуктах.');
            }

            // Delete photo if exists
            if ($category->photo) {
                Storage::delete('public/' . $category->photo);
            }

            $category->delete();

            return redirect()
                ->route('categories.index')
                ->with('success', 'Категория успешно удалена!');
        } catch (QueryException $e) {
            Log::error('Category deletion error: ' . $e->getMessage());
            return redirect()
                ->route('categories.index')
                ->with('error', 'Ошибка при удалении категории: ' . $e->getMessage());
        }
    }
}
