<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                應收帳款明細
            </h2>
            <a href="{{ route('accounts-receivable.edit', $receivable) }}" class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition ease-in-out duration-150 hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900">
                編輯應收
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <dl class="grid gap-6 md:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">應收編號</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $receivable->ar_no }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">客戶</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $receivable->customer?->name ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">來源類型</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $receivable->source_type ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">來源編號 ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $receivable->source_id ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">應收日期</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $receivable->ar_date->format('Y-m-d') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">到期日</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $receivable->due_date?->format('Y-m-d') ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">應收金額</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($receivable->total_amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">已收金額</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($receivable->received_amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">未收金額</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($receivable->balance_amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">狀態</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $statusLabels[$receivable->status] ?? $receivable->status }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">建立者</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $receivable->creator?->name ?: '-' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-8">
                        <h3 class="text-sm font-medium text-gray-500">備註</h3>
                        <div class="mt-2 rounded-md bg-gray-50 p-4 text-sm text-gray-900">{{ $receivable->remark ?: '-' }}</div>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('accounts-receivable.index') }}" class="text-sm text-gray-600 hover:text-gray-900">返回應收列表</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
