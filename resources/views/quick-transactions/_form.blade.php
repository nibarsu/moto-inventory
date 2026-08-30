<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title }}</h2>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">返回儀表板</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <div class="font-semibold">請修正以下欄位後再送出：</div>
                    <ul class="mt-2 list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $action }}" class="space-y-5">
                @csrf

                <section class="overflow-hidden rounded-md border border-gray-300 bg-white shadow-sm">
                    <div class="border-b border-gray-300 bg-gray-50 px-5 py-4">
                        <div class="flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <div class="text-xs font-semibold tracking-[0.18em] text-gray-500">{{ $documentType }}</div>
                                <h3 class="mt-1 text-2xl font-bold text-gray-900">{{ $title }}</h3>
                            </div>
                            <div class="text-right text-xs leading-5 text-gray-500">
                                <div>輸入完成後會立即建立單據與庫存異動</div>
                                <div>交易對象及商品可直接輸入新名稱</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid border-b border-gray-300 sm:grid-cols-3">
                        <label class="block border-b border-gray-300 p-4 sm:border-b-0 sm:border-r">
                            <span class="text-xs font-medium text-gray-600">交易日期</span>
                            <x-text-input id="transaction_date" name="transaction_date" type="date" class="mt-1 block w-full border-0 border-b border-gray-300 bg-transparent px-0 shadow-none focus:border-indigo-500 focus:ring-0" :value="old('transaction_date', now()->toDateString())" required />
                        </label>

                        <label class="block border-b border-gray-300 p-4 sm:border-b-0 sm:border-r">
                            <span class="text-xs font-medium text-gray-600">{{ $partyLabel }}</span>
                            <x-text-input id="party_name" name="{{ $partyName }}" type="text" class="mt-1 block w-full border-0 border-b border-gray-300 bg-transparent px-0 shadow-none focus:border-indigo-500 focus:ring-0" :value="old($partyName)" list="party-suggestions" data-lookup-url="{{ $partyLookupUrl }}" autocomplete="off" placeholder="輸入名稱後可選擇既有資料" required />
                            <datalist id="party-suggestions"></datalist>
                        </label>

                        <label class="block p-4">
                            <span class="text-xs font-medium text-gray-600">倉庫</span>
                            <select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full border-0 border-b border-gray-300 bg-transparent px-0 text-sm shadow-none focus:border-indigo-500 focus:ring-0" required>
                                <option value="">請選擇倉庫</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" @selected((string) old('warehouse_id') === (string) $warehouse->id)>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <label class="block p-4">
                        <span class="text-xs font-medium text-gray-600">單據備註</span>
                        <input id="remark" name="remark" type="text" value="{{ old('remark') }}" class="mt-1 block w-full border-0 border-b border-gray-300 bg-transparent px-0 text-sm shadow-none focus:border-indigo-500 focus:ring-0" placeholder="可留白">
                    </label>
                </section>

                <section class="overflow-hidden rounded-md border border-gray-300 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-300 bg-gray-50 px-4 py-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">商品明細</h3>
                            <p class="mt-0.5 text-xs text-gray-500">商品名稱輸入一個字即可查詢。於備註欄按 Enter 可新增下一列。</p>
                        </div>
                        <button type="button" id="add-item-row" class="inline-flex items-center rounded-md border border-gray-800 bg-gray-800 px-3 py-2 text-sm font-medium text-white hover:bg-gray-700">新增品項</button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-[780px] w-full table-fixed border-collapse text-sm">
                            <thead class="bg-gray-100 text-left text-xs font-semibold text-gray-600">
                                <tr>
                                    <th class="w-[5%] border-b border-r border-gray-300 px-2 py-3 text-center">項次</th>
                                    <th class="w-[33%] border-b border-r border-gray-300 px-3 py-3">商品名稱</th>
                                    <th class="w-[12%] border-b border-r border-gray-300 px-3 py-3 text-right">數量</th>
                                    <th class="w-[16%] border-b border-r border-gray-300 px-3 py-3 text-right">單價</th>
                                    <th class="w-[16%] border-b border-r border-gray-300 px-3 py-3 text-right">金額</th>
                                    <th class="w-[13%] border-b border-r border-gray-300 px-3 py-3">備註</th>
                                    <th class="w-[5%] border-b border-gray-300 px-2 py-3 text-center">刪除</th>
                                </tr>
                            </thead>
                            <tbody id="item-rows">
                                @php
                                    $oldItems = old('items', [
                                        ['name' => '', 'quantity' => 1, 'unit_price' => '', 'remark' => ''],
                                    ]);
                                @endphp
                                @foreach ($oldItems as $index => $item)
                                    <tr class="item-row border-b border-gray-300 align-top">
                                        <td class="item-number border-r border-gray-300 px-2 py-3 text-center text-gray-500"></td>
                                        <td class="border-r border-gray-300 px-2 py-2">
                                            <input type="text" name="items[{{ $index }}][name]" value="{{ $item['name'] ?? '' }}" class="product-name block w-full border-0 bg-transparent px-1 py-1.5 shadow-none focus:ring-0" list="product-suggestions-{{ $index }}" data-lookup-url="{{ $productLookupUrl }}" autocomplete="off" placeholder="商品名稱" required>
                                            <datalist id="product-suggestions-{{ $index }}"></datalist>
                                        </td>
                                        <td class="border-r border-gray-300 px-2 py-2">
                                            <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" min="1" class="item-quantity block w-full border-0 bg-transparent px-1 py-1.5 text-right shadow-none focus:ring-0" required>
                                        </td>
                                        <td class="border-r border-gray-300 px-2 py-2">
                                            <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}" min="0" step="0.01" class="item-unit-price block w-full border-0 bg-transparent px-1 py-1.5 text-right shadow-none focus:ring-0" required>
                                        </td>
                                        <td class="border-r border-gray-300 px-3 py-3"><div class="item-line-total text-right font-medium text-gray-800">0.00</div></td>
                                        <td class="border-r border-gray-300 px-2 py-2"><input type="text" name="items[{{ $index }}][remark]" value="{{ $item['remark'] ?? '' }}" class="item-remark block w-full border-0 bg-transparent px-1 py-1.5 shadow-none focus:ring-0" placeholder="備註"></td>
                                        <td class="px-2 py-2 text-center"><button type="button" class="remove-item rounded px-2 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">刪除</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="4" class="border-r border-gray-300 px-3 py-4 text-right text-sm font-semibold text-gray-700">合計金額</td>
                                    <td class="border-r border-gray-300 px-3 py-4"><div id="grand-total" class="text-right text-lg font-bold text-gray-900">0.00</div></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 pt-4">
                    <p class="text-xs text-gray-500">送出後即建立正式單據並更新庫存，請確認資料正確。</p>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">取消</a>
                        <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-500">{{ $submitLabel }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <template id="item-row-template">
        <tr class="item-row border-b border-gray-300 align-top">
            <td class="item-number border-r border-gray-300 px-2 py-3 text-center text-gray-500"></td>
            <td class="border-r border-gray-300 px-2 py-2"><input type="text" class="product-name block w-full border-0 bg-transparent px-1 py-1.5 shadow-none focus:ring-0" autocomplete="off" placeholder="商品名稱" required><datalist></datalist></td>
            <td class="border-r border-gray-300 px-2 py-2"><input type="number" min="1" value="1" class="item-quantity block w-full border-0 bg-transparent px-1 py-1.5 text-right shadow-none focus:ring-0" required></td>
            <td class="border-r border-gray-300 px-2 py-2"><input type="number" min="0" step="0.01" class="item-unit-price block w-full border-0 bg-transparent px-1 py-1.5 text-right shadow-none focus:ring-0" required></td>
            <td class="border-r border-gray-300 px-3 py-3"><div class="item-line-total text-right font-medium text-gray-800">0.00</div></td>
            <td class="border-r border-gray-300 px-2 py-2"><input type="text" class="item-remark block w-full border-0 bg-transparent px-1 py-1.5 shadow-none focus:ring-0" placeholder="備註"></td>
            <td class="px-2 py-2 text-center"><button type="button" class="remove-item rounded px-2 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">刪除</button></td>
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
                return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); };
            };

            const fetchJson = async (url) => {
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                return response.ok ? response.json() : [];
            };

            const setOptions = (datalist, options, formatter) => {
                datalist.innerHTML = '';
                options.forEach((option) => {
                    const node = document.createElement('option');
                    const data = formatter(option);
                    node.value = data.value;
                    if (data.label) node.label = data.label;
                    datalist.appendChild(node);
                });
            };

            const updateRowNumbers = () => rowsContainer.querySelectorAll('.item-row').forEach((row, index) => { row.querySelector('.item-number').textContent = index + 1; });

            const updateGrandTotal = () => {
                let total = 0;
                rowsContainer.querySelectorAll('.item-row').forEach((row) => {
                    total += Number(row.querySelector('.item-quantity').value || 0) * Number(row.querySelector('.item-unit-price').value || 0);
                });
                document.getElementById('grand-total').textContent = total.toFixed(2);
            };

            const updateLineTotal = (row) => {
                const quantity = Number(row.querySelector('.item-quantity').value || 0);
                const unitPrice = Number(row.querySelector('.item-unit-price').value || 0);
                row.querySelector('.item-line-total').textContent = (quantity * unitPrice).toFixed(2);
                updateGrandTotal();
            };

            const bindProductLookup = (row, index) => {
                const nameInput = row.querySelector('.product-name');
                const unitPriceInput = row.querySelector('.item-unit-price');
                const datalist = row.querySelector('datalist');
                const datalistId = `product-suggestions-${index}`;
                datalist.id = datalistId;
                nameInput.setAttribute('list', datalistId);
                nameInput.name = `items[${index}][name]`;
                row.querySelector('.item-quantity').name = `items[${index}][quantity]`;
                unitPriceInput.name = `items[${index}][unit_price]`;
                row.querySelector('.item-remark').name = `items[${index}][remark]`;
                row.dataset.productMap = '[]';

                nameInput.addEventListener('input', debounce(async () => {
                    const keyword = nameInput.value.trim();
                    if (keyword.length < 1) { datalist.innerHTML = ''; return; }
                    const items = await fetchJson(`${nameInput.dataset.lookupUrl}?keyword=${encodeURIComponent(keyword)}`);
                    row.dataset.productMap = JSON.stringify(items);
                    setOptions(datalist, items, (item) => ({ value: item.name, label: `${item.part_no} / ${item.name}` }));
                }));

                nameInput.addEventListener('change', () => {
                    const match = JSON.parse(row.dataset.productMap || '[]').find((item) => item.name === nameInput.value.trim());
                    if (match && !unitPriceInput.value) {
                        unitPriceInput.value = Number(match[defaultPriceField] || 0).toFixed(2);
                        updateLineTotal(row);
                    }
                });
            };

            const addRow = (focusProduct = false) => {
                const fragment = rowTemplate.content.cloneNode(true);
                fragment.querySelector('.product-name').dataset.lookupUrl = @json($productLookupUrl);
                rowsContainer.appendChild(fragment);
                const row = rowsContainer.lastElementChild;
                bindProductLookup(row, rowIndex);
                updateLineTotal(row);
                updateRowNumbers();
                rowIndex += 1;
                if (focusProduct) row.querySelector('.product-name').focus();
            };

            rowsContainer.querySelectorAll('.item-row').forEach((row, index) => { bindProductLookup(row, index); updateLineTotal(row); });
            updateRowNumbers();

            rowsContainer.addEventListener('input', (event) => {
                if (event.target.matches('.item-quantity, .item-unit-price')) updateLineTotal(event.target.closest('.item-row'));
            });

            rowsContainer.addEventListener('keydown', (event) => {
                if (event.key !== 'Enter' || event.shiftKey) return;
                const row = event.target.closest('.item-row');
                if (!row) return;
                if (event.target.matches('.product-name')) { event.preventDefault(); row.querySelector('.item-quantity').focus(); }
                else if (event.target.matches('.item-quantity')) { event.preventDefault(); row.querySelector('.item-unit-price').focus(); }
                else if (event.target.matches('.item-unit-price')) { event.preventDefault(); row.querySelector('.item-remark').focus(); }
                else if (event.target.matches('.item-remark')) { event.preventDefault(); addRow(true); }
            });

            rowsContainer.addEventListener('click', (event) => {
                if (!event.target.matches('.remove-item')) return;
                if (rowsContainer.querySelectorAll('.item-row').length === 1) {
                    const row = rowsContainer.querySelector('.item-row');
                    row.querySelector('.product-name').value = '';
                    row.querySelector('.item-quantity').value = 1;
                    row.querySelector('.item-unit-price').value = '';
                    row.querySelector('.item-remark').value = '';
                    updateLineTotal(row);
                    row.querySelector('.product-name').focus();
                    return;
                }
                event.target.closest('.item-row').remove();
                updateRowNumbers();
                updateGrandTotal();
            });

            addRowButton.addEventListener('click', () => addRow(true));
            partyInput.addEventListener('input', debounce(async () => {
                const keyword = partyInput.value.trim();
                if (keyword.length < 1) { partyDatalist.innerHTML = ''; return; }
                setOptions(partyDatalist, await fetchJson(`${partyInput.dataset.lookupUrl}?keyword=${encodeURIComponent(keyword)}`), (value) => ({ value }));
            }));
        });
    </script>
</x-app-layout>
