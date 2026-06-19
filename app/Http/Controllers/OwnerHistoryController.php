<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerHistoryIndexRequest;
use App\Models\Customer;
use App\Models\MaintenanceRecord;
use App\Models\RepairOrder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OwnerHistoryController extends Controller
{
    public function index(OwnerHistoryIndexRequest $request)
    {
        $filters = $request->validated();
        $customerId = $filters['customer_id'] ?? null;
        $keyword = trim($filters['keyword'] ?? '');

        $items = collect()
            ->concat($this->repairItems($customerId, $keyword))
            ->concat($this->maintenanceItems($customerId, $keyword))
            ->sortByDesc(fn (object $item) => $item->sort_date.' '.$item->sort_time)
            ->values();

        $ownerHistories = $this->paginateCollection($items, 15)->appends($request->query());

        $selectedCustomer = $customerId ? Customer::find($customerId) : null;

        return view('owner-histories.index', [
            'ownerHistories' => $ownerHistories,
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'selectedCustomer' => $selectedCustomer,
            'customerId' => $customerId ? (string) $customerId : '',
            'keyword' => $keyword,
            'summary' => (object) [
                'repair_count' => $items->where('record_type', 'repair')->count(),
                'maintenance_count' => $items->where('record_type', 'maintenance')->count(),
                'total_count' => $items->count(),
            ],
        ]);
    }

    private function repairItems(?int $customerId, string $keyword): Collection
    {
        $query = RepairOrder::query()
            ->with(['customer', 'vehicle'])
            ->when($customerId, fn ($builder) => $builder->where('customer_id', $customerId))
            ->when($keyword !== '', function ($builder) use ($keyword) {
                $builder->where(function ($nested) use ($keyword) {
                    $nested->where('wo_no', 'like', "%{$keyword}%")
                        ->orWhere('plate_no', 'like', "%{$keyword}%")
                        ->orWhere('complaint', 'like', "%{$keyword}%")
                        ->orWhere('diagnosis', 'like', "%{$keyword}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($keyword) {
                            $customerQuery->where('name', 'like', "%{$keyword}%")
                                ->orWhere('phone', 'like', "%{$keyword}%")
                                ->orWhere('mobile', 'like', "%{$keyword}%");
                        });
                });
            })
            ->orderByDesc('order_date')
            ->orderByDesc('id');

        return $query->get()->map(function (RepairOrder $repairOrder) {
            return (object) [
                'record_type' => 'repair',
                'record_type_label' => '維修工單',
                'sort_date' => $repairOrder->order_date?->format('Y-m-d') ?? '',
                'sort_time' => str_pad((string) $repairOrder->id, 10, '0', STR_PAD_LEFT),
                'date' => $repairOrder->order_date?->format('Y-m-d') ?? '-',
                'number' => $repairOrder->wo_no,
                'customer' => $repairOrder->customer?->name ?: '-',
                'vehicle' => $repairOrder->vehicle?->name ?: '-',
                'plate_no' => $repairOrder->plate_no ?: '-',
                'mileage' => $repairOrder->mileage,
                'service_type' => $repairOrder->status,
                'summary' => $repairOrder->complaint ?: '-',
                'detail' => $repairOrder->diagnosis ?: '-',
                'link' => route('repair-orders.show', $repairOrder),
            ];
        });
    }

    private function maintenanceItems(?int $customerId, string $keyword): Collection
    {
        $query = MaintenanceRecord::query()
            ->with(['customer', 'vehicle', 'repairOrder'])
            ->when($customerId, fn ($builder) => $builder->where('customer_id', $customerId))
            ->when($keyword !== '', function ($builder) use ($keyword) {
                $builder->where(function ($nested) use ($keyword) {
                    $nested->where('record_no', 'like', "%{$keyword}%")
                        ->orWhere('plate_no', 'like', "%{$keyword}%")
                        ->orWhere('service_type', 'like', "%{$keyword}%")
                        ->orWhere('service_content', 'like', "%{$keyword}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($keyword) {
                            $customerQuery->where('name', 'like', "%{$keyword}%")
                                ->orWhere('phone', 'like', "%{$keyword}%")
                                ->orWhere('mobile', 'like', "%{$keyword}%");
                        });
                });
            })
            ->orderByDesc('service_date')
            ->orderByDesc('id');

        return $query->get()->map(function (MaintenanceRecord $maintenanceRecord) {
            return (object) [
                'record_type' => 'maintenance',
                'record_type_label' => '保養紀錄',
                'sort_date' => $maintenanceRecord->service_date?->format('Y-m-d') ?? '',
                'sort_time' => str_pad((string) $maintenanceRecord->id, 10, '0', STR_PAD_LEFT),
                'date' => $maintenanceRecord->service_date?->format('Y-m-d') ?? '-',
                'number' => $maintenanceRecord->record_no,
                'customer' => $maintenanceRecord->customer?->name ?: '-',
                'vehicle' => $maintenanceRecord->vehicle?->name ?: '-',
                'plate_no' => $maintenanceRecord->plate_no ?: '-',
                'mileage' => $maintenanceRecord->mileage,
                'service_type' => $maintenanceRecord->service_type,
                'summary' => $maintenanceRecord->service_content ?: '-',
                'detail' => $maintenanceRecord->repairOrder?->wo_no ? '關聯工單：'.$maintenanceRecord->repairOrder->wo_no : '-',
                'link' => route('maintenance-records.show', $maintenanceRecord),
            ];
        });
    }

    private function paginateCollection(Collection $items, int $perPage): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $results = $items->forPage($page, $perPage)->values();

        return new LengthAwarePaginator(
            $results,
            $items->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );
    }
}
