<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            庫存調整
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('stocks.update-adjustment') }}" class="space-y-6 p-6">
                    @csrf

                    <div>
                        <x-input-label for="item_type" value="類型" />
                        <select id="item_type" name="item_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="toggleItemSelects()" required>
                            <option value="part" @selected(old('item_type', 'part') === 'part')>零件</option>
                            <option value="vehicle" @selected(old('item_type') === 'vehicle')>整車</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('item_type')" />
                    </div>

                    <div id="part-select-wrapper">
                        <x-input-label for="part_item_id" value="零件商品" />
                        <select id="part_item_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="syncSelectedItem()">
                            <option value="">請選擇</option>
                            @foreach ($parts as $part)
                                <option value="{{ $part->id }}" @selected(old('item_type', 'part') === 'part' && (string) old('item_id') === (string) $part->id)>
                                    {{ $part->part_no }} - {{ $part->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="vehicle-select-wrapper">
                        <x-input-label for="vehicle_item_id" value="整車商品" />
                        <select id="vehicle_item_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="syncSelectedItem()">
                            <option value="">請選擇</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected(old('item_type') === 'vehicle' && (string) old('item_id') === (string) $vehicle->id)>
                                    {{ $vehicle->model_code }} - {{ $vehicle->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" id="item_id" name="item_id" value="{{ old('item_id') }}">
                    <x-input-error class="mt-2" :messages="$errors->get('item_id')" />

                    <div>
                        <x-input-label for="warehouse_id" value="倉庫" />
                        <select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">請選擇</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" @selected((string) old('warehouse_id') === (string) $warehouse->id)>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('warehouse_id')" />
                    </div>

                    <div>
                        <x-input-label for="adjusted_quantity" value="調整後數量" />
                        <x-text-input id="adjusted_quantity" name="adjusted_quantity" type="number" min="0" class="mt-1 block w-full" :value="old('adjusted_quantity', 0)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('adjusted_quantity')" />
                    </div>

                    <div>
                        <x-input-label for="remark" value="備註" />
                        <textarea id="remark" name="remark" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('remark')" />
                    </div>

                    <div class="flex items-center justify-end">
                        <x-primary-button>送出調整</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleItemSelects() {
            const type = document.getElementById('item_type').value;
            document.getElementById('part-select-wrapper').style.display = type === 'part' ? 'block' : 'none';
            document.getElementById('vehicle-select-wrapper').style.display = type === 'vehicle' ? 'block' : 'none';
            syncSelectedItem();
        }

        function syncSelectedItem() {
            const type = document.getElementById('item_type').value;
            const sourceId = type === 'part' ? 'part_item_id' : 'vehicle_item_id';
            document.getElementById('item_id').value = document.getElementById(sourceId).value;
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleItemSelects();
        });
    </script>
</x-app-layout>
