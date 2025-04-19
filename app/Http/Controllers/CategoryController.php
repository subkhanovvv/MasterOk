<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function category()
    {
        $categories = Category::orderBy('id', 'desc')->paginate(10);
        return view('pages.categories.category', compact('categories'));
    }

    public function new_category()
    {
        return view('pages.categories.new-category');
    }

    public function edit_category($id)
    {
        $categories = Category::find($id);
        return view('pages.categories.edit-category', compact('categories'));
    }

    public function store_category(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
        ]);

        $photoPath = $request->file('photo')->store('categories', 'public');

        Category::create([
            'name'  => $validated['name'],
            'photo' => $photoPath,
        ]);

        return redirect()->route('category')->with('success', 'Категория успешно сохранён!');
    }

    public function update_category(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($request->id);

        if ($request->hasFile('photo')) {
            if ($category->photo) {
                Storage::delete('public/' . $category->photo);
            }

            $photoPath = $request->file('photo')->store('categories', 'public');

            $validated['photo'] = $photoPath;
        } else {
            $validated['photo'] = $category->photo;
        }

        $category->update([
            'name' => $validated['name'],
            'photo'  => $validated['photo'],
        ]);

        return redirect()->route('category')->with('success', 'Категория успешно обновлён!');
    }

    public function destroy_category($id)
    {
        try {
            $category = Category::findOrFail($id);

            if ($category->photo) {
                Storage::delete($category->photo);
            }

            $category->delete();

            return redirect()->route('category')->with('success', ' Категория успешно удалён!');
        } catch (QueryException $e) {
            Log::error($e);

            return redirect()->route('brand')->with('error', 'Невозможно удалить бренд, так как он используется в продуктах.');
        }
    }
}
