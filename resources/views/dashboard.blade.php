<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @php
                $user = auth()->user();

                $quickLinks = [
                    ['route' => 'brands.index', 'label' => '品牌管理', 'visible' => $user?->hasPermission('brands.manage') ?? false],
                    ['route' => 'stocks.index', 'label' => '庫存查詢', 'visible' => $user?->hasPermission('stocks.manage') ?? false],
                    ['route' => 'quick-purchases.create', 'label' => '快速進貨單', 'visible' => $user?->hasPermission('purchase.manage') ?? false],
                    ['route' => 'purchase-orders.index', 'label' => '完整進貨單', 'visible' => $user?->hasPermission('purchase.manage') ?? false],
                    ['route' => 'quick-sales.create', 'label' => '快速出貨單', 'visible' => $user?->hasPermission('sales.manage') ?? false],
                    ['route' => 'sales-orders.index', 'label' => '完整出貨單', 'visible' => $user?->hasPermission('sales.manage') ?? false],
                    ['route' => 'transaction-reports.index', 'label' => '交易報表', 'visible' => ($user?->hasPermission('purchase.manage') ?? false) || ($user?->hasPermission('sales.manage') ?? false)],
                    ['route' => 'excel-exports.index', 'label' => '匯出 Excel', 'visible' => $user?->hasPermission('export.manage') ?? false],
                    ['route' => 'user-access.index', 'label' => '權限管理', 'visible' => $user?->hasPermission('permissions.manage') ?? false],
                ];

                $quickLinks = array_values(array_filter($quickLinks, static fn (array $link) => $link['visible']));
            @endphp

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (empty($quickLinks))
                        <div class="text-sm text-gray-500">目前沒有可用功能，請先確認使用者角色與權限設定。</div>
                    @else
                        <div class="flex flex-wrap gap-3">
                            @foreach ($quickLinks as $link)
                                <a href="{{ route($link['route']) }}" class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition ease-in-out duration-150 hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900">
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
