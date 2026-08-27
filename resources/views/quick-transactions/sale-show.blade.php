<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">快速出貨單</h2>
            <div class="flex gap-2">
                <a href="{{ route('quick-sales.print', $salesShipment) }}" target="_blank" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">列印 A4</a>
                <a href="{{ route('quick-sales.create') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">再建一張</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="grid gap-6 sm:grid-cols-4">
                    <div><div class="text-sm text-gray-500">單號</div><div class="mt-1 font-semibold">{{ $salesShipment->shipment_no }}</div></div>
                    <div><div class="text-sm text-gray-500">交易日期</div><div class="mt-1 font-semibold">{{ optional($salesShipment->shipment_date)->format('Y-m-d') }}</div></div>
                    <div><div class="text-sm text-gray-500">客戶</div><div class="mt-1 font-semibold">{{ $salesShipment->customer?->name }}</div></div>
                    <div><div class="text-sm text-gray-500">倉庫</div><div class="mt-1 font-semibold">{{ $salesShipment->warehouse?->name }}</div></div>
                </div>
                <div class="mt-4 text-sm text-gray-600">備註：{{ $salesShipment->remark ?: '-' }}</div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b text-left text-sm text-gray-500">
                            <th class="px-4 py-2">商品</th>
                            <th class="px-4 py-2">料號</th>
                            <th class="px-4 py-2 text-right">數量</th>
                            <th class="px-4 py-2 text-right">單價</th>
                            <th class="px-4 py-2 text-right">小計</th>
                            <th class="px-4 py-2">備註</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($salesShipment->items as $item)
                            <tr class="border-b text-sm text-gray-700">
                                <td class="px-4 py-3">{{ $item->item_name }}</td>
                                <td class="px-4 py-3">{{ $item->item_code ?: '-' }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($item->quantity) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format((float) $item->line_total, 2) }}</td>
                                <td class="px-4 py-3">{{ $item->remark ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-right text-base font-semibold text-gray-900">合計</td>
                            <td class="px-4 py-4 text-right text-base font-semibold text-gray-900">{{ number_format((float) $salesShipment->total_amount, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
