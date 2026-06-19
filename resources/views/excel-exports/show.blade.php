<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Excel 匯出紀錄
            </h2>
            <a href="{{ route('excel-exports.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                返回清單
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="grid gap-6 p-6 text-gray-900 md:grid-cols-2">
                    <div>
                        <div class="text-sm font-medium text-gray-500">資料集</div>
                        <div class="mt-1 text-base text-gray-900">{{ $typeLabels[$log->export_type] ?? $log->export_type }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">檔名</div>
                        <div class="mt-1 break-all text-base text-gray-900">{{ $log->filename }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">匯出時間</div>
                        <div class="mt-1 text-base text-gray-900">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">操作人員</div>
                        <div class="mt-1 text-base text-gray-900">{{ $log->creator?->name ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">資料筆數</div>
                        <div class="mt-1 text-base text-gray-900">{{ number_format($log->row_count) }}</div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900">匯出篩選條件</h3>

                    @if (empty($log->filter_summary))
                        <div class="mt-3 rounded-lg border border-dashed border-gray-300 px-6 py-8 text-center text-sm text-gray-500">
                            本次匯出沒有額外篩選條件。
                        </div>
                    @else
                        <div class="mt-3 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">欄位</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">值</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($log->filter_summary as $key => $value)
                                        <tr>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $key }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
