<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">日常作業</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @php
                $user = auth()->user();
                $quickLinks = [
                    ['route' => 'quick-purchases.create', 'label' => '快速進貨單', 'visible' => $user?->hasPermission('purchase.manage') ?? false],
                    ['route' => 'quick-sales.create', 'label' => '快速出貨單', 'visible' => $user?->hasPermission('sales.manage') ?? false],
                    ['route' => 'transaction-reports.index', 'label' => '交易報表', 'visible' => ($user?->hasPermission('purchase.manage') ?? false) || ($user?->hasPermission('sales.manage') ?? false)],
                ];

                $quickLinks = array_values(array_filter($quickLinks, static fn (array $link): bool => $link['visible']));
            @endphp

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (empty($quickLinks))
                        <div class="text-sm text-gray-500">目前帳號沒有可使用的日常作業權限，請聯絡系統管理員。</div>
                    @else
                        <div class="grid gap-4 sm:grid-cols-3">
                            @foreach ($quickLinks as $link)
                                <a href="{{ route($link['route']) }}" class="flex min-h-24 items-center justify-center rounded-md border border-gray-200 bg-gray-50 px-5 py-4 text-base font-semibold text-gray-800 transition hover:border-gray-400 hover:bg-white hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
