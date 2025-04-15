<?php

use App\Http\Controllers\BrandController;
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

Route::view('index','pages.dindex')->name('index');

Route::get('brand', [BrandController::class,'brand'])->name('brand');
Route::get('new-brand', [BrandController::class,'new_brand'])->name('new-brand');
Route::get('edit-brand/{id}', [BrandController::class, 'edit_brand'])->name('edit-brand');
Route::post('update-brand', [BrandController::class, 'update_brand'])->name('update-brand');
Route::post('store-brand',[BrandController::class,'store_brand'])->name('store-brand');
Route::delete('destroy-brand/{id}', [BrandController::class, 'destroy_brand'])->name('destroy-brand');
