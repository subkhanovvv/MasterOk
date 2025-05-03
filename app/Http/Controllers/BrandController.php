<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
   public function index(Request $request)
   {
      $sortOrder = $request->get('sort', 'desc');

      $brands = Brand::withCount('products')
         ->when($request->filled('search'), function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%');
         })
         ->orderBy('id', $sortOrder)
         ->paginate(10)
         ->appends(['sort' => $sortOrder]);

      return view('pages.brands.brand', compact('brands'));
   }
   public function create()
   {
      return view('pages.brands.new-brand');
   }

   public function edit($id)
   {
      $brands = Brand::find($id);
      return view('pages.brands.edit-brand', compact('brands'));
   }

   public function store(Request $request)
   {
      $validated = $request->validate([
         'name'  => 'required|string|max:255',
         'phone' => 'required|string|max:20',
         'description' => 'required|string|max:255',
      ]);

      $photoPath = $request->file('photo')->store('brands', 'public');

      Brand::create([
         'name'  => $validated['name'],
         'phone' => $validated['phone'],
         'description' => $validated['description'],
         'photo' => $photoPath,
      ]);
      $message = "🛒 Новый brand добавлен:\n\nНазвание: {$request->name}\n\nФото: {$request->file('photo')->getClientOriginalName()}\n\n telefon: {$request->phone}";
      $botToken = config('services.telegram.token');
      $chatIds = config('services.telegram.chat_ids');

      foreach ($chatIds as $chatId) {
         Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => trim($chatId),
            'text' => $message
         ]);
      }

      return redirect()->route('brand')->with('success', 'Бренд успешно сохранён!');
   }

   public function update(Request $request, Brand $brand)
   {
      $validated = $request->validate([
         'name' => 'required|string|max:255',
         'description' => 'nullable|string',
         'phone' => 'required|numeric|min:0',
         'photo' => 'nullable|image|max:2048',
      ]);

      $brand->name = $validated['name'];
      $brand->description = $validated['description'] ?? '';
      $brand->phone = $validated['phone']; // Updated to sale_price

      if ($request->hasFile('photo')) {
         if ($brand->photo) {
            Storage::delete($brand->photo);
         }
         $brand->photo = $request->file('photo')->store('brands');
      }

      $brand->save();

      return redirect()->back()->with('success', 'brand успешно обновлен.');
   }

   public function destroy($id)
   {
      try {
         $brand = Brand::findOrFail($id);
         $brand->delete();

         return redirect()->back()->with('success', 'Brand успешно удален');
      } catch (\Exception $e) {
         return redirect()->back()->with('error', 'Ошибка при удалении brandа');
      }
   }
}
