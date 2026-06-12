<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            新增銷貨單
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('sales-orders.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <x-input-label for="so_no" value="銷貨單號" />
                                <x-text-input id="so_no" name="so_no" type="text" class="mt-1 block w-full" :value="old('so_no')" required />
                                <x-input-error :messages="$errors->get('so_no')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="order_date" value="銷貨日期" />
                                <x-text-input id="order_date" name="order_date" type="date" class="mt-1 block w-full" :value="old('order_date', now()->toDateString())" required />
                                <x-input-error :messages="$errors->get('order_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="delivery_date" value="預計出貨日" />
                                <x-text-input id="delivery_date" name="delivery_date" type="date" class="mt-1 block w-full" :value="old('delivery_date')" />
                                <x-input-error :messages="$errors->get('delivery_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="customer_id" value="客戶" />
                                <select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">請選擇客戶</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="warehouse_id" value="出貨倉庫" />
                                <select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">請選擇倉庫</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('warehouse_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="status" value="狀態" />
                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @foreach (['draft' => 'draft', 'confirmed' => 'confirmed', 'completed' => 'completed', 'cancelled' => 'cancelled'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', 'draft') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="total_amount" value="總金額" />
                                <x-text-input id="total_amount" name="total_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('total_amount', '0.00')" required />
                                <x-input-error :messages="$errors->get('total_amount')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="remark" value="備註" />
                            <textarea id="remark" name="remark" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark') }}</textarea>
                            <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>儲存</x-primary-button>
                            <a href="{{ route('sales-orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
