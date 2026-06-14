<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            庫存報表
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('inventory-reports.index') }}" class="grid gap-4 md:grid-cols-5 md:items-end">
                        <div>
                            <x-input-label for="type" value="類型" />
                            <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="all" @selected($type === 'all')>全部</option>
                                <option value="part" @selected($type === 'part')>零件</option>
                                <option value="vehicle" @selected($type === 'vehicle')>整車</option>
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
                            <x-input-label for="is_active" value="狀態" />
                            <select id="is_active" name="is_active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="all" @selected($isActive === 'all')>全部</option>
                                <option value="1" @selected($isActive === '1')>啟用</option>
                                <option value="0" @selected($isActive === '0')>停用</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="keyword" value="關鍵字" />
                            <div class="flex gap-3">
                                <x-text-input id="keyword" name="keyword" type="text" class="mt-1 block w-full" :value="$keyword" placeholder="輸入料號、車型代碼或商品名稱" />
                                <x-primary-button class="mt-1">查詢</x-primary-button>
                                <a href="{{ route('inventory-reports.index') }}" class="mt-1 inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                                    清除
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">類型</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">代碼</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">名稱</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">品牌</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">分類</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">倉庫</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">庫存量</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">平均成本</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">庫存成本金額</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">售價</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">庫存售價金額</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">狀態</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($inventoryReports as $item)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->type_label }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $item->code }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->name }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->brand }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->category }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->warehouse }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($item->quantity) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($item->average_cost_price, 4) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($item->stock_cost_amount, 2) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($item->sale_price, 2) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($item->stock_sale_amount, 2) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-center text-sm">
                                            @if ($item->is_active)
                                                <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">啟用</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">停用</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="px-4 py-6 text-center text-sm text-gray-500">查無符合條件的庫存報表資料。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $inventoryReports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
