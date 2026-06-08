<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            編輯進貨單
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}" class="space-y-6 p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="po_no" value="進貨單號" />
                            <x-text-input id="po_no" name="po_no" type="text" class="mt-1 block w-full" :value="old('po_no', $purchaseOrder->po_no)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('po_no')" />
                        </div>
                        <div>
                            <x-input-label for="status" value="狀態" />
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="draft" @selected(old('status', $purchaseOrder->status) === 'draft')>draft</option>
                                <option value="confirmed" @selected(old('status', $purchaseOrder->status) === 'confirmed')>confirmed</option>
                                <option value="completed" @selected(old('status', $purchaseOrder->status) === 'completed')>completed</option>
                                <option value="cancelled" @selected(old('status', $purchaseOrder->status) === 'cancelled')>cancelled</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="order_date" value="進貨日期" />
                            <x-text-input id="order_date" name="order_date" type="date" class="mt-1 block w-full" :value="old('order_date', $purchaseOrder->order_date->format('Y-m-d'))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('order_date')" />
                        </div>
                        <div>
                            <x-input-label for="expected_date" value="預計到貨日" />
                            <x-text-input id="expected_date" name="expected_date" type="date" class="mt-1 block w-full" :value="old('expected_date', optional($purchaseOrder->expected_date)->format('Y-m-d'))" />
                            <x-input-error class="mt-2" :messages="$errors->get('expected_date')" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="supplier_id" value="供應商" />
                            <select id="supplier_id" name="supplier_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">請選擇</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected((string) old('supplier_id', $purchaseOrder->supplier_id) === (string) $supplier->id)>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('supplier_id')" />
                        </div>
                        <div>
                            <x-input-label for="warehouse_id" value="入庫倉庫" />
                            <select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">請選擇</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" @selected((string) old('warehouse_id', $purchaseOrder->warehouse_id) === (string) $warehouse->id)>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('warehouse_id')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="total_amount" value="實際進貨金額" />
                        <x-text-input id="total_amount" name="total_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('total_amount', $purchaseOrder->total_amount)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('total_amount')" />
                    </div>

                    <div>
                        <x-input-label for="remark" value="備註" />
                        <textarea id="remark" name="remark" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark', $purchaseOrder->remark) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('remark')" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('purchase-orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">取消</a>
                        <x-primary-button>更新</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
