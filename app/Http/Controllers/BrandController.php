<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
   public function brand()
   {
      $brands = Brand::orderBy('id', 'desc')->paginate(10);
      return view('pages.brands.brand', compact('brands'));
   }

   public function new_brand()
   {
      return view('pages.brands.new-brand');
   }

   public function store_brand(Request $request)
   {
      $validated = $request->validate([
         'name'  => 'required|string|max:255',
         'phone' => 'required|string|max:255',
         // 'photo' => 'required|photo|mimes:jpeg,png,jpg,gif,svg|max:2048',
      ]);

      // Check for soft-deleted brand with same phone
      $existing = Brand::withTrashed()->where('phone', $validated['phone'])->first();

      $photoPath = $request->file('photo')->store('brands', 'public');

      if ($existing) {
         // Restore if deleted
         if ($existing->trashed()) {
            $existing->restore();
         }

         // Update brand data
         $existing->update([
            'name'  => $validated['name'],
            'phone' => $validated['phone'],
            'photo' => $photoPath,
         ]);
      } else {
         // Create new
         Brand::create([
            'name'  => $validated['name'],
            'phone' => $validated['phone'],
            'photo' => $photoPath,
         ]);
      }

      return redirect()->route('brand')->with('success', 'Бренд успешно сохранён!');
   }
   public function destroy_brand($id)
   {
      $brand = Brand::findOrFail($id);

      if ($brand->image) {
         Storage::delete($brand->image);
      }

      $brand->delete();

      return redirect()->route('brand')->with('success', ' Бренд успешно удалён!');
   }
}
