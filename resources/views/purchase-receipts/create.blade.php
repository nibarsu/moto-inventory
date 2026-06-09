<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            新增進貨入庫
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('purchase-receipts.create') }}" class="grid gap-4 md:grid-cols-3 md:items-end">
                        <div>
                            <x-input-label for="purchase_order_id_selector" value="進貨單" />
                            <select id="purchase_order_id_selector" name="purchase_order_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">請選擇進貨單</option>
                                @foreach ($purchaseOrders as $purchaseOrder)
                                    <option value="{{ $purchaseOrder->id }}" @selected((string) request('purchase_order_id') === (string) $purchaseOrder->id)>
                                        {{ $purchaseOrder->po_no }} / {{ $purchaseOrder->supplier?->name }} / {{ $purchaseOrder->warehouse?->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-sm text-gray-500">
                            只顯示仍有未入庫數量的進貨單。
                        </div>
                        <div class="flex gap-3">
                            <x-primary-button>載入明細</x-primary-button>
                            <a href="{{ route('purchase-receipts.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                                返回列表
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if ($selectedPurchaseOrder)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form method="POST" action="{{ route('purchase-receipts.store') }}">
                            @csrf

                            <input type="hidden" name="purchase_order_id" value="{{ $selectedPurchaseOrder->id }}">

                            <div class="grid gap-6 md:grid-cols-3">
                                <div>
                                    <x-input-label for="receipt_no" value="入庫單號" />
                                    <x-text-input id="receipt_no" name="receipt_no" type="text" class="mt-1 block w-full" :value="old('receipt_no', $defaultReceiptNo)" required />
                                    <x-input-error :messages="$errors->get('receipt_no')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="receipt_date" value="入庫日期" />
                                    <x-text-input id="receipt_date" name="receipt_date" type="date" class="mt-1 block w-full" :value="old('receipt_date', now()->toDateString())" required />
                                    <x-input-error :messages="$errors->get('receipt_date')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label value="入庫倉庫" />
                                    <div class="mt-1 rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                        {{ $selectedPurchaseOrder->warehouse?->name ?: '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 grid gap-6 md:grid-cols-3">
                                <div>
                                    <x-input-label value="進貨單號" />
                                    <div class="mt-1 rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                        {{ $selectedPurchaseOrder->po_no }}
                                    </div>
                                </div>
                                <div>
                                    <x-input-label value="供應商" />
                                    <div class="mt-1 rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                        {{ $selectedPurchaseOrder->supplier?->name ?: '-' }}
                                    </div>
                                </div>
                                <div>
                                    <x-input-label value="進貨日期" />
                                    <div class="mt-1 rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                        {{ $selectedPurchaseOrder->order_date->format('Y-m-d') }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">類型</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">商品代碼</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">商品名稱</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">訂購數量</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">已入庫</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">剩餘</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">本次入庫</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">實際單價</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">備註</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach ($remainingItems as $index => $item)
                                            @php
                                                $remainingQuantity = max(0, $item->quantity - $item->received_quantity);
                                            @endphp
                                            <tr>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                    {{ $item->item_type === 'part' ? '零件' : '整車' }}
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                                                    {{ $item->item_code ?: '-' }}
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                    {{ $item->item_name }}
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">
                                                    {{ $item->quantity }}
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">
                                                    {{ $item->received_quantity }}
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">
                                                    {{ $remainingQuantity }}
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3">
                                                    <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->id }}">
                                                    <x-text-input name="items[{{ $index }}][quantity_received]" type="number" min="0" max="{{ $remainingQuantity }}" class="block w-24 text-right" :value="old("items.$index.quantity_received", 0)" />
                                                    <x-input-error :messages="$errors->get("items.$index.quantity_received")" class="mt-2" />
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3">
                                                    <x-text-input name="items[{{ $index }}][unit_cost]" type="number" min="0" step="0.01" class="block w-32 text-right" :value="old("items.$index.unit_cost", number_format((float) $item->unit_price, 2, '.', ''))" />
                                                    <x-input-error :messages="$errors->get("items.$index.unit_cost")" class="mt-2" />
                                                </td>
                                                <td class="px-4 py-3">
                                                    <x-text-input name="items[{{ $index }}][remark]" type="text" class="block w-full" :value="old("items.$index.remark")" />
                                                    <x-input-error :messages="$errors->get("items.$index.remark")" class="mt-2" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6">
                                <x-input-label for="remark" value="整單備註" />
                                <textarea id="remark" name="remark" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark') }}</textarea>
                                <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                                <x-input-error :messages="$errors->get('items')" class="mt-2" />
                                <x-input-error :messages="$errors->get('purchase_order_id')" class="mt-2" />
                            </div>

                            <div class="mt-6 flex items-center gap-3">
                                <x-primary-button>儲存入庫</x-primary-button>
                                <a href="{{ route('purchase-orders.show', $selectedPurchaseOrder) }}" class="text-sm text-gray-600 hover:text-gray-900">
                                    返回進貨單
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
