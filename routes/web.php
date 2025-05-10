<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;
use App\Models\Product;
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

    Route::resources([
        'brands' => BrandController::class,
        'barcode' => BarcodeController::class,
        'categories' => CategoryController::class,
        'products' => ProductController::class,
        'suppliers' => SupplierController::class,
        'history' => HistoryController::class,
    ]);

    Route::controller(BarcodeController::class)->group(function () {
        Route::get('/barcode/print/{id}', [BarcodeController::class, 'print'])->name('barcode.print');
        Route::get('/barcode/download/{id}', [BarcodeController::class, 'download'])->name('barcode.download');
    });

    Route::controller(AuthController::class)->group(function () {
        Route::get('logout', 'logout')->name('logout');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile.update', 'update')->name('profile.update');
    });

    Route::prefix('intake')->group(function () {
        Route::get('/', [TransactionController::class, 'intakeIndex'])->name('intake.index');
        Route::post('/add', [TransactionController::class, 'intakeAdd'])->name('intake.add');
        Route::get('/remove/{index}', [TransactionController::class, 'intakeRemove'])->name('intake.remove');
        Route::post('/store', [TransactionController::class, 'intakeStore'])->name('intake.store');
        Route::get('/history', [TransactionController::class, 'intakeHistory'])->name('intake.history');
    });

    Route::controller(TransactionController::class)->group(function () {
        Route::get('consumption', 'consumption')->name('consumption');
        Route::get('consumption.products', 'getProducts')->name('consumption.products');
        Route::post('consumption.store', 'store')->name('consumption.store');
        Route::post('consumption.create', 'create')->name('consumption.create');
        Route::post('consumption.remove/{id}', 'remove')->name('consumption.remove');
        Route::post('consumption.add', 'add')->name('consumption.add');
        Route::post('consumption.history', 'history')->name('consumption.history');
        Route::post('consumption.show/{id}', 'show')->name('consumption.show');
        Route::post('consumption/{id}/print', 'print')->name('consumption.print');

        Route::get('report', 'report')->name('admin.reports.index');
    });
});

Route::middleware(['guest'])->controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('ProcessLogin', 'ProcessLogin')->name('ProcessLogin');
});
