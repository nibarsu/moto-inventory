<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                銷貨出庫單
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('sales-shipments.edit', $salesShipment) }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                    編輯
                </a>
                <a href="{{ route('sales-shipments.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                    返回列表
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <dl class="grid gap-6 md:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">出庫單號</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $salesShipment->shipment_no }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">出庫日期</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $salesShipment->shipment_date->format('Y-m-d') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">銷貨單號</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $salesShipment->salesOrder?->so_no ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">客戶</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $salesShipment->customer?->name ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">出庫倉庫</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $salesShipment->warehouse?->name ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">總額</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($salesShipment->total_amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">建立人員</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $salesShipment->creator?->name ?: '-' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">備註</dt>
                            <dd class="mt-1 whitespace-pre-line text-sm text-gray-900">{{ $salesShipment->remark ?: '-' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-8 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">類型</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">料號 / 車型代碼</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">商品名稱</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">出庫數量</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">銷貨單價</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">小計</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">備註</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($salesShipment->items as $item)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->item_type === 'part' ? '零件' : '整車' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $item->item_code ?: '-' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->item_name }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ $item->quantity }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($item->line_total, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $item->remark ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                                            尚無出庫明細資料。
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
