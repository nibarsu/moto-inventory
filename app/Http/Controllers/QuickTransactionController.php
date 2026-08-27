<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuickPurchaseRequest;
use App\Http\Requests\StoreQuickSaleRequest;
use App\Http\Requests\TransactionReportRequest;
use App\Models\PurchaseReceipt;
use App\Models\SalesShipment;
use App\Models\Warehouse;
use App\Support\QuickTransactionService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class QuickTransactionController extends Controller
{
    public function __construct(private readonly QuickTransactionService $service)
    {
    }

    public function createPurchase()
    {
        return view('quick-transactions.purchase-create', [
            'warehouses' => $this->warehouses(),
        ]);
    }

    public function storePurchase(StoreQuickPurchaseRequest $request)
    {
        $receipt = $this->service->createPurchase($this->sanitizeItems($request->validated()), $request->user());

        return redirect()
            ->route('quick-purchases.show', $receipt)
            ->with('success', '快速進貨單已建立。');
    }

    public function showPurchase(PurchaseReceipt $purchaseReceipt)
    {
        $purchaseReceipt->load(['purchaseOrder', 'supplier', 'warehouse', 'creator', 'items']);

        return view('quick-transactions.purchase-show', compact('purchaseReceipt'));
    }

    public function printPurchase(PurchaseReceipt $purchaseReceipt)
    {
        $purchaseReceipt->load(['supplier', 'warehouse', 'items']);

        return view('quick-transactions.purchase-print', compact('purchaseReceipt'));
    }

    public function createSale()
    {
        return view('quick-transactions.sale-create', [
            'warehouses' => $this->warehouses(),
        ]);
    }

    public function storeSale(StoreQuickSaleRequest $request)
    {
        $shipment = $this->service->createSale($this->sanitizeItems($request->validated()), $request->user());

        return redirect()
            ->route('quick-sales.show', $shipment)
            ->with('success', '快速出貨單已建立。');
    }

    public function showSale(SalesShipment $salesShipment)
    {
        $salesShipment->load(['salesOrder', 'customer', 'warehouse', 'creator', 'items']);

        return view('quick-transactions.sale-show', compact('salesShipment'));
    }

    public function printSale(SalesShipment $salesShipment)
    {
        $salesShipment->load(['customer', 'warehouse', 'items']);

        return view('quick-transactions.sale-print', compact('salesShipment'));
    }

    public function report(TransactionReportRequest $request)
    {
        $validated = $request->validated();
        $filters = [
            'party_name' => $validated['party_name'] ?? '',
            'type' => $validated['type'] ?? 'all',
            'date_from' => $validated['date_from'] ?? now()->subYear()->toDateString(),
            'date_to' => $validated['date_to'] ?? now()->toDateString(),
        ];

        $rows = $this->buildReportRows($filters);
        $page = max(1, (int) $request->integer('page', 1));
        $perPage = 15;
        $paginator = new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => route('transaction-reports.index'), 'query' => $filters]
        );

        return view('quick-transactions.report', [
            'filters' => $filters,
            'rows' => $paginator,
        ]);
    }

    public function supplierLookup()
    {
        return response()->json(
            $this->service->supplierSuggestions((string) request('keyword', ''))->values()
        );
    }

    public function customerLookup()
    {
        return response()->json(
            $this->service->customerSuggestions((string) request('keyword', ''))->values()
        );
    }

    public function productLookup()
    {
        return response()->json(
            $this->service->productSuggestions((string) request('keyword', ''))->values()
        );
    }

    private function warehouses()
    {
        return Warehouse::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function sanitizeItems(array $validated): array
    {
        $validated['items'] = collect($validated['items'])
            ->map(fn (array $item) => [
                'name' => trim((string) ($item['name'] ?? '')),
                'quantity' => $item['quantity'] ?? 0,
                'unit_price' => $item['unit_price'] ?? 0,
                'remark' => isset($item['remark']) ? trim((string) $item['remark']) : null,
            ])
            ->filter(fn (array $item) => $item['name'] !== '')
            ->values()
            ->all();

        return $validated;
    }

    private function buildReportRows(array $filters): Collection
    {
        $partyName = trim((string) $filters['party_name']);

        $purchaseRows = PurchaseReceipt::query()
            ->with(['supplier', 'warehouse'])
            ->whereBetween('receipt_date', [$filters['date_from'], $filters['date_to']])
            ->when($partyName !== '', function ($query) use ($partyName) {
                $query->whereHas('supplier', fn ($supplierQuery) => $supplierQuery->where('name', 'like', '%'.$partyName.'%'));
            })
            ->get()
            ->map(fn (PurchaseReceipt $receipt) => [
                'type' => 'purchase',
                'type_label' => '進貨',
                'date' => optional($receipt->receipt_date)->format('Y-m-d'),
                'doc_no' => $receipt->receipt_no,
                'party_name' => $receipt->supplier?->name ?? '-',
                'warehouse_name' => $receipt->warehouse?->name ?? '-',
                'total_amount' => (float) $receipt->total_amount,
                'show_url' => route('quick-purchases.show', $receipt),
                'print_url' => route('quick-purchases.print', $receipt),
            ]);

        $salesRows = SalesShipment::query()
            ->with(['customer', 'warehouse'])
            ->whereBetween('shipment_date', [$filters['date_from'], $filters['date_to']])
            ->when($partyName !== '', function ($query) use ($partyName) {
                $query->whereHas('customer', fn ($customerQuery) => $customerQuery->where('name', 'like', '%'.$partyName.'%'));
            })
            ->get()
            ->map(fn (SalesShipment $shipment) => [
                'type' => 'sale',
                'type_label' => '出貨',
                'date' => optional($shipment->shipment_date)->format('Y-m-d'),
                'doc_no' => $shipment->shipment_no,
                'party_name' => $shipment->customer?->name ?? '-',
                'warehouse_name' => $shipment->warehouse?->name ?? '-',
                'total_amount' => (float) $shipment->total_amount,
                'show_url' => route('quick-sales.show', $shipment),
                'print_url' => route('quick-sales.print', $shipment),
            ]);

        return $purchaseRows
            ->merge($salesRows)
            ->filter(function (array $row) use ($filters) {
                if ($filters['type'] === 'purchase') {
                    return $row['type'] === 'purchase';
                }

                if ($filters['type'] === 'sale') {
                    return $row['type'] === 'sale';
                }

                return true;
            })
            ->sortByDesc(fn (array $row) => $row['date'].'-'.$row['doc_no'])
            ->values();
    }
}
