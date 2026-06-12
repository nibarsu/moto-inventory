<?php

use App\Http\Controllers\AverageCostController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderItemController;
use App\Http\Controllers\PurchaseReceiptController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('average-costs', [AverageCostController::class, 'index'])->name('average-costs.index');
    Route::resource('brands', BrandController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('parts', PartController::class);
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::resource('purchase-orders.items', PurchaseOrderItemController::class)->except(['show']);
    Route::resource('purchase-receipts', PurchaseReceiptController::class)->except(['destroy']);
    Route::resource('sales-orders', SalesOrderController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('stock-movements', [StockController::class, 'movements'])->name('stock-movements.index');
    Route::get('stocks/adjust', [StockController::class, 'adjust'])->name('stocks.adjust');
    Route::post('stocks/adjust', [StockController::class, 'updateAdjustment'])->name('stocks.update-adjustment');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
