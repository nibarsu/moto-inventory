<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            編輯銷貨單明細
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('sales-orders.items.update', [$salesOrder, $item]) }}" class="space-y-6 p-6">
                    @csrf
                    @method('PUT')

                    @include('sales-order-items.partials.form', [
                        'salesOrder' => $salesOrder,
                        'item' => $item,
                        'parts' => $parts,
                        'vehicles' => $vehicles,
                    ])

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('sales-orders.items.index', $salesOrder) }}" class="text-sm text-gray-600 hover:text-gray-900">取消</a>
                        <x-primary-button>更新</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
