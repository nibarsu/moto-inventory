<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @php
                $quickLinks = [
                    ['route' => 'brands.index', 'label' => '品牌管理', 'permission' => 'brands.manage'],
                    ['route' => 'stocks.index', 'label' => '庫存查詢', 'permission' => 'stocks.manage'],
                    ['route' => 'purchase-orders.index', 'label' => '進貨單管理', 'permission' => 'purchase.manage'],
                    ['route' => 'sales-orders.index', 'label' => '銷貨單管理', 'permission' => 'sales.manage'],
                    ['route' => 'excel-exports.index', 'label' => '匯出 Excel', 'permission' => 'export.manage'],
                    ['route' => 'user-access.index', 'label' => '權限管理', 'permission' => 'permissions.manage'],
                ];

                $quickLinks = array_values(array_filter($quickLinks, static fn (array $link) => auth()->user()?->hasPermission($link['permission']) ?? false));
            @endphp

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (empty($quickLinks))
                        <div class="text-sm text-gray-500">目前帳號尚未指派任何功能權限，請洽系統管理員設定。</div>
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
