<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                應付帳款明細
            </h2>
            <a href="{{ route('accounts-payable.edit', $payable) }}" class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition ease-in-out duration-150 hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900">
                編輯應付
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <dl class="grid gap-6 md:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">應付編號</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payable->ap_no }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">供應商</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payable->supplier?->name ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">來源類型</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payable->source_type ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">來源編號 ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payable->source_id ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">應付日期</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payable->ap_date->format('Y-m-d') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">到期日</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payable->due_date?->format('Y-m-d') ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">應付金額</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($payable->total_amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">已付金額</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($payable->paid_amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">未付金額</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($payable->balance_amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">狀態</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $statusLabels[$payable->status] ?? $payable->status }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">建立者</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payable->creator?->name ?: '-' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-8">
                        <h3 class="text-sm font-medium text-gray-500">備註</h3>
                        <div class="mt-2 rounded-md bg-gray-50 p-4 text-sm text-gray-900">{{ $payable->remark ?: '-' }}</div>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('accounts-payable.index') }}" class="text-sm text-gray-600 hover:text-gray-900">返回應付列表</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
