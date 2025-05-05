<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
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

Route::redirect('/', '/index');

Route::middleware(['auth'])->group(function () {

    Route::view('/index', 'pages.index')->name('index');
    Route::get('barcode', [ProductController::class, 'barcode'])->name('barcode');

    Route::resources([
        'brands' => BrandController::class,
        'categories' => CategoryController::class,
        'products' => ProductController::class,
    ]);

    Route::get('/products/by-barcode/{barcode}', [ProductController::class, 'getByBarcode']);

    Route::controller(AuthController::class)->group(function () {
        Route::get('logout', 'logout')->name('logout');
        Route::get('profile', 'profile')->name('profile');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update', 'update')->name('update');
    });

    Route::controller(TransactionController::class)->group(function () {
        Route::post('consume', 'consume')->name('consume');
        Route::post('intake', 'intake')->name('intake');
        Route::get('transactions', 'transactions')->name('transactions');
        Route::get('report', 'report')->name('admin.reports.index');
    });
    Route::view('/consumption' , 'pages.consumption')->name('consumption');
});

Route::middleware(['guest'])->controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('ProcessLogin', 'ProcessLogin')->name('ProcessLogin');
});
