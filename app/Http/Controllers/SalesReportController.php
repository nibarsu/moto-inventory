<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesReportIndexRequest;
use App\Models\Customer;
use App\Models\SalesShipmentItem;
use App\Models\Warehouse;

class SalesReportController extends Controller
{
    public function index(SalesReportIndexRequest $request)
    {
        $filters = $request->validated();

        $startDate = $filters['start_date'] ?? '';
        $endDate = $filters['end_date'] ?? '';
        $customerId = $filters['customer_id'] ?? null;
        $warehouseId = $filters['warehouse_id'] ?? null;
        $itemType = $filters['item_type'] ?? 'all';
        $keyword = trim($filters['keyword'] ?? '');

        $query = SalesShipmentItem::query()
            ->select('sales_shipment_items.*')
            ->join('sales_shipments', 'sales_shipments.id', '=', 'sales_shipment_items.sales_shipment_id')
            ->with(['salesShipment.salesOrder', 'salesShipment.customer', 'salesShipment.warehouse'])
            ->when($startDate !== '', fn ($builder) => $builder->whereDate('sales_shipments.shipment_date', '>=', $startDate))
            ->when($endDate !== '', fn ($builder) => $builder->whereDate('sales_shipments.shipment_date', '<=', $endDate))
            ->when($customerId, fn ($builder) => $builder->where('sales_shipments.customer_id', $customerId))
            ->when($warehouseId, fn ($builder) => $builder->where('sales_shipments.warehouse_id', $warehouseId))
            ->when($itemType !== 'all', fn ($builder) => $builder->where('sales_shipment_items.item_type', $itemType))
            ->when($keyword !== '', function ($builder) use ($keyword) {
                $builder->where(function ($nested) use ($keyword) {
                    $nested->where('sales_shipment_items.item_code', 'like', "%{$keyword}%")
                        ->orWhere('sales_shipment_items.item_name', 'like', "%{$keyword}%")
                        ->orWhereHas('salesShipment', function ($shipmentQuery) use ($keyword) {
                            $shipmentQuery->where('shipment_no', 'like', "%{$keyword}%")
                                ->orWhereHas('salesOrder', fn ($orderQuery) => $orderQuery->where('so_no', 'like', "%{$keyword}%"));
                        });
                });
            });

        $summary = (object) [
            'total_lines' => (int) (clone $query)->count(),
            'total_quantity' => (int) (clone $query)->sum('sales_shipment_items.quantity'),
            'total_amount' => (float) (clone $query)->sum('sales_shipment_items.line_total'),
        ];

        $salesReports = $query
            ->orderByDesc('sales_shipments.shipment_date')
            ->orderByDesc('sales_shipments.id')
            ->orderBy('sales_shipment_items.id')
            ->paginate(15)
            ->withQueryString();

        return view('sales-reports.index', [
            'salesReports' => $salesReports,
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'customerId' => $customerId ? (string) $customerId : '',
            'warehouseId' => $warehouseId ? (string) $warehouseId : '',
            'itemType' => $itemType,
            'keyword' => $keyword,
            'summary' => $summary,
        ]);
    }
}
