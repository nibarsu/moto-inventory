<?php

use App\Http\Controllers\AverageCostController;
use App\Http\Controllers\BarcodeLabelController;
use App\Http\Controllers\BarcodeScanController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExcelExportController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\OwnerHistoryController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderItemController;
use App\Http\Controllers\PurchaseReceiptController;
use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\RepairOrderController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SalesOrderItemController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\SalesShipmentController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserAccessController;
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
    Route::middleware('permission:brands.manage')->group(function () {
        Route::resource('brands', BrandController::class);
    });

    Route::middleware('permission:categories.manage')->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    Route::middleware('permission:warehouses.manage')->group(function () {
        Route::resource('warehouses', WarehouseController::class);
    });

    Route::middleware('permission:suppliers.manage')->group(function () {
        Route::resource('suppliers', SupplierController::class);
    });

    Route::middleware('permission:customers.manage')->group(function () {
        Route::resource('customers', CustomerController::class);
    });

    Route::middleware('permission:parts.manage')->group(function () {
        Route::resource('parts', PartController::class);
    });

    Route::middleware('permission:vehicles.manage')->group(function () {
        Route::resource('vehicles', VehicleController::class);
    });

    Route::middleware('permission:stocks.manage')->group(function () {
        Route::get('average-costs', [AverageCostController::class, 'index'])->name('average-costs.index');
        Route::get('inventory-reports', [InventoryReportController::class, 'index'])->name('inventory-reports.index');
        Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
        Route::get('stock-movements', [StockController::class, 'movements'])->name('stock-movements.index');
        Route::get('stocks/adjust', [StockController::class, 'adjust'])->name('stocks.adjust');
        Route::post('stocks/adjust', [StockController::class, 'updateAdjustment'])->name('stocks.update-adjustment');
    });

    Route::middleware('permission:purchase.manage')->group(function () {
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::resource('purchase-orders.items', PurchaseOrderItemController::class)->except(['show']);
        Route::resource('purchase-receipts', PurchaseReceiptController::class)->except(['destroy']);
        Route::get('purchase-reports', [PurchaseReportController::class, 'index'])->name('purchase-reports.index');
    });

    Route::middleware('permission:sales.manage')->group(function () {
        Route::resource('sales-orders', SalesOrderController::class);
        Route::resource('sales-orders.items', SalesOrderItemController::class)->except(['show']);
        Route::resource('sales-shipments', SalesShipmentController::class)->except(['destroy']);
        Route::get('sales-reports', [SalesReportController::class, 'index'])->name('sales-reports.index');
    });

    Route::middleware('permission:repairs.manage')->group(function () {
        Route::resource('repair-orders', RepairOrderController::class);
        Route::resource('maintenance-records', MaintenanceRecordController::class);
        Route::get('owner-histories', [OwnerHistoryController::class, 'index'])->name('owner-histories.index');
    });

    Route::middleware('permission:finance.manage')->group(function () {
        Route::resource('accounts-receivable', ReceivableController::class);
        Route::resource('accounts-payable', PayableController::class);
    });

    Route::middleware('permission:barcode.manage')->group(function () {
        Route::get('barcode-labels', [BarcodeLabelController::class, 'index'])->name('barcode-labels.index');
        Route::post('barcode-labels/print', [BarcodeLabelController::class, 'print'])->name('barcode-labels.print');
        Route::get('barcode-scans', [BarcodeScanController::class, 'index'])->name('barcode-scans.index');
    });

    Route::middleware('permission:import.manage')->group(function () {
        Route::get('product-imports/template/{itemType}', [ProductImportController::class, 'template'])->name('product-imports.template');
        Route::resource('product-imports', ProductImportController::class)->only(['index', 'store', 'show']);
    });

    Route::middleware('permission:export.manage')->group(function () {
        Route::resource('excel-exports', ExcelExportController::class)->only(['index', 'store', 'show']);
    });

    Route::middleware('permission:permissions.manage')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::get('user-access', [UserAccessController::class, 'index'])->name('user-access.index');
        Route::get('user-access/{user}/edit', [UserAccessController::class, 'edit'])->name('user-access.edit');
        Route::put('user-access/{user}', [UserAccessController::class, 'update'])->name('user-access.update');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
