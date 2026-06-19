<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                保養紀錄明細
            </h2>
            <a href="{{ route('maintenance-records.edit', $maintenanceRecord) }}" class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition ease-in-out duration-150 hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900">
                編輯紀錄
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <dl class="grid gap-6 md:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">紀錄編號</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenanceRecord->record_no }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">保養日期</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenanceRecord->service_date->format('Y-m-d') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">客戶</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenanceRecord->customer?->name ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">車型</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenanceRecord->vehicle?->name ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">關聯工單</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenanceRecord->repairOrder?->wo_no ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">保養類型</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenanceRecord->service_type }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">車牌號碼</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenanceRecord->plate_no ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">目前里程</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenanceRecord->mileage !== null ? number_format($maintenanceRecord->mileage) : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">下次保養日期</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenanceRecord->next_service_date?->format('Y-m-d') ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">下次保養里程</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenanceRecord->next_service_mileage !== null ? number_format($maintenanceRecord->next_service_mileage) : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">建立者</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $maintenanceRecord->creator?->name ?: '-' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-8 space-y-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">保養內容</h3>
                            <div class="mt-2 rounded-md bg-gray-50 p-4 text-sm text-gray-900">{{ $maintenanceRecord->service_content ?: '-' }}</div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">備註</h3>
                            <div class="mt-2 rounded-md bg-gray-50 p-4 text-sm text-gray-900">{{ $maintenanceRecord->remark ?: '-' }}</div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('maintenance-records.index') }}" class="text-sm text-gray-600 hover:text-gray-900">返回保養紀錄列表</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
