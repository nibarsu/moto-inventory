<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            條碼列印
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('barcode-labels.index') }}" class="grid gap-4 md:grid-cols-4 md:items-end">
                        <div>
                            <x-input-label for="type" value="商品類型" />
                            <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="all" @selected($type === 'all')>全部</option>
                                <option value="part" @selected($type === 'part')>零件</option>
                                <option value="vehicle" @selected($type === 'vehicle')>整車</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="keyword" value="關鍵字" />
                            <x-text-input id="keyword" name="keyword" type="text" class="mt-1 block w-full" :value="$keyword" placeholder="可搜尋代碼、條碼或名稱" />
                        </div>
                        <div class="flex gap-3">
                            <x-primary-button>查詢</x-primary-button>
                            <a href="{{ route('barcode-labels.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">
                                清除
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div class="text-sm text-gray-600">
                            勾選商品後可開啟新視窗預覽並列印條碼標籤。若商品未設定條碼，系統會自動使用料號或車型代碼。
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" id="select-all-items" class="inline-flex items-center rounded-md border border-gray-300 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50">
                                全選本頁
                            </button>
                            <form method="POST" action="{{ route('barcode-labels.print') }}" target="_blank" id="barcode-print-form">
                                @csrf
                                <div id="selected-items-container"></div>
                                <x-primary-button>開啟列印預覽</x-primary-button>
                            </form>
                        </div>
                    </div>

                    <x-input-error class="mb-4" :messages="$errors->get('items')" />

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">選取</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">類型</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">代碼</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">商品名稱</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">條碼內容</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">列印張數</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($items as $item)
                                    <tr data-barcode-row data-item-type="{{ $item->type }}" data-item-id="{{ $item->id }}">
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                            <input type="checkbox" data-select-item class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->type_label }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $item->code }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $item->name }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $item->barcode }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                            <input type="number" min="1" max="50" value="1" data-quantity class="w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">查無可列印的商品資料。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAllButton = document.getElementById('select-all-items');
            const printForm = document.getElementById('barcode-print-form');
            const selectedItemsContainer = document.getElementById('selected-items-container');

            selectAllButton?.addEventListener('click', () => {
                document.querySelectorAll('[data-select-item]').forEach((checkbox) => {
                    checkbox.checked = true;
                });
            });

            printForm?.addEventListener('submit', (event) => {
                selectedItemsContainer.innerHTML = '';

                let selectedCount = 0;

                document.querySelectorAll('[data-barcode-row]').forEach((row) => {
                    const checkbox = row.querySelector('[data-select-item]');

                    if (!checkbox?.checked) {
                        return;
                    }

                    const quantityInput = row.querySelector('[data-quantity]');
                    const quantity = Number.parseInt(quantityInput?.value ?? '1', 10);
                    const normalizedQuantity = Number.isNaN(quantity) ? 1 : Math.min(Math.max(quantity, 1), 50);
                    const index = selectedCount;

                    const fields = {
                        type: row.dataset.itemType,
                        id: row.dataset.itemId,
                        quantity: normalizedQuantity,
                    };

                    Object.entries(fields).forEach(([key, value]) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `items[${index}][${key}]`;
                        input.value = value;
                        selectedItemsContainer.appendChild(input);
                    });

                    selectedCount += 1;
                });

                if (selectedCount > 0) {
                    return;
                }

                event.preventDefault();

                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'warning',
                        title: '尚未選擇商品',
                        text: '請至少勾選一筆商品再進行條碼列印。',
                        confirmButtonText: '知道了',
                    });
                } else {
                    window.alert('請至少勾選一筆商品再進行條碼列印。');
                }
            });
        });
    </script>
</x-app-layout>
