<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseReportIndexRequest;
use App\Models\PurchaseReceiptItem;
use App\Models\Supplier;
use App\Models\Warehouse;

class PurchaseReportController extends Controller
{
    public function index(PurchaseReportIndexRequest $request)
    {
        $filters = $request->validated();

        $startDate = $filters['start_date'] ?? '';
        $endDate = $filters['end_date'] ?? '';
        $supplierId = $filters['supplier_id'] ?? null;
        $warehouseId = $filters['warehouse_id'] ?? null;
        $itemType = $filters['item_type'] ?? 'all';
        $keyword = trim($filters['keyword'] ?? '');

        $query = PurchaseReceiptItem::query()
            ->select('purchase_receipt_items.*')
            ->join('purchase_receipts', 'purchase_receipts.id', '=', 'purchase_receipt_items.purchase_receipt_id')
            ->with(['purchaseReceipt.purchaseOrder', 'purchaseReceipt.supplier', 'purchaseReceipt.warehouse'])
            ->when($startDate !== '', fn ($builder) => $builder->whereDate('purchase_receipts.receipt_date', '>=', $startDate))
            ->when($endDate !== '', fn ($builder) => $builder->whereDate('purchase_receipts.receipt_date', '<=', $endDate))
            ->when($supplierId, fn ($builder) => $builder->where('purchase_receipts.supplier_id', $supplierId))
            ->when($warehouseId, fn ($builder) => $builder->where('purchase_receipts.warehouse_id', $warehouseId))
            ->when($itemType !== 'all', fn ($builder) => $builder->where('purchase_receipt_items.item_type', $itemType))
            ->when($keyword !== '', function ($builder) use ($keyword) {
                $builder->where(function ($nested) use ($keyword) {
                    $nested->where('purchase_receipt_items.item_code', 'like', "%{$keyword}%")
                        ->orWhere('purchase_receipt_items.item_name', 'like', "%{$keyword}%")
                        ->orWhereHas('purchaseReceipt', function ($receiptQuery) use ($keyword) {
                            $receiptQuery->where('receipt_no', 'like', "%{$keyword}%")
                                ->orWhereHas('purchaseOrder', fn ($orderQuery) => $orderQuery->where('po_no', 'like', "%{$keyword}%"));
                        });
                });
            });

        $summary = (object) [
            'total_lines' => (int) (clone $query)->count(),
            'total_quantity' => (int) (clone $query)->sum('purchase_receipt_items.quantity'),
            'total_amount' => (float) (clone $query)->sum('purchase_receipt_items.line_total'),
        ];

        $purchaseReports = $query
            ->orderByDesc('purchase_receipts.receipt_date')
            ->orderByDesc('purchase_receipts.id')
            ->orderBy('purchase_receipt_items.id')
            ->paginate(15)
            ->withQueryString();

        return view('purchase-reports.index', [
            'purchaseReports' => $purchaseReports,
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'supplierId' => $supplierId ? (string) $supplierId : '',
            'warehouseId' => $warehouseId ? (string) $warehouseId : '',
            'itemType' => $itemType,
            'keyword' => $keyword,
            'summary' => $summary,
        ]);
    }
}
