<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            車主歷史紀錄
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('owner-histories.index') }}" class="grid gap-4 md:grid-cols-4 md:items-end">
                        <div>
                            <x-input-label for="customer_id" value="客戶" />
                            <select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">全部客戶</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected($customerId === (string) $customer->id)>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="keyword" value="關鍵字" />
                            <x-text-input id="keyword" name="keyword" type="text" class="mt-1 block w-full" :value="$keyword" placeholder="客戶名稱、電話、手機、車牌、單號、內容" />
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <x-primary-button>查詢</x-primary-button>
                            <a href="{{ route('owner-histories.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                                清除
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-4">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg md:col-span-2">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">目前查詢車主</div>
                        <div class="mt-2 text-lg font-semibold text-gray-900">{{ $selectedCustomer?->name ?: '全部客戶' }}</div>
                        <div class="mt-2 text-sm text-gray-600">
                            電話：{{ $selectedCustomer?->phone ?: '-' }}<br>
                            手機：{{ $selectedCustomer?->mobile ?: '-' }}
                        </div>
                    </div>
                </div>
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">維修工單</div>
                        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($summary->repair_count) }}</div>
                    </div>
                </div>
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">保養紀錄</div>
                        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($summary->maintenance_count) }}</div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">日期</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">類型</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">單號</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">客戶</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">車型</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">車牌</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">里程</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">項目 / 狀態</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">摘要</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">查看</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($ownerHistories as $item)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->date }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->record_type_label }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $item->number }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->customer }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->vehicle }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->plate_no }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ $item->mileage !== null ? number_format($item->mileage) : '-' }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->service_type }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            <div>{{ $item->summary }}</div>
                                            @if ($item->detail !== '-')
                                                <div class="mt-1 text-xs text-gray-500">{{ $item->detail }}</div>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium">
                                            <a href="{{ $item->link }}" class="text-indigo-600 hover:text-indigo-900">明細</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-4 py-8 text-center text-sm text-gray-500">目前沒有符合條件的車主歷史資料。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $ownerHistories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
