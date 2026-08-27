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
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <div class="text-sm tracking-[0.2em] text-gray-500">SALES DOCUMENT</div>
                            <div class="mt-1 text-3xl font-bold text-gray-900">出貨單</div>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-md bg-gray-50 px-4 py-3">
                                <div class="text-xs text-gray-500">單號</div>
                                <div class="mt-1 font-semibold text-gray-900">{{ $salesShipment->shipment_no }}</div>
                            </div>
                            <div class="rounded-md bg-gray-50 px-4 py-3">
                                <div class="text-xs text-gray-500">日期</div>
                                <div class="mt-1 font-semibold text-gray-900">{{ optional($salesShipment->shipment_date)->format('Y-m-d') }}</div>
                            </div>
                            <div class="rounded-md bg-gray-50 px-4 py-3">
                                <div class="text-xs text-gray-500">建立者</div>
                                <div class="mt-1 font-semibold text-gray-900">{{ $salesShipment->creator?->name ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 px-6 py-5 lg:grid-cols-[1.5fr_1fr]">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-md border border-dashed border-gray-300 px-4 py-3">
                            <div class="text-xs text-gray-500">客戶</div>
                            <div class="mt-1 text-base font-semibold text-gray-900">{{ $salesShipment->customer?->name ?: '-' }}</div>
                        </div>
                        <div class="rounded-md border border-dashed border-gray-300 px-4 py-3">
                            <div class="text-xs text-gray-500">倉庫</div>
                            <div class="mt-1 text-base font-semibold text-gray-900">{{ $salesShipment->warehouse?->name ?: '-' }}</div>
                        </div>
                    </div>
                    <div class="rounded-md border border-dashed border-gray-300 px-4 py-3">
                        <div class="text-xs text-gray-500">備註</div>
                        <div class="mt-1 min-h-[48px] text-sm leading-6 text-gray-700">{{ $salesShipment->remark ?: ' ' }}</div>
                    </div>
                </div>

                <div class="px-6 pb-6">
                    <div class="overflow-x-auto rounded-md border border-gray-200">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr class="text-left text-sm text-gray-600">
                                    <th class="w-16 px-4 py-3 text-center">項次</th>
                                    <th class="w-40 px-4 py-3">料號</th>
                                    <th class="px-4 py-3">商品名稱</th>
                                    <th class="w-24 px-4 py-3 text-right">數量</th>
                                    <th class="w-28 px-4 py-3 text-right">單價</th>
                                    <th class="w-32 px-4 py-3 text-right">金額</th>
                                    <th class="w-40 px-4 py-3">備註</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salesShipment->items as $index => $item)
                                    <tr class="border-t border-gray-200 text-sm text-gray-700">
                                        <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">{{ $item->item_code ?: '-' }}</td>
                                        <td class="px-4 py-3">{{ $item->item_name }}</td>
                                        <td class="px-4 py-3 text-right">{{ number_format($item->quantity) }}</td>
                                        <td class="px-4 py-3 text-right">{{ number_format((float) $item->unit_price, 2) }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ number_format((float) $item->line_total, 2) }}</td>
                                        <td class="px-4 py-3">{{ $item->remark ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <div class="w-full max-w-sm rounded-md border border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 text-sm">
                                <span class="text-gray-500">小計</span>
                                <span class="font-semibold text-gray-900">{{ number_format((float) $salesShipment->total_amount, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 text-sm">
                                <span class="text-gray-500">折讓</span>
                                <span class="font-semibold text-gray-900">0.00</span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-4 text-base">
                                <span class="font-semibold text-gray-900">總計</span>
                                <span class="text-xl font-bold text-gray-900">{{ number_format((float) $salesShipment->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
