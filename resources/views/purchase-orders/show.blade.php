<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                進貨單資料
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('purchase-orders.items.index', $purchaseOrder) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    明細管理
                </a>
                <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    編輯進貨單
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <dl class="divide-y divide-gray-200 p-6">
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">進貨單號</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $purchaseOrder->po_no }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">進貨日期</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $purchaseOrder->order_date->format('Y-m-d') }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">預計到貨日</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ optional($purchaseOrder->expected_date)->format('Y-m-d') ?: '-' }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">供應商</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $purchaseOrder->supplier?->name ?: '-' }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">入庫倉庫</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $purchaseOrder->warehouse?->name ?: '-' }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">狀態</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $purchaseOrder->status }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">實際進貨金額</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ number_format($purchaseOrder->total_amount, 2) }}</dd>
                    </div>
                    <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">備註</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $purchaseOrder->remark ?: '-' }}</dd>
                    </div>
                </dl>

                <div class="border-t border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">進貨單明細</h3>
                        <a href="{{ route('purchase-orders.items.create', $purchaseOrder) }}" class="text-sm text-indigo-600 hover:text-indigo-900">新增明細</a>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">類型</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">商品代碼</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">商品名稱</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">數量</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">單價</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">小計</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($purchaseOrder->items as $item)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->item_type === 'part' ? '零件' : '整車' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $item->item_code ?: '-' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->item_name }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ $item->quantity }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($item->line_total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">目前沒有進貨單明細。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border-t border-gray-200 p-6">
                    <a href="{{ route('purchase-orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">返回列表</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
