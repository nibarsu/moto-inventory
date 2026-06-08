<div class="rounded-md bg-gray-50 p-4 text-sm text-gray-700">
    進貨單號：{{ $purchaseOrder->po_no }}
</div>

<div>
    <x-input-label for="item_type" value="類型" />
    <select id="item_type" name="item_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="togglePurchaseItemSelects()" required>
        <option value="part" @selected(old('item_type', $item?->item_type ?? 'part') === 'part')>零件</option>
        <option value="vehicle" @selected(old('item_type', $item?->item_type) === 'vehicle')>整車</option>
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('item_type')" />
</div>

<div id="part-item-select-wrapper">
    <x-input-label for="part_item_id" value="零件商品" />
    <select id="part_item_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="syncPurchaseItem()">
        <option value="">請選擇</option>
        @foreach ($parts as $part)
            <option value="{{ $part->id }}" @selected(old('item_type', $item?->item_type ?? 'part') === 'part' && (string) old('item_id', $item?->item_id) === (string) $part->id)>
                {{ $part->part_no }} - {{ $part->name }}
            </option>
        @endforeach
    </select>
</div>

<div id="vehicle-item-select-wrapper">
    <x-input-label for="vehicle_item_id" value="整車商品" />
    <select id="vehicle_item_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="syncPurchaseItem()">
        <option value="">請選擇</option>
        @foreach ($vehicles as $vehicle)
            <option value="{{ $vehicle->id }}" @selected(old('item_type', $item?->item_type) === 'vehicle' && (string) old('item_id', $item?->item_id) === (string) $vehicle->id)>
                {{ $vehicle->model_code }} - {{ $vehicle->name }}
            </option>
        @endforeach
    </select>
</div>

<input type="hidden" id="item_id" name="item_id" value="{{ old('item_id', $item?->item_id) }}">
<x-input-error class="mt-2" :messages="$errors->get('item_id')" />

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <x-input-label for="quantity" value="數量" />
        <x-text-input id="quantity" name="quantity" type="number" min="1" class="mt-1 block w-full" :value="old('quantity', $item?->quantity ?? 1)" required />
        <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
    </div>

    <div>
        <x-input-label for="unit_price" value="單價" />
        <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('unit_price', $item?->unit_price ?? '0.00')" required />
        <x-input-error class="mt-2" :messages="$errors->get('unit_price')" />
    </div>
</div>

<div>
    <x-input-label for="remark" value="備註" />
    <textarea id="remark" name="remark" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark', $item?->remark) }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('remark')" />
</div>

<script>
    function togglePurchaseItemSelects() {
        const type = document.getElementById('item_type').value;
        document.getElementById('part-item-select-wrapper').style.display = type === 'part' ? 'block' : 'none';
        document.getElementById('vehicle-item-select-wrapper').style.display = type === 'vehicle' ? 'block' : 'none';
        syncPurchaseItem();
    }

    function syncPurchaseItem() {
        const type = document.getElementById('item_type').value;
        const sourceId = type === 'part' ? 'part_item_id' : 'vehicle_item_id';
        document.getElementById('item_id').value = document.getElementById(sourceId).value;
    }

    document.addEventListener('DOMContentLoaded', function () {
        togglePurchaseItemSelects();
    });
</script>
