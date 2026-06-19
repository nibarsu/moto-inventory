<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            銷貨報表
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('sales-reports.index') }}" class="grid gap-4 md:grid-cols-6 md:items-end">
                        <div>
                            <x-input-label for="start_date" value="開始日期" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="$startDate" />
                        </div>

                        <div>
                            <x-input-label for="end_date" value="結束日期" />
                            <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="$endDate" />
                        </div>

                        <div>
                            <x-input-label for="customer_id" value="客戶" />
                            <select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">全部客戶</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected($customerId === (string) $customer->id)>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="warehouse_id" value="倉庫" />
                            <select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">全部倉庫</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" @selected($warehouseId === (string) $warehouse->id)>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="item_type" value="類型" />
                            <select id="item_type" name="item_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="all" @selected($itemType === 'all')>全部</option>
                                <option value="part" @selected($itemType === 'part')>零件</option>
                                <option value="vehicle" @selected($itemType === 'vehicle')>整車</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="keyword" value="關鍵字" />
                            <x-text-input id="keyword" name="keyword" type="text" class="mt-1 block w-full" :value="$keyword" placeholder="出庫單號、銷貨單號、料號、商品名稱" />
                        </div>

                        <div class="md:col-span-6 flex flex-wrap gap-3">
                            <x-primary-button>查詢</x-primary-button>
                            <a href="{{ route('sales-reports.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                                清除
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">筆數</div>
                        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($summary->total_lines) }}</div>
                    </div>
                </div>
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">銷貨數量</div>
                        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($summary->total_quantity) }}</div>
                    </div>
                </div>
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">銷貨金額</div>
                        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($summary->total_amount, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">出庫日期</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">出庫單號</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">銷貨單號</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">客戶</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">倉庫</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">類型</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">料號 / 車型代碼</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">商品名稱</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">數量</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">單價</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">金額</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($salesReports as $line)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $line->salesShipment?->shipment_date?->format('Y-m-d') ?: '-' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $line->salesShipment?->shipment_no ?: '-' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $line->salesShipment?->salesOrder?->so_no ?: '-' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $line->salesShipment?->customer?->name ?: '-' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $line->salesShipment?->warehouse?->name ?: '-' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $line->item_type === 'vehicle' ? '整車' : '零件' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $line->item_code ?: '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $line->item_name }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($line->quantity) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($line->unit_price, 2) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($line->line_total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="px-4 py-6 text-center text-sm text-gray-500">
                                            目前沒有符合條件的銷貨資料。
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $salesReports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
