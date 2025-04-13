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

Route::view('admin','pages.index')->name('index');

Route::get('brand', BrandController::class,'brand')->name('brand');
Route::get('new-brand', BrandController::class,'new_brand')->name('new-brand');
Route::get('edit-brand', BrandController::class,'edit_brand')->name('edit-brand');
