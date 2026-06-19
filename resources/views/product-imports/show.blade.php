<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                匯入紀錄詳情
            </h2>
            <a href="{{ route('product-imports.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                返回清單
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="grid gap-6 p-6 text-gray-900 md:grid-cols-2">
                    <div>
                        <div class="text-sm font-medium text-gray-500">商品類型</div>
                        <div class="mt-1 text-base text-gray-900">{{ $log->item_type === 'part' ? '零件' : '整車' }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">檔案名稱</div>
                        <div class="mt-1 break-all text-base text-gray-900">{{ $log->original_filename }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">匯入時間</div>
                        <div class="mt-1 text-base text-gray-900">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">操作人員</div>
                        <div class="mt-1 text-base text-gray-900">{{ $log->creator?->name ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">狀態</div>
                        <div class="mt-1 text-base text-gray-900">{{ $log->status === 'completed' ? '完成' : '失敗' }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">資料筆數</div>
                        <div class="mt-1 text-base text-gray-900">{{ number_format($log->total_rows) }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">新增筆數</div>
                        <div class="mt-1 text-base text-green-700">{{ number_format($log->created_count) }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">更新筆數</div>
                        <div class="mt-1 text-base text-indigo-700">{{ number_format($log->updated_count) }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">略過筆數</div>
                        <div class="mt-1 text-base text-amber-700">{{ number_format($log->skipped_count) }}</div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900">欄位標題</h3>
                    <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                        {{ implode(', ', $log->summary['headers'] ?? []) ?: '-' }}
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900">錯誤與略過原因</h3>

                    @if (empty($log->summary['errors']))
                        <div class="mt-3 rounded-lg border border-dashed border-gray-300 px-6 py-8 text-center text-sm text-gray-500">
                            本次匯入沒有錯誤或略過資料。
                        </div>
                    @else
                        <ul class="mt-3 space-y-3">
                            @foreach ($log->summary['errors'] as $error)
                                <li class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
