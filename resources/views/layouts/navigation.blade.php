@php
    $navigationLinks = [
        ['route' => 'dashboard', 'label' => __('Dashboard'), 'pattern' => 'dashboard', 'permission' => null],
        ['route' => 'brands.index', 'label' => '品牌管理', 'pattern' => 'brands.*', 'permission' => 'brands.manage'],
        ['route' => 'categories.index', 'label' => '商品分類', 'pattern' => 'categories.*', 'permission' => 'categories.manage'],
        ['route' => 'parts.index', 'label' => '零件商品管理', 'pattern' => 'parts.*', 'permission' => 'parts.manage'],
        ['route' => 'vehicles.index', 'label' => '整車商品管理', 'pattern' => 'vehicles.*', 'permission' => 'vehicles.manage'],
        ['route' => 'warehouses.index', 'label' => '倉庫管理', 'pattern' => 'warehouses.*', 'permission' => 'warehouses.manage'],
        ['route' => 'suppliers.index', 'label' => '供應商管理', 'pattern' => 'suppliers.*', 'permission' => 'suppliers.manage'],
        ['route' => 'customers.index', 'label' => '客戶管理', 'pattern' => 'customers.*', 'permission' => 'customers.manage'],
        ['route' => 'stocks.index', 'label' => '庫存查詢', 'pattern' => 'stocks.index', 'permission' => 'stocks.manage'],
        ['route' => 'stock-movements.index', 'label' => '庫存異動', 'pattern' => 'stock-movements.index', 'permission' => 'stocks.manage'],
        ['route' => 'stocks.adjust', 'label' => '庫存調整', 'pattern' => 'stocks.adjust', 'permission' => 'stocks.manage'],
        ['route' => 'inventory-reports.index', 'label' => '庫存報表', 'pattern' => 'inventory-reports.*', 'permission' => 'stocks.manage'],
        ['route' => 'average-costs.index', 'label' => '平均成本', 'pattern' => 'average-costs.*', 'permission' => 'stocks.manage'],
        ['route' => 'purchase-orders.index', 'label' => '進貨單管理', 'pattern' => 'purchase-orders.*', 'permission' => 'purchase.manage'],
        ['route' => 'purchase-receipts.index', 'label' => '進貨入庫', 'pattern' => 'purchase-receipts.*', 'permission' => 'purchase.manage'],
        ['route' => 'purchase-reports.index', 'label' => '進貨報表', 'pattern' => 'purchase-reports.*', 'permission' => 'purchase.manage'],
        ['route' => 'sales-orders.index', 'label' => '銷貨單管理', 'pattern' => 'sales-orders.*', 'permission' => 'sales.manage'],
        ['route' => 'sales-shipments.index', 'label' => '銷貨出庫', 'pattern' => 'sales-shipments.*', 'permission' => 'sales.manage'],
        ['route' => 'sales-reports.index', 'label' => '銷貨報表', 'pattern' => 'sales-reports.*', 'permission' => 'sales.manage'],
        ['route' => 'repair-orders.index', 'label' => '維修工單', 'pattern' => 'repair-orders.*', 'permission' => 'repairs.manage'],
        ['route' => 'maintenance-records.index', 'label' => '保養紀錄', 'pattern' => 'maintenance-records.*', 'permission' => 'repairs.manage'],
        ['route' => 'owner-histories.index', 'label' => '車主歷史紀錄', 'pattern' => 'owner-histories.*', 'permission' => 'repairs.manage'],
        ['route' => 'accounts-receivable.index', 'label' => '應收帳款', 'pattern' => 'accounts-receivable.*', 'permission' => 'finance.manage'],
        ['route' => 'accounts-payable.index', 'label' => '應付帳款', 'pattern' => 'accounts-payable.*', 'permission' => 'finance.manage'],
        ['route' => 'barcode-labels.index', 'label' => '條碼列印', 'pattern' => 'barcode-labels.*', 'permission' => 'barcode.manage'],
        ['route' => 'barcode-scans.index', 'label' => '條碼掃描', 'pattern' => 'barcode-scans.*', 'permission' => 'barcode.manage'],
        ['route' => 'product-imports.index', 'label' => '匯入商品', 'pattern' => 'product-imports.*', 'permission' => 'import.manage'],
        ['route' => 'excel-exports.index', 'label' => '匯出 Excel', 'pattern' => 'excel-exports.*', 'permission' => 'export.manage'],
        ['route' => 'roles.index', 'label' => '角色管理', 'pattern' => 'roles.*', 'permission' => 'permissions.manage'],
        ['route' => 'user-access.index', 'label' => '使用者權限', 'pattern' => 'user-access.*', 'permission' => 'permissions.manage'],
    ];

    $navigationLinks = array_values(array_filter($navigationLinks, static function (array $link): bool {
        if (($link['permission'] ?? null) === null) {
            return true;
        }

        return auth()->user()?->hasPermission($link['permission']) ?? false;
    }));
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
