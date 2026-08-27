<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">交易報表</h2>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('transaction-reports.index') }}" class="rounded-lg bg-white p-6 shadow-sm">
                <div class="grid gap-4 sm:grid-cols-4">
                    <div>
                        <x-input-label for="party_name" value="交易對象名稱" />
                        <x-text-input id="party_name" name="party_name" type="text" class="mt-1 block w-full" :value="$filters['party_name']" />
                    </div>
                    <div>
                        <x-input-label for="type" value="類型" />
                        <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all" @selected($filters['type'] === 'all')>全部</option>
                            <option value="purchase" @selected($filters['type'] === 'purchase')>進貨</option>
                            <option value="sale" @selected($filters['type'] === 'sale')>出貨</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="date_from" value="開始日期" />
                        <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full" :value="$filters['date_from']" />
                    </div>
                    <div>
                        <x-input-label for="date_to" value="結束日期" />
                        <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full" :value="$filters['date_to']" />
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">查詢</button>
                </div>
            </form>

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b text-left text-sm text-gray-500">
                            <th class="px-4 py-2">日期</th>
                            <th class="px-4 py-2">類型</th>
                            <th class="px-4 py-2">單號</th>
                            <th class="px-4 py-2">交易對象</th>
                            <th class="px-4 py-2">倉庫</th>
                            <th class="px-4 py-2 text-right">金額</th>
                            <th class="px-4 py-2 text-right">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="border-b text-sm text-gray-700">
                                <td class="px-4 py-3">{{ $row['date'] }}</td>
                                <td class="px-4 py-3">{{ $row['type_label'] }}</td>
                                <td class="px-4 py-3">{{ $row['doc_no'] }}</td>
                                <td class="px-4 py-3">{{ $row['party_name'] }}</td>
                                <td class="px-4 py-3">{{ $row['warehouse_name'] }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($row['total_amount'], 2) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ $row['show_url'] }}" class="text-indigo-600 hover:text-indigo-900">查看</a>
                                    <a href="{{ $row['print_url'] }}" target="_blank" class="ms-3 text-gray-700 hover:text-gray-900">列印</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">查無資料。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-6">
                    {{ $rows->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
