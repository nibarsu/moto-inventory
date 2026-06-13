<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            新增銷貨出庫
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('sales-shipments.create') }}" class="grid gap-4 md:grid-cols-3 md:items-end">
                        <div>
                            <x-input-label for="sales_order_id_selector" value="銷貨單" />
                            <select id="sales_order_id_selector" name="sales_order_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">請選擇銷貨單</option>
                                @foreach ($salesOrders as $salesOrder)
                                    <option value="{{ $salesOrder->id }}" @selected((string) request('sales_order_id') === (string) $salesOrder->id)>
                                        {{ $salesOrder->so_no }} / {{ $salesOrder->customer?->name }} / {{ $salesOrder->warehouse?->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-sm text-gray-500">
                            只顯示仍有可出庫數量的銷貨單。
                        </div>
                        <div class="flex gap-3">
                            <x-primary-button>載入明細</x-primary-button>
                            <a href="{{ route('sales-shipments.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                                返回列表
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if ($selectedSalesOrder)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form method="POST" action="{{ route('sales-shipments.store') }}">
                            @csrf

                            <input type="hidden" name="sales_order_id" value="{{ $selectedSalesOrder->id }}">

                            <div class="grid gap-6 md:grid-cols-3">
                                <div>
                                    <x-input-label for="shipment_no" value="出庫單號" />
                                    <x-text-input id="shipment_no" name="shipment_no" type="text" class="mt-1 block w-full" :value="old('shipment_no', $defaultShipmentNo)" required />
                                    <x-input-error :messages="$errors->get('shipment_no')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="shipment_date" value="出庫日期" />
                                    <x-text-input id="shipment_date" name="shipment_date" type="date" class="mt-1 block w-full" :value="old('shipment_date', now()->toDateString())" required />
                                    <x-input-error :messages="$errors->get('shipment_date')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label value="出庫倉庫" />
                                    <div class="mt-1 rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                        {{ $selectedSalesOrder->warehouse?->name ?: '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 grid gap-6 md:grid-cols-3">
                                <div>
                                    <x-input-label value="銷貨單號" />
                                    <div class="mt-1 rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                        {{ $selectedSalesOrder->so_no }}
                                    </div>
                                </div>
                                <div>
                                    <x-input-label value="客戶" />
                                    <div class="mt-1 rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                        {{ $selectedSalesOrder->customer?->name ?: '-' }}
                                    </div>
                                </div>
                                <div>
                                    <x-input-label value="銷貨日期" />
                                    <div class="mt-1 rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                        {{ $selectedSalesOrder->order_date->format('Y-m-d') }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">類型</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">料號 / 車型代碼</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">商品名稱</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">訂單數量</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">已出庫</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">剩餘可出</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">目前庫存</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">本次出庫</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">銷貨單價</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">備註</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach ($remainingItems as $index => $item)
                                            @php
                                                $remainingQuantity = max(0, $item->quantity - $item->delivered_quantity);
                                                $currentStock = (int) ($item->current_stock ?? 0);
                                                $quantityField = "items.$index.quantity_shipped";
                                                $unitPriceField = "items.$index.unit_price";
                                                $remarkField = "items.$index.remark";
                                                $defaultUnitPrice = number_format((float) $item->unit_price, 2, '.', '');
                                            @endphp
                                            <tr>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->item_type === 'part' ? '零件' : '整車' }}</td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $item->item_code ?: '-' }}</td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->item_name }}</td>
                                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ $item->quantity }}</td>
                                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ $item->delivered_quantity }}</td>
                                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ $remainingQuantity }}</td>
                                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ $currentStock }}</td>
                                                <td class="whitespace-nowrap px-4 py-3">
                                                    <input type="hidden" name="items[{{ $index }}][sales_order_item_id]" value="{{ $item->id }}">
                                                    <x-text-input name="items[{{ $index }}][quantity_shipped]" type="number" min="0" max="{{ min($remainingQuantity, $currentStock) }}" class="block w-24 text-right" :value="old($quantityField, 0)" />
                                                    <x-input-error :messages="$errors->get($quantityField)" class="mt-2" />
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3">
                                                    <x-text-input name="items[{{ $index }}][unit_price]" type="number" min="0" step="0.01" class="block w-32 text-right" :value="old($unitPriceField, $defaultUnitPrice)" />
                                                    <x-input-error :messages="$errors->get($unitPriceField)" class="mt-2" />
                                                </td>
                                                <td class="px-4 py-3">
                                                    <x-text-input name="items[{{ $index }}][remark]" type="text" class="block w-full" :value="old($remarkField)" />
                                                    <x-input-error :messages="$errors->get($remarkField)" class="mt-2" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6">
                                <x-input-label for="remark" value="單據備註" />
                                <textarea id="remark" name="remark" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark') }}</textarea>
                                <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                                <x-input-error :messages="$errors->get('items')" class="mt-2" />
                                <x-input-error :messages="$errors->get('sales_order_id')" class="mt-2" />
                            </div>

                            <div class="mt-6 flex items-center gap-3">
                                <x-primary-button>儲存出庫</x-primary-button>
                                <a href="{{ route('sales-orders.show', $selectedSalesOrder) }}" class="text-sm text-gray-600 hover:text-gray-900">
                                    返回銷貨單
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
