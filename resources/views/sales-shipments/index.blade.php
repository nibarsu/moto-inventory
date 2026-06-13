<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                銷貨出庫
            </h2>
            <a href="{{ route('sales-shipments.create') }}" class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition ease-in-out duration-150 hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900">
                新增出庫單
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">出庫單號</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">出庫日期</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">銷貨單號</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">客戶</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">倉庫</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">總額</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">操作</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($salesShipments as $salesShipment)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                                            <a href="{{ route('sales-shipments.show', $salesShipment) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $salesShipment->shipment_no }}
                                            </a>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $salesShipment->shipment_date->format('Y-m-d') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $salesShipment->salesOrder?->so_no ?: '-' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $salesShipment->customer?->name ?: '-' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $salesShipment->warehouse?->name ?: '-' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($salesShipment->total_amount, 2) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium">
                                            <a href="{{ route('sales-shipments.edit', $salesShipment) }}" class="text-indigo-600 hover:text-indigo-900">編輯</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">目前沒有銷貨出庫資料。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $salesShipments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
