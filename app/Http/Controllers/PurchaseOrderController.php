<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderRequest;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'warehouse'])
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        return view('purchase-orders.create', [
            'suppliers' => $this->suppliers(),
            'warehouses' => $this->warehouses(),
        ]);
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        PurchaseOrder::create($request->validated() + [
            'created_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('purchase-orders.index')
            ->with('success', '進貨單已建立。');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'warehouse', 'creator']);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        return view('purchase-orders.edit', [
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => $this->suppliers(),
            'warehouses' => $this->warehouses(),
        ]);
    }

    public function update(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update($request->validated());

        return redirect()
            ->route('purchase-orders.index')
            ->with('success', '進貨單已更新。');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        return redirect()
            ->route('purchase-orders.index')
            ->with('success', '進貨單已刪除。');
    }

    private function suppliers()
    {
        return Supplier::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function warehouses()
    {
        return Warehouse::where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
