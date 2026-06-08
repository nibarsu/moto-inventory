<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            新增進貨單明細
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('purchase-orders.items.store', $purchaseOrder) }}" class="space-y-6 p-6">
                    @csrf

                    @include('purchase-order-items.partials.form', [
                        'purchaseOrder' => $purchaseOrder,
                        'item' => null,
                        'parts' => $parts,
                        'vehicles' => $vehicles,
                    ])

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('purchase-orders.items.index', $purchaseOrder) }}" class="text-sm text-gray-600 hover:text-gray-900">取消</a>
                        <x-primary-button>儲存</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
