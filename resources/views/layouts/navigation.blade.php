@php
    $user = auth()->user();

    $navigationLinks = [
        ['route' => 'dashboard', 'label' => __('Dashboard'), 'pattern' => 'dashboard', 'visible' => true],
        ['route' => 'brands.index', 'label' => '品牌管理', 'pattern' => 'brands.*', 'visible' => $user?->hasPermission('brands.manage') ?? false],
        ['route' => 'categories.index', 'label' => '商品分類', 'pattern' => 'categories.*', 'visible' => $user?->hasPermission('categories.manage') ?? false],
        ['route' => 'parts.index', 'label' => '零件商品管理', 'pattern' => 'parts.*', 'visible' => $user?->hasPermission('parts.manage') ?? false],
        ['route' => 'vehicles.index', 'label' => '整車商品管理', 'pattern' => 'vehicles.*', 'visible' => $user?->hasPermission('vehicles.manage') ?? false],
        ['route' => 'warehouses.index', 'label' => '倉庫管理', 'pattern' => 'warehouses.*', 'visible' => $user?->hasPermission('warehouses.manage') ?? false],
        ['route' => 'suppliers.index', 'label' => '供應商管理', 'pattern' => 'suppliers.*', 'visible' => $user?->hasPermission('suppliers.manage') ?? false],
        ['route' => 'customers.index', 'label' => '客戶管理', 'pattern' => 'customers.*', 'visible' => $user?->hasPermission('customers.manage') ?? false],
        ['route' => 'stocks.index', 'label' => '庫存查詢', 'pattern' => 'stocks.index', 'visible' => $user?->hasPermission('stocks.manage') ?? false],
        ['route' => 'stock-movements.index', 'label' => '庫存異動', 'pattern' => 'stock-movements.index', 'visible' => $user?->hasPermission('stocks.manage') ?? false],
        ['route' => 'stocks.adjust', 'label' => '庫存調整', 'pattern' => 'stocks.adjust', 'visible' => $user?->hasPermission('stocks.manage') ?? false],
        ['route' => 'inventory-reports.index', 'label' => '庫存報表', 'pattern' => 'inventory-reports.*', 'visible' => $user?->hasPermission('stocks.manage') ?? false],
        ['route' => 'average-costs.index', 'label' => '平均成本', 'pattern' => 'average-costs.*', 'visible' => $user?->hasPermission('stocks.manage') ?? false],
        ['route' => 'quick-purchases.create', 'label' => '快速進貨單', 'pattern' => 'quick-purchases.*', 'visible' => $user?->hasPermission('purchase.manage') ?? false],
        ['route' => 'purchase-orders.index', 'label' => '完整進貨單', 'pattern' => 'purchase-orders.*', 'visible' => $user?->hasPermission('purchase.manage') ?? false],
        ['route' => 'purchase-receipts.index', 'label' => '進貨入庫', 'pattern' => 'purchase-receipts.*', 'visible' => $user?->hasPermission('purchase.manage') ?? false],
        ['route' => 'purchase-reports.index', 'label' => '進貨報表', 'pattern' => 'purchase-reports.*', 'visible' => $user?->hasPermission('purchase.manage') ?? false],
        ['route' => 'quick-sales.create', 'label' => '快速出貨單', 'pattern' => 'quick-sales.*', 'visible' => $user?->hasPermission('sales.manage') ?? false],
        ['route' => 'sales-orders.index', 'label' => '完整出貨單', 'pattern' => 'sales-orders.*', 'visible' => $user?->hasPermission('sales.manage') ?? false],
        ['route' => 'sales-shipments.index', 'label' => '銷貨出庫', 'pattern' => 'sales-shipments.*', 'visible' => $user?->hasPermission('sales.manage') ?? false],
        ['route' => 'sales-reports.index', 'label' => '銷貨報表', 'pattern' => 'sales-reports.*', 'visible' => $user?->hasPermission('sales.manage') ?? false],
        ['route' => 'transaction-reports.index', 'label' => '交易報表', 'pattern' => 'transaction-reports.*', 'visible' => ($user?->hasPermission('purchase.manage') ?? false) || ($user?->hasPermission('sales.manage') ?? false)],
        ['route' => 'repair-orders.index', 'label' => '維修工單', 'pattern' => 'repair-orders.*', 'visible' => $user?->hasPermission('repairs.manage') ?? false],
        ['route' => 'maintenance-records.index', 'label' => '保養紀錄', 'pattern' => 'maintenance-records.*', 'visible' => $user?->hasPermission('repairs.manage') ?? false],
        ['route' => 'owner-histories.index', 'label' => '車主歷史紀錄', 'pattern' => 'owner-histories.*', 'visible' => $user?->hasPermission('repairs.manage') ?? false],
        ['route' => 'accounts-receivable.index', 'label' => '應收帳款', 'pattern' => 'accounts-receivable.*', 'visible' => $user?->hasPermission('finance.manage') ?? false],
        ['route' => 'accounts-payable.index', 'label' => '應付帳款', 'pattern' => 'accounts-payable.*', 'visible' => $user?->hasPermission('finance.manage') ?? false],
        ['route' => 'barcode-labels.index', 'label' => '條碼列印', 'pattern' => 'barcode-labels.*', 'visible' => $user?->hasPermission('barcode.manage') ?? false],
        ['route' => 'barcode-scans.index', 'label' => '條碼掃描', 'pattern' => 'barcode-scans.*', 'visible' => $user?->hasPermission('barcode.manage') ?? false],
        ['route' => 'product-imports.index', 'label' => '匯入商品', 'pattern' => 'product-imports.*', 'visible' => $user?->hasPermission('import.manage') ?? false],
        ['route' => 'excel-exports.index', 'label' => '匯出 Excel', 'pattern' => 'excel-exports.*', 'visible' => $user?->hasPermission('export.manage') ?? false],
        ['route' => 'roles.index', 'label' => '角色管理', 'pattern' => 'roles.*', 'visible' => $user?->hasPermission('permissions.manage') ?? false],
        ['route' => 'user-access.index', 'label' => '使用者權限', 'pattern' => 'user-access.*', 'visible' => $user?->hasPermission('permissions.manage') ?? false],
    ];

    $navigationLinks = array_values(array_filter($navigationLinks, static fn (array $link): bool => $link['visible']));
@endphp

<nav x-data="{ open: false }" class="border-b border-gray-100 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 items-center justify-between py-3">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="shrink-0">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                </a>
                <div class="hidden sm:block">
                    <div class="text-sm font-semibold text-gray-900">{{ config('app.name', 'Moto Inventory') }}</div>
                    <div class="text-xs text-gray-500">機車行進銷存管理系統</div>
                </div>
            </div>

            <div class="hidden sm:ms-6 sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition ease-in-out duration-150 hover:text-gray-700 focus:outline-none">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="hidden border-t border-gray-100 bg-white sm:block">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 py-3">
                @foreach ($navigationLinks as $link)
                    <x-nav-link :href="route($link['route'])" :active="request()->routeIs($link['pattern'])">
                        {{ $link['label'] }}
                    </x-nav-link>
                @endforeach
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-gray-200 bg-white sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
            @foreach ($navigationLinks as $link)
                <x-responsive-nav-link :href="route($link['route'])" :active="request()->routeIs($link['pattern'])">
                    {{ $link['label'] }}
                </x-responsive-nav-link>
            @endforeach
        </div>

        <div class="border-t border-gray-200 pb-1 pt-4">
            <div class="px-4">
                <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
