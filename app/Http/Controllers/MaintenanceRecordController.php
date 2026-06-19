<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceRecordRequest;
use App\Http\Requests\UpdateMaintenanceRecordRequest;
use App\Models\Customer;
use App\Models\MaintenanceRecord;
use App\Models\RepairOrder;
use App\Models\Vehicle;

class MaintenanceRecordController extends Controller
{
    public function index()
    {
        $maintenanceRecords = MaintenanceRecord::with(['customer', 'vehicle', 'repairOrder'])
            ->orderByDesc('service_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('maintenance-records.index', compact('maintenanceRecords'));
    }

    public function create()
    {
        return view('maintenance-records.create', [
            'customers' => $this->customers(),
            'vehicles' => $this->vehicles(),
            'repairOrders' => $this->repairOrders(),
            'defaultRecordNo' => $this->generateRecordNo(),
        ]);
    }

    public function store(StoreMaintenanceRecordRequest $request)
    {
        MaintenanceRecord::create($request->validated() + [
            'created_by' => $request->user()?->id,
        ]);

        return redirect()
            ->route('maintenance-records.index')
            ->with('success', '保養紀錄已建立。');
    }

    public function show(MaintenanceRecord $maintenanceRecord)
    {
        $maintenanceRecord->load(['customer', 'vehicle', 'repairOrder', 'creator']);

        return view('maintenance-records.show', compact('maintenanceRecord'));
    }

    public function edit(MaintenanceRecord $maintenanceRecord)
    {
        return view('maintenance-records.edit', [
            'maintenanceRecord' => $maintenanceRecord,
            'customers' => $this->customers(),
            'vehicles' => $this->vehicles(),
            'repairOrders' => $this->repairOrders(),
        ]);
    }

    public function update(UpdateMaintenanceRecordRequest $request, MaintenanceRecord $maintenanceRecord)
    {
        $maintenanceRecord->update($request->validated());

        return redirect()
            ->route('maintenance-records.index')
            ->with('success', '保養紀錄已更新。');
    }

    public function destroy(MaintenanceRecord $maintenanceRecord)
    {
        $maintenanceRecord->delete();

        return redirect()
            ->route('maintenance-records.index')
            ->with('success', '保養紀錄已刪除。');
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

    private function repairOrders()
    {
        return RepairOrder::with('customer')
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->get();
    }

    private function generateRecordNo(): string
    {
        $prefix = 'MR-'.now()->format('Ymd');
        $count = MaintenanceRecord::where('record_no', 'like', $prefix.'-%')->count() + 1;

        return sprintf('%s-%03d', $prefix, $count);
    }
}
