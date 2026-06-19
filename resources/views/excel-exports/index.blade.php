<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            匯出 Excel
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900">匯出中心</h3>
                    <p class="mt-1 text-sm text-gray-500">可匯出主檔清單與主要報表。輸出格式為 Excel 相容 `.xls`。</p>

                    <form method="POST" action="{{ route('excel-exports.store') }}" class="mt-6 space-y-6">
                        @csrf

                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="md:col-span-2">
                                <x-input-label for="export_type" value="匯出資料集" />
                                <select id="export_type" name="export_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach ($exportTypes as $value => $label)
                                        <option value="{{ $value }}" @selected(old('export_type') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('export_type')" />
                            </div>
                            <div>
                                <x-input-label for="keyword" value="關鍵字" />
                                <x-text-input id="keyword" name="keyword" type="text" class="mt-1 block w-full" :value="old('keyword')" placeholder="可留空" />
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-4">
                            <div>
                                <x-input-label for="type" value="商品類型" />
                                <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="all">全部</option>
                                    <option value="part" @selected(old('type') === 'part')>零件</option>
                                    <option value="vehicle" @selected(old('type') === 'vehicle')>整車</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="item_type" value="報表商品類型" />
                                <select id="item_type" name="item_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="all">全部</option>
                                    <option value="part" @selected(old('item_type') === 'part')>零件</option>
                                    <option value="vehicle" @selected(old('item_type') === 'vehicle')>整車</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="warehouse_id" value="倉庫" />
                                <select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">全部</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="is_active" value="啟用狀態" />
                                <select id="is_active" name="is_active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="all">全部</option>
                                    <option value="1" @selected(old('is_active') === '1')>啟用</option>
                                    <option value="0" @selected(old('is_active') === '0')>停用</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-4">
                            <div>
                                <x-input-label for="supplier_id" value="供應商" />
                                <select id="supplier_id" name="supplier_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">全部</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="customer_id" value="客戶" />
                                <select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">全部</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="start_date" value="起始日期" />
                                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date')" />
                            </div>
                            <div>
                                <x-input-label for="end_date" value="結束日期" />
                                <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date')" />
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>下載 Excel</x-primary-button>
                            <span class="text-sm text-gray-500">若某些篩選不適用於目前資料集，系統會自動忽略。</span>
                        </div>
                    </form>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900">匯出紀錄</h3>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">時間</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">資料集</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">檔名</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">資料筆數</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">操作人員</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">操作</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($logs as $log)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $exportTypes[$log->export_type] ?? $log->export_type }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $log->filename }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($log->row_count) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $log->creator?->name ?: '-' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                            <a href="{{ route('excel-exports.show', $log) }}" class="font-medium text-indigo-600 hover:text-indigo-900">查看</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">目前沒有匯出紀錄。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
