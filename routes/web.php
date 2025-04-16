<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::view('index','pages.index')->name('index');

Route::get('brand', [BrandController::class,'brand'])->name('brand');
Route::get('new-brand', [BrandController::class,'new_brand'])->name('new-brand');
Route::get('edit-brand/{id}', [BrandController::class, 'edit_brand'])->name('edit-brand');
Route::post('update-brand', [BrandController::class, 'update_brand'])->name('update-brand');
Route::post('store-brand',[BrandController::class,'store_brand'])->name('store-brand');
Route::delete('destroy-brand/{id}', [BrandController::class, 'destroy_brand'])->name('destroy-brand');

Route::get('category', [CategoryController::class,'category'])->name('category');
Route::get('new-category', [CategoryController::class,'new_category'])->name('new-category');
Route::get('edit-category/{id}', [CategoryController::class, 'edit_category'])->name('edit-category');
Route::post('update-category', [CategoryController::class, 'update_category'])->name('update-category');
Route::post('store-category',[CategoryController::class,'store_category'])->name('store-category');
Route::delete('destroy-category/{id}', [CategoryController::class, 'destroy_category'])->name('destroy-category');
