<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRepairOrderRequest;
use App\Http\Requests\UpdateRepairOrderRequest;
use App\Models\Customer;
use App\Models\RepairOrder;
use App\Models\Vehicle;

class RepairOrderController extends Controller
{
    public function index()
    {
        $repairOrders = RepairOrder::with(['customer', 'vehicle'])
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('repair-orders.index', compact('repairOrders'));
    }

    public function create()
    {
        return view('repair-orders.create', [
            'customers' => $this->customers(),
            'vehicles' => $this->vehicles(),
            'defaultWorkOrderNo' => $this->generateWorkOrderNo(),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function store(StoreRepairOrderRequest $request)
    {
        RepairOrder::create($request->validated() + [
            'created_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('repair-orders.index')
            ->with('success', '維修工單已建立。');
    }

    public function show(RepairOrder $repairOrder)
    {
        $repairOrder->load(['customer', 'vehicle', 'creator']);

        return view('repair-orders.show', [
            'repairOrder' => $repairOrder,
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function edit(RepairOrder $repairOrder)
    {
        return view('repair-orders.edit', [
            'repairOrder' => $repairOrder,
            'customers' => $this->customers(),
            'vehicles' => $this->vehicles(),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function update(UpdateRepairOrderRequest $request, RepairOrder $repairOrder)
    {
        $repairOrder->update($request->validated());

        return redirect()
            ->route('repair-orders.index')
            ->with('success', '維修工單已更新。');
    }

    public function destroy(RepairOrder $repairOrder)
    {
        $repairOrder->delete();

        return redirect()
            ->route('repair-orders.index')
            ->with('success', '維修工單已刪除。');
    }

    private function customers()
    {
        return Customer::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function vehicles()
    {
        return Vehicle::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function statusOptions(): array
    {
        return [
            'open' => '待處理',
            'in_progress' => '處理中',
            'completed' => '已完成',
            'cancelled' => '已取消',
        ];
    }

    private function generateWorkOrderNo(): string
    {
        $prefix = 'RO-'.now()->format('Ymd');
        $count = RepairOrder::where('wo_no', 'like', $prefix.'-%')->count() + 1;

        return sprintf('%s-%03d', $prefix, $count);
    }
}
