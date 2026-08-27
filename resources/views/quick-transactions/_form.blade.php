<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title }}</h2>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <div class="font-semibold">資料未通過驗證</div>
                    <ul class="mt-2 list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $action }}" class="space-y-6">
                @csrf

                <div class="grid gap-6 rounded-lg bg-white p-6 shadow-sm sm:grid-cols-3">
                    <div>
                        <x-input-label for="transaction_date" value="交易日期" />
                        <x-text-input id="transaction_date" name="transaction_date" type="date" class="mt-1 block w-full" :value="old('transaction_date', now()->toDateString())" required />
                    </div>

                    <div>
                        <x-input-label for="party_name" :value="$partyLabel" />
                        <x-text-input id="party_name" name="{{ $partyName }}" type="text" class="mt-1 block w-full" :value="old($partyName)" list="party-suggestions" data-lookup-url="{{ $partyLookupUrl }}" autocomplete="off" required />
                        <datalist id="party-suggestions"></datalist>
                    </div>

                    <div>
                        <x-input-label for="warehouse_id" value="倉庫" />
                        <select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">請選擇</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" @selected((string) old('warehouse_id') === (string) $warehouse->id)>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-3">
                        <x-input-label for="remark" value="備註" />
                        <textarea id="remark" name="remark" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark') }}</textarea>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">商品明細</h3>
                        <button type="button" id="add-item-row" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">新增一列</button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-fixed border-collapse">
                            <thead>
                                <tr class="border-b text-left text-sm text-gray-500">
                                    <th class="w-[34%] px-3 py-2">商品名稱</th>
                                    <th class="w-[12%] px-3 py-2">數量</th>
                                    <th class="w-[16%] px-3 py-2">單價</th>
                                    <th class="w-[18%] px-3 py-2">小計</th>
                                    <th class="w-[15%] px-3 py-2">備註</th>
                                    <th class="w-[5%] px-3 py-2 text-right">刪除</th>
                                </tr>
                            </thead>
                            <tbody id="item-rows">
                                @php
                                    $oldItems = old('items', [
                                        ['name' => '', 'quantity' => 1, 'unit_price' => '', 'remark' => ''],
                                    ]);
                                @endphp
                                @foreach ($oldItems as $index => $item)
                                    <tr class="border-b align-top item-row">
                                        <td class="px-3 py-3">
                                            <input type="text" name="items[{{ $index }}][name]" value="{{ $item['name'] ?? '' }}" class="product-name block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" list="product-suggestions-{{ $index }}" data-lookup-url="{{ $productLookupUrl }}" autocomplete="off" required>
                                            <datalist id="product-suggestions-{{ $index }}"></datalist>
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" min="1" class="item-quantity block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}" min="0" step="0.01" class="item-unit-price block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="item-line-total rounded-md bg-gray-50 px-3 py-2 text-right text-sm text-gray-700">0.00</div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="text" name="items[{{ $index }}][remark]" value="{{ $item['remark'] ?? '' }}" class="item-remark block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <button type="button" class="remove-item rounded-md px-2 py-2 text-sm text-red-600 hover:bg-red-50">刪除</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="px-3 py-4 text-right text-base font-semibold text-gray-900">合計</td>
                                    <td class="px-3 py-4">
                                        <div id="grand-total" class="rounded-md bg-gray-100 px-3 py-2 text-right text-base font-semibold text-gray-900">0.00</div>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">返回</a>
                    <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">{{ $submitLabel }}</button>
                </div>
            </form>
        </div>
    </div>

    <template id="item-row-template">
        <tr class="border-b align-top item-row">
            <td class="px-3 py-3">
                <input type="text" class="product-name block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="off" required>
                <datalist></datalist>
            </td>
            <td class="px-3 py-3">
                <input type="number" min="1" value="1" class="item-quantity block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </td>
            <td class="px-3 py-3">
                <input type="number" min="0" step="0.01" class="item-unit-price block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </td>
            <td class="px-3 py-3">
                <div class="item-line-total rounded-md bg-gray-50 px-3 py-2 text-right text-sm text-gray-700">0.00</div>
            </td>
            <td class="px-3 py-3">
                <input type="text" class="item-remark block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </td>
            <td class="px-3 py-3 text-right">
                <button type="button" class="remove-item rounded-md px-2 py-2 text-sm text-red-600 hover:bg-red-50">刪除</button>
            </td>
        </tr>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rowsContainer = document.getElementById('item-rows');
            const addRowButton = document.getElementById('add-item-row');
            const rowTemplate = document.getElementById('item-row-template');
            const partyInput = document.getElementById('party_name');
            const partyDatalist = document.getElementById('party-suggestions');
            const defaultPriceField = @json($defaultPriceField);
            let rowIndex = rowsContainer.querySelectorAll('.item-row').length;

            const debounce = (fn, delay = 200) => {
                let timer;
                return (...args) => {
                    clearTimeout(timer);
                    timer = setTimeout(() => fn(...args), delay);
                };
            };

            const fetchJson = async (url) => {
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!response.ok) {
                    return [];
                }
                return response.json();
            };

            const setOptions = (datalist, options, formatter) => {
                datalist.innerHTML = '';
                options.forEach((option) => {
                    const node = document.createElement('option');
                    const data = formatter(option);
                    node.value = data.value;
                    if (data.label) {
                        node.label = data.label;
                    }
                    datalist.appendChild(node);
                });
            };

            const updateLineTotal = (row) => {
                const quantity = Number(row.querySelector('.item-quantity').value || 0);
                const unitPrice = Number(row.querySelector('.item-unit-price').value || 0);
                row.querySelector('.item-line-total').textContent = (quantity * unitPrice).toFixed(2);
                updateGrandTotal();
            };

            const updateGrandTotal = () => {
                let total = 0;
                rowsContainer.querySelectorAll('.item-row').forEach((row) => {
                    total += Number(row.querySelector('.item-quantity').value || 0) * Number(row.querySelector('.item-unit-price').value || 0);
                });
                document.getElementById('grand-total').textContent = total.toFixed(2);
            };

            const bindProductLookup = (row, index) => {
                const nameInput = row.querySelector('.product-name');
                const unitPriceInput = row.querySelector('.item-unit-price');
                const datalist = row.querySelector('datalist');
                const lookupUrl = nameInput.dataset.lookupUrl;
                const datalistId = `product-suggestions-${index}`;
                datalist.id = datalistId;
                nameInput.setAttribute('list', datalistId);
                nameInput.name = `items[${index}][name]`;
                row.querySelector('.item-quantity').name = `items[${index}][quantity]`;
                unitPriceInput.name = `items[${index}][unit_price]`;
                row.querySelector('.item-remark').name = `items[${index}][remark]`;
                row.dataset.productMap = '{}';

                const updateSuggestions = debounce(async () => {
                    const keyword = nameInput.value.trim();
                    if (keyword.length < 1) {
                        datalist.innerHTML = '';
                        return;
                    }

                    const items = await fetchJson(`${lookupUrl}?keyword=${encodeURIComponent(keyword)}`);
                    row.dataset.productMap = JSON.stringify(items);
                    setOptions(datalist, items, (item) => ({
                        value: item.name,
                        label: `${item.part_no} / ${item.name}`,
                    }));
                });

                nameInput.addEventListener('input', updateSuggestions);
                nameInput.addEventListener('change', () => {
                    const items = JSON.parse(row.dataset.productMap || '[]');
                    const match = items.find((item) => item.name === nameInput.value.trim());

                    if (match && !unitPriceInput.value) {
                        unitPriceInput.value = Number(match[defaultPriceField] || 0).toFixed(2);
                        updateLineTotal(row);
                    }
                });
            };

            const addRow = () => {
                const fragment = rowTemplate.content.cloneNode(true);
                const row = fragment.querySelector('.item-row');
                row.querySelector('.product-name').dataset.lookupUrl = @json($productLookupUrl);
                rowsContainer.appendChild(fragment);
                const appendedRow = rowsContainer.lastElementChild;
                bindProductLookup(appendedRow, rowIndex);
                updateLineTotal(appendedRow);
                rowIndex += 1;
            };

            const bindExistingRows = () => {
                rowsContainer.querySelectorAll('.item-row').forEach((row, index) => {
                    bindProductLookup(row, index);
                    updateLineTotal(row);
                });
                rowIndex = rowsContainer.querySelectorAll('.item-row').length;
            };

            rowsContainer.addEventListener('input', (event) => {
                if (event.target.classList.contains('item-quantity') || event.target.classList.contains('item-unit-price')) {
                    updateLineTotal(event.target.closest('.item-row'));
                }
            });

            rowsContainer.addEventListener('click', (event) => {
                if (!event.target.classList.contains('remove-item')) {
                    return;
                }

                if (rowsContainer.querySelectorAll('.item-row').length === 1) {
                    const row = rowsContainer.querySelector('.item-row');
                    row.querySelectorAll('input').forEach((input) => {
                        input.value = input.type === 'number' ? '' : '';
                    });
                    row.querySelector('.item-quantity').value = 1;
                    updateLineTotal(row);
                    return;
                }

                event.target.closest('.item-row').remove();
                updateGrandTotal();
            });

            addRowButton.addEventListener('click', addRow);

            partyInput.addEventListener('input', debounce(async () => {
                const keyword = partyInput.value.trim();
                if (keyword.length < 1) {
                    partyDatalist.innerHTML = '';
                    return;
                }

                const options = await fetchJson(`${partyInput.dataset.lookupUrl}?keyword=${encodeURIComponent(keyword)}`);
                setOptions(partyDatalist, options, (value) => ({ value }));
            }));

            bindExistingRows();
        });
    </script>
</x-app-layout>
