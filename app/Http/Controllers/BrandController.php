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
   public function brand()
   {
      $brands = Brand::withCount('products') // add this line
         ->orderBy('id', 'desc')
         ->paginate(10);

      return view('pages.brands.brand', compact('brands'));
   }

   public function new_brand()
   {
      return view('pages.brands.new-brand');
   }

   public function edit_brand($id)
   {
      $brands = Brand::find($id);
      return view('pages.brands.edit-brand', compact('brands'));
   }

   public function store_brand(Request $request)
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

   public function update_brand(Request $request)
   {
      $validated = $request->validate([
         'name'  => 'required|string|max:255',
         'phone'  => 'required|string|max:20|unique:brands,phone,' . $request->id,
         'description'  => 'required|string|max:255,',
      ]);

      $brand = Brand::findOrFail($request->id);

      if ($request->hasFile('photo')) {
         if ($brand->photo) {
            Storage::delete('public/' . $brand->photo);
         }

         $photoPath = $request->file('photo')->store('brands', 'public');

         $validated['photo'] = $photoPath;
      } else {
         $validated['photo'] = $brand->photo;
      }

      $brand->update([
         'name' => $validated['name'],
         'phone' => $validated['phone'],
         'photo'  => $validated['photo'],
         'description'  => $validated['description'],
      ]);

      return redirect()->route('brand')->with('success', 'Бренд успешно обновлён!');
   }

   public function destroy_brand($id)
   {
      try {
         $brand = Brand::findOrFail($id);
         if ($brand->photo) {
            Storage::delete($brand->photo);
         }
         $brand->delete();

         return redirect()->route('brand')->with('success', 'Бренд успешно удален.');
      } catch (QueryException $e) {
         Log::error($e);

         return redirect()->route('brand')->with('error', 'Невозможно удалить бренд, так как он используется в продуктах.');
      }
   }
}
