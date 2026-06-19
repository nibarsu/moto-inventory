<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExcelExportRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\ExcelExportLog;
use App\Models\Part;
use App\Models\PartStock;
use App\Models\PurchaseReceiptItem;
use App\Models\SalesShipmentItem;
use App\Models\Supplier;
use App\Models\Vehicle;
use App\Models\VehicleStock;
use App\Models\Warehouse;
use App\Support\SpreadsheetXmlExporter;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExportController extends Controller
{
    public function index()
    {
        return view('excel-exports.index', [
            'logs' => ExcelExportLog::query()->with('creator')->latest()->paginate(10),
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(),
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'exportTypes' => $this->exportTypes(),
        ]);
    }

    public function store(StoreExcelExportRequest $request): StreamedResponse
    {
        $filters = $request->validated();
        $exportType = $filters['export_type'];
        $dataset = $this->dataset($exportType, $filters);
        $filename = $exportType.'-'.now()->format('Ymd-His').'.xls';

        ExcelExportLog::create([
            'export_type' => $exportType,
            'filename' => $filename,
            'row_count' => count($dataset['rows']),
            'filter_summary' => $this->filterSummary($filters),
            'created_by' => Auth::id(),
        ]);

        $xml = SpreadsheetXmlExporter::make($dataset['sheet'], $dataset['headers'], $dataset['rows']);

        return response()->streamDownload(
            static function () use ($xml): void {
                echo $xml;
            },
            $filename,
            [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            ]
        );
    }

    public function show(ExcelExportLog $excelExport)
    {
        $excelExport->load('creator');

        return view('excel-exports.show', [
            'log' => $excelExport,
            'typeLabels' => $this->exportTypes(),
        ]);
    }

    /**
     * @param array<string, mixed> $filters
     * @return array{sheet:string,headers:array<int,string>,rows:array<int,array<int,scalar|null>>}
     */
    private function dataset(string $type, array $filters): array
    {
        return match ($type) {
            'brands' => $this->brandDataset(),
            'categories' => $this->categoryDataset(),
            'parts' => $this->partDataset(),
            'vehicles' => $this->vehicleDataset(),
            'customers' => $this->customerDataset(),
            'suppliers' => $this->supplierDataset(),
            'warehouses' => $this->warehouseDataset(),
            'inventory_report' => $this->inventoryReportDataset($filters),
            'purchase_report' => $this->purchaseReportDataset($filters),
            'sales_report' => $this->salesReportDataset($filters),
            default => throw new \InvalidArgumentException('Unsupported export type.'),
        };
    }

    private function brandDataset(): array
    {
        $rows = Brand::query()->orderBy('code')->get()->map(fn (Brand $brand) => [
            $brand->code,
            $brand->name,
            $brand->english_name,
            $brand->remark,
            $brand->is_active ? '啟用' : '停用',
        ])->all();

        return ['sheet' => 'Brands', 'headers' => ['品牌代碼', '品牌名稱', '英文名稱', '備註', '狀態'], 'rows' => $rows];
    }

    private function categoryDataset(): array
    {
        $rows = Category::query()->orderBy('code')->get()->map(fn (Category $category) => [
            $category->code,
            $category->name,
            $category->type === 'part' ? '零件' : '整車',
            $category->remark,
            $category->is_active ? '啟用' : '停用',
        ])->all();

        return ['sheet' => 'Categories', 'headers' => ['分類代碼', '分類名稱', '類型', '備註', '狀態'], 'rows' => $rows];
    }

    private function partDataset(): array
    {
        $rows = Part::query()->with(['brand', 'category'])->orderBy('part_no')->get()->map(fn (Part $part) => [
            $part->part_no,
            $part->barcode,
            $part->name,
            $part->brand?->name,
            $part->category?->name,
            $part->unit,
            (float) $part->last_cost_price,
            (float) $part->average_cost_price,
            (float) $part->sale_price,
            (int) $part->safety_stock,
            $part->remark,
            $part->is_active ? '啟用' : '停用',
        ])->all();

        return ['sheet' => 'Parts', 'headers' => ['料號', '條碼', '商品名稱', '品牌', '分類', '單位', '最近成本', '平均成本', '售價', '安全庫存', '備註', '狀態'], 'rows' => $rows];
    }

    private function vehicleDataset(): array
    {
        $rows = Vehicle::query()->with(['brand', 'category'])->orderBy('model_code')->get()->map(fn (Vehicle $vehicle) => [
            $vehicle->model_code,
            $vehicle->barcode,
            $vehicle->name,
            $vehicle->brand?->name,
            $vehicle->category?->name,
            $vehicle->year,
            $vehicle->color,
            $vehicle->engine_displacement,
            (float) $vehicle->last_cost_price,
            (float) $vehicle->average_cost_price,
            (float) $vehicle->sale_price,
            $vehicle->remark,
            $vehicle->is_active ? '啟用' : '停用',
        ])->all();

        return ['sheet' => 'Vehicles', 'headers' => ['車型代碼', '條碼', '車名', '品牌', '分類', '年份', '顏色', '排氣量', '最近成本', '平均成本', '售價', '備註', '狀態'], 'rows' => $rows];
    }

    private function customerDataset(): array
    {
        $rows = Customer::query()->orderBy('code')->get()->map(fn (Customer $customer) => [
            $customer->code,
            $customer->name,
            $customer->phone,
            $customer->mobile,
            $customer->email,
            $customer->address,
            $customer->tax_id,
            $customer->remark,
            $customer->is_active ? '啟用' : '停用',
        ])->all();

        return ['sheet' => 'Customers', 'headers' => ['客戶代碼', '客戶名稱', '電話', '手機', 'Email', '地址', '統編', '備註', '狀態'], 'rows' => $rows];
    }

    private function supplierDataset(): array
    {
        $rows = Supplier::query()->orderBy('code')->get()->map(fn (Supplier $supplier) => [
            $supplier->code,
            $supplier->name,
            $supplier->tax_id,
            $supplier->contact_person,
            $supplier->phone,
            $supplier->mobile,
            $supplier->email,
            $supplier->address,
            $supplier->remark,
            $supplier->is_active ? '啟用' : '停用',
        ])->all();

        return ['sheet' => 'Suppliers', 'headers' => ['供應商代碼', '供應商名稱', '統編', '聯絡人', '電話', '手機', 'Email', '地址', '備註', '狀態'], 'rows' => $rows];
    }

    private function warehouseDataset(): array
    {
        $rows = Warehouse::query()->orderBy('code')->get()->map(fn (Warehouse $warehouse) => [
            $warehouse->code,
            $warehouse->name,
            $warehouse->address,
            $warehouse->contact_person,
            $warehouse->phone,
            $warehouse->remark,
            $warehouse->is_active ? '啟用' : '停用',
        ])->all();

        return ['sheet' => 'Warehouses', 'headers' => ['倉庫代碼', '倉庫名稱', '地址', '聯絡人', '電話', '備註', '狀態'], 'rows' => $rows];
    }

    private function inventoryReportDataset(array $filters): array
    {
        $type = $filters['type'] ?? 'all';
        $warehouseId = $filters['warehouse_id'] ?? null;
        $isActive = $filters['is_active'] ?? 'all';
        $keyword = trim((string) ($filters['keyword'] ?? ''));
        $rows = [];

        if (in_array($type, ['all', 'part'], true)) {
            $partRows = PartStock::query()
                ->with(['part.brand', 'part.category', 'warehouse'])
                ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
                ->when($keyword !== '', fn ($q) => $q->whereHas('part', fn ($b) => $b->where('part_no', 'like', "%{$keyword}%")->orWhere('name', 'like', "%{$keyword}%")))
                ->when($isActive !== 'all', fn ($q) => $q->whereHas('part', fn ($b) => $b->where('is_active', $isActive === '1')))
                ->orderBy('part_id')
                ->orderBy('warehouse_id')
                ->get()
                ->map(function (PartStock $stock) {
                    $part = $stock->part;
                    $averageCost = (float) ($part?->average_cost_price ?? 0);
                    $salePrice = (float) ($part?->sale_price ?? 0);

                    return ['零件', $part?->part_no, $part?->name, $part?->brand?->name, $part?->category?->name, $stock->warehouse?->name, (int) $stock->quantity, $averageCost, $salePrice, round($stock->quantity * $averageCost, 2), round($stock->quantity * $salePrice, 2), ($part?->is_active ?? false) ? '啟用' : '停用'];
                })->all();
            $rows = array_merge($rows, $partRows);
        }

        if (in_array($type, ['all', 'vehicle'], true)) {
            $vehicleRows = VehicleStock::query()
                ->with(['vehicle.brand', 'vehicle.category', 'warehouse'])
                ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
                ->when($keyword !== '', fn ($q) => $q->whereHas('vehicle', fn ($b) => $b->where('model_code', 'like', "%{$keyword}%")->orWhere('name', 'like', "%{$keyword}%")))
                ->when($isActive !== 'all', fn ($q) => $q->whereHas('vehicle', fn ($b) => $b->where('is_active', $isActive === '1')))
                ->orderBy('vehicle_id')
                ->orderBy('warehouse_id')
                ->get()
                ->map(function (VehicleStock $stock) {
                    $vehicle = $stock->vehicle;
                    $averageCost = (float) ($vehicle?->average_cost_price ?? 0);
                    $salePrice = (float) ($vehicle?->sale_price ?? 0);

                    return ['整車', $vehicle?->model_code, $vehicle?->name, $vehicle?->brand?->name, $vehicle?->category?->name, $stock->warehouse?->name, (int) $stock->quantity, $averageCost, $salePrice, round($stock->quantity * $averageCost, 2), round($stock->quantity * $salePrice, 2), ($vehicle?->is_active ?? false) ? '啟用' : '停用'];
                })->all();
            $rows = array_merge($rows, $vehicleRows);
        }

        return ['sheet' => 'InventoryReport', 'headers' => ['類型', '代碼', '名稱', '品牌', '分類', '倉庫', '庫存數量', '平均成本', '售價', '庫存成本金額', '庫存售價金額', '狀態'], 'rows' => $rows];
    }

    private function purchaseReportDataset(array $filters): array
    {
        $startDate = $filters['start_date'] ?? '';
        $endDate = $filters['end_date'] ?? '';
        $supplierId = $filters['supplier_id'] ?? null;
        $warehouseId = $filters['warehouse_id'] ?? null;
        $itemType = $filters['item_type'] ?? 'all';
        $keyword = trim((string) ($filters['keyword'] ?? ''));

        $rows = PurchaseReceiptItem::query()
            ->select('purchase_receipt_items.*')
            ->join('purchase_receipts', 'purchase_receipts.id', '=', 'purchase_receipt_items.purchase_receipt_id')
            ->with(['purchaseReceipt.purchaseOrder', 'purchaseReceipt.supplier', 'purchaseReceipt.warehouse'])
            ->when($startDate !== '', fn ($q) => $q->whereDate('purchase_receipts.receipt_date', '>=', $startDate))
            ->when($endDate !== '', fn ($q) => $q->whereDate('purchase_receipts.receipt_date', '<=', $endDate))
            ->when($supplierId, fn ($q) => $q->where('purchase_receipts.supplier_id', $supplierId))
            ->when($warehouseId, fn ($q) => $q->where('purchase_receipts.warehouse_id', $warehouseId))
            ->when($itemType !== 'all', fn ($q) => $q->where('purchase_receipt_items.item_type', $itemType))
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where(function ($nested) use ($keyword) {
                    $nested->where('purchase_receipt_items.item_code', 'like', "%{$keyword}%")
                        ->orWhere('purchase_receipt_items.item_name', 'like', "%{$keyword}%")
                        ->orWhereHas('purchaseReceipt', function ($receiptQuery) use ($keyword) {
                            $receiptQuery->where('receipt_no', 'like', "%{$keyword}%")
                                ->orWhereHas('purchaseOrder', fn ($orderQuery) => $orderQuery->where('po_no', 'like', "%{$keyword}%"));
                        });
                });
            })
            ->orderByDesc('purchase_receipts.receipt_date')
            ->orderByDesc('purchase_receipts.id')
            ->orderBy('purchase_receipt_items.id')
            ->get()
            ->map(fn (PurchaseReceiptItem $item) => [
                $item->purchaseReceipt?->receipt_no,
                $item->purchaseReceipt?->purchaseOrder?->po_no,
                $item->purchaseReceipt?->receipt_date?->format('Y-m-d'),
                $item->purchaseReceipt?->supplier?->name,
                $item->purchaseReceipt?->warehouse?->name,
                $item->item_type === 'part' ? '零件' : '整車',
                $item->item_code,
                $item->item_name,
                (int) $item->quantity,
                (float) $item->unit_cost,
                (float) $item->line_total,
                $item->remark,
            ])->all();

        return ['sheet' => 'PurchaseReport', 'headers' => ['進貨單號', '採購單號', '進貨日期', '供應商', '倉庫', '類型', '商品代碼', '商品名稱', '數量', '單價', '金額', '備註'], 'rows' => $rows];
    }

    private function salesReportDataset(array $filters): array
    {
        $startDate = $filters['start_date'] ?? '';
        $endDate = $filters['end_date'] ?? '';
        $customerId = $filters['customer_id'] ?? null;
        $warehouseId = $filters['warehouse_id'] ?? null;
        $itemType = $filters['item_type'] ?? 'all';
        $keyword = trim((string) ($filters['keyword'] ?? ''));

        $rows = SalesShipmentItem::query()
            ->select('sales_shipment_items.*')
            ->join('sales_shipments', 'sales_shipments.id', '=', 'sales_shipment_items.sales_shipment_id')
            ->with(['salesShipment.salesOrder', 'salesShipment.customer', 'salesShipment.warehouse'])
            ->when($startDate !== '', fn ($q) => $q->whereDate('sales_shipments.shipment_date', '>=', $startDate))
            ->when($endDate !== '', fn ($q) => $q->whereDate('sales_shipments.shipment_date', '<=', $endDate))
            ->when($customerId, fn ($q) => $q->where('sales_shipments.customer_id', $customerId))
            ->when($warehouseId, fn ($q) => $q->where('sales_shipments.warehouse_id', $warehouseId))
            ->when($itemType !== 'all', fn ($q) => $q->where('sales_shipment_items.item_type', $itemType))
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where(function ($nested) use ($keyword) {
                    $nested->where('sales_shipment_items.item_code', 'like', "%{$keyword}%")
                        ->orWhere('sales_shipment_items.item_name', 'like', "%{$keyword}%")
                        ->orWhereHas('salesShipment', function ($shipmentQuery) use ($keyword) {
                            $shipmentQuery->where('shipment_no', 'like', "%{$keyword}%")
                                ->orWhereHas('salesOrder', fn ($orderQuery) => $orderQuery->where('so_no', 'like', "%{$keyword}%"));
                        });
                });
            })
            ->orderByDesc('sales_shipments.shipment_date')
            ->orderByDesc('sales_shipments.id')
            ->orderBy('sales_shipment_items.id')
            ->get()
            ->map(fn (SalesShipmentItem $item) => [
                $item->salesShipment?->shipment_no,
                $item->salesShipment?->salesOrder?->so_no,
                $item->salesShipment?->shipment_date?->format('Y-m-d'),
                $item->salesShipment?->customer?->name,
                $item->salesShipment?->warehouse?->name,
                $item->item_type === 'part' ? '零件' : '整車',
                $item->item_code,
                $item->item_name,
                (int) $item->quantity,
                (float) $item->unit_price,
                (float) $item->line_total,
                $item->remark,
            ])->all();

        return ['sheet' => 'SalesReport', 'headers' => ['出貨單號', '銷貨單號', '出貨日期', '客戶', '倉庫', '類型', '商品代碼', '商品名稱', '數量', '單價', '金額', '備註'], 'rows' => $rows];
    }

    private function exportTypes(): array
    {
        return [
            'brands' => '品牌清單',
            'categories' => '分類清單',
            'parts' => '零件商品',
            'vehicles' => '整車商品',
            'customers' => '客戶清單',
            'suppliers' => '供應商清單',
            'warehouses' => '倉庫清單',
            'inventory_report' => '庫存報表',
            'purchase_report' => '進貨報表',
            'sales_report' => '銷貨報表',
        ];
    }

    private function filterSummary(array $filters): array
    {
        return array_filter([
            'type' => $filters['type'] ?? null,
            'warehouse_id' => $filters['warehouse_id'] ?? null,
            'supplier_id' => $filters['supplier_id'] ?? null,
            'customer_id' => $filters['customer_id'] ?? null,
            'item_type' => $filters['item_type'] ?? null,
            'is_active' => $filters['is_active'] ?? null,
            'keyword' => $filters['keyword'] ?? null,
            'start_date' => $filters['start_date'] ?? null,
            'end_date' => $filters['end_date'] ?? null,
        ], static fn ($value) => $value !== null && $value !== '' && $value !== 'all');
    }
}
