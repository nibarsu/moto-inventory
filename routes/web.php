<?php

use App\Http\Controllers\AverageCostController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\OwnerHistoryController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderItemController;
use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\PurchaseReceiptController;
use App\Http\Controllers\RepairOrderController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\SalesOrderItemController;
use App\Http\Controllers\SalesShipmentController;
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
    Route::get('inventory-reports', [InventoryReportController::class, 'index'])->name('inventory-reports.index');
    Route::resource('accounts-receivable', ReceivableController::class);
    Route::resource('accounts-payable', PayableController::class);
    Route::resource('maintenance-records', MaintenanceRecordController::class);
    Route::get('owner-histories', [OwnerHistoryController::class, 'index'])->name('owner-histories.index');
    Route::resource('parts', PartController::class);
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::resource('purchase-orders.items', PurchaseOrderItemController::class)->except(['show']);
    Route::get('purchase-reports', [PurchaseReportController::class, 'index'])->name('purchase-reports.index');
    Route::resource('purchase-receipts', PurchaseReceiptController::class)->except(['destroy']);
    Route::resource('repair-orders', RepairOrderController::class);
    Route::resource('sales-orders', SalesOrderController::class);
    Route::resource('sales-orders.items', SalesOrderItemController::class)->except(['show']);
    Route::get('sales-reports', [SalesReportController::class, 'index'])->name('sales-reports.index');
    Route::resource('sales-shipments', SalesShipmentController::class)->except(['destroy']);
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
