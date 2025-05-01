<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChequeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Milon\Barcode\DNS1D as BarcodeDNS1D;
use Milon\Barcode\Facades\DNS1D;

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
    if (Auth::user()) {
        return view('pages.index');
    } else {
        return view('auth.login');
    }
});

Route::middleware(['auth'])->group(function () {

    Route::view('index', 'pages.index')->name('index');

    Route::get('brand', [BrandController::class, 'brand'])->name('brand');
    Route::get('new-brand', [BrandController::class, 'new_brand'])->name('new-brand');
    Route::get('edit-brand/{id}', [BrandController::class, 'edit_brand'])->name('edit-brand');
    Route::post('update-brand', [BrandController::class, 'update_brand'])->name('update-brand');
    Route::post('store-brand', [BrandController::class, 'store_brand'])->name('store-brand');
    Route::delete('destroy-brand/{id}', [BrandController::class, 'destroy_brand'])->name('destroy-brand');

    Route::get('category', [CategoryController::class, 'category'])->name('category');
    Route::get('new-category', [CategoryController::class, 'new_category'])->name('new-category');
    Route::get('edit-category/{id}', [CategoryController::class, 'edit_category'])->name('edit-category');
    Route::post('update-category', [CategoryController::class, 'update_category'])->name('update-category');
    Route::post('store-category', [CategoryController::class, 'store_category'])->name('store-category');
    Route::delete('destroy-category/{id}', [CategoryController::class, 'destroy_category'])->name('destroy-category');

    Route::get('product', [ProductController::class, 'product'])->name('product');
    Route::get('new-product', [ProductController::class, 'new_product'])->name('new-product');
    Route::post('store-product', [ProductController::class, 'store_product'])->name('store-product');
    Route::delete('destroy-product/{id}', [ProductController::class, 'destroy_product'])->name('destroy-product');

    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');
    Route::get('edit-profile.{id}', [AuthController::class, 'edit_profile'])->name('edit-profile');
    Route::post('update-profile', [AuthController::class, 'update_profile'])->name('update-profile');

    Route::get('barcode', [ProductController::class, 'barcode'])->name('barcode');

    Route::post('consume', [TransactionController::class, 'consume'])->name('consume');
    Route::post('intake', [TransactionController::class, 'intake'])->name('intake');
    Route::get('transactions', [TransactionController::class, 'transactions'])->name('transactions');

    Route::post('products.byCategory/{id}', [ProductController::class, 'productsByCategory'])->name('products.byCategory');

    Route::get('/transactions/{id}/cheque', [ChequeController::class, 'printCheque'])->name('transactions.cheque');
});

Route::middleware(['guest'])->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('ProcessLogin', [AuthController::class, 'ProcessLogin'])->name('ProcessLogin');
});
