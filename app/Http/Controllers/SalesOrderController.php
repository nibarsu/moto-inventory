<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesOrderRequest;
use App\Http\Requests\UpdateSalesOrderRequest;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\Warehouse;

class SalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::with(['customer', 'warehouse'])
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('sales-orders.index', compact('salesOrders'));
    }

    public function create()
    {
        return view('sales-orders.create', [
            'customers' => $this->customers(),
            'warehouses' => $this->warehouses(),
        ]);
    }

    public function store(StoreSalesOrderRequest $request)
    {
        SalesOrder::create($request->validated() + [
            'created_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('sales-orders.index')
            ->with('success', '銷貨單已新增。');
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'warehouse', 'creator', 'items']);

        return view('sales-orders.show', compact('salesOrder'));
    }

    public function edit(SalesOrder $salesOrder)
    {
        return view('sales-orders.edit', [
            'salesOrder' => $salesOrder,
            'customers' => $this->customers(),
            'warehouses' => $this->warehouses(),
        ]);
    }

    public function update(UpdateSalesOrderRequest $request, SalesOrder $salesOrder)
    {
        $salesOrder->update($request->validated());

        return redirect()
            ->route('sales-orders.index')
            ->with('success', '銷貨單已更新。');
    }

    public function destroy(SalesOrder $salesOrder)
    {
        $salesOrder->delete();

        return redirect()
            ->route('sales-orders.index')
            ->with('success', '銷貨單已刪除。');
    }

    private function customers()
    {
        return Customer::where('is_active', true)
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
