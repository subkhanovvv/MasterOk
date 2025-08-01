<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConsumptionController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\IntakeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
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

    Route::get('/index', [IndexController::class, 'index'])->name('index');

    Route::resources([
        'brands' => BrandController::class,
        'categories' => CategoryController::class,
        'products' => ProductController::class,
        'suppliers' => SupplierController::class,
        // 'history' => HistoryController::class,
    ]);

    Route::controller(HistoryController::class)->group(function () {
        Route::get('history', 'index')->name('history.index');
        Route::get('/history/print/{id}', 'print')->name('history.print');
        Route::get('/history/{id}', 'show')->name('history.show');
        Route::patch('/history/{id}/status', 'updateStatus')->name('history.updateStatus');
    });

    Route::controller(BarcodeController::class)->group(function () {
        Route::get('/barcode/print/{id}', 'print')->name('barcode.print');
        Route::get('/barcode/download/{id}', 'download')->name('barcode.download');
        Route::get('/barcode/print-all',  'printAll')->name('barcode.printAll');
        Route::get('barcode', 'index')->name('barcode.index');
    });

    Route::controller(IntakeController::class)->group(function () {
        Route::get('/intake', 'index')->name('intake.index');
        Route::post('/intake/store', 'store')->name('intake.store');
    });

    Route::controller(ConsumptionController::class)->group(function () {
        Route::get('/consumption', 'index')->name('consumption.index');
        Route::post('/consumption/store', 'store')->name('consumption.store');
        Route::get('/consumption/{activity}/print', 'print')->name('consumption.print');
    });

    Route::controller(AuthController::class)->group(function () {
        Route::get('logout', 'logout')->name('logout');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile.update', 'update')->name('profile.update');
    });

    Route::controller(SettingController::class)->group(function () {
        Route::get('settings', 'index')->name('settings');
        Route::post('settings/update', 'update')->name('settings.update');
    });

    Route::controller(TransactionController::class)->group(function () {
        Route::get('/report', 'index')->name('report.index');
        Route::get('/report/export', 'export')->name('report.export');
    });
});

Route::middleware(['guest'])->controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('ProcessLogin', 'ProcessLogin')->name('ProcessLogin');
});
