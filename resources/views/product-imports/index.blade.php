<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            匯入商品
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[1fr_1fr]">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-900">上傳 CSV</h3>
                        <p class="mt-1 text-sm text-gray-500">系統會依照商品代碼自動判斷新增或更新。品牌與分類請使用 `code` 對應。</p>

                        <form method="POST" action="{{ route('product-imports.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                            @csrf

                            <div>
                                <x-input-label for="item_type" value="商品類型" />
                                <select id="item_type" name="item_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="part" @selected(old('item_type') === 'part')>零件商品</option>
                                    <option value="vehicle" @selected(old('item_type') === 'vehicle')>整車商品</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('item_type')" />
                            </div>

                            <div>
                                <x-input-label for="import_file" value="CSV 檔案" />
                                <input id="import_file" name="import_file" type="file" accept=".csv,.txt" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
                                <x-input-error class="mt-2" :messages="$errors->get('import_file')" />
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <x-primary-button>開始匯入</x-primary-button>
                                <a href="{{ route('product-imports.template', 'part') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                                    下載零件範本
                                </a>
                                <a href="{{ route('product-imports.template', 'vehicle') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                                    下載整車範本
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-900">欄位規格</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <div class="mb-2 text-sm font-semibold text-gray-800">零件 CSV 欄位</div>
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                                    {{ implode(', ', $partColumns) }}
                                </div>
                            </div>
                            <div>
                                <div class="mb-2 text-sm font-semibold text-gray-800">整車 CSV 欄位</div>
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                                    {{ implode(', ', $vehicleColumns) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900">匯入紀錄</h3>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">時間</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">類型</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">檔名</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">資料筆數</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">新增</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">更新</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">略過</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">狀態</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">操作</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($logs as $log)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $log->item_type === 'part' ? '零件' : '整車' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $log->original_filename }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($log->total_rows) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-green-700">{{ number_format($log->created_count) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-indigo-700">{{ number_format($log->updated_count) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-amber-700">{{ number_format($log->skipped_count) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm">
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $log->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700' }}">
                                                {{ $log->status === 'completed' ? '完成' : '失敗' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                            <a href="{{ route('product-imports.show', $log) }}" class="font-medium text-indigo-600 hover:text-indigo-900">查看</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">目前沒有商品匯入紀錄。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
