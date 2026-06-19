<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            編輯維修工單
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('repair-orders.update', $repairOrder) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <x-input-label for="wo_no" value="工單號碼" />
                                <x-text-input id="wo_no" name="wo_no" type="text" class="mt-1 block w-full" :value="old('wo_no', $repairOrder->wo_no)" required />
                                <x-input-error :messages="$errors->get('wo_no')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="order_date" value="開單日期" />
                                <x-text-input id="order_date" name="order_date" type="date" class="mt-1 block w-full" :value="old('order_date', $repairOrder->order_date->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('order_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="customer_id" value="客戶" />
                                <select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">請選擇客戶</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" @selected(old('customer_id', $repairOrder->customer_id) == $customer->id)>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="vehicle_id" value="車型" />
                                <select id="vehicle_id" name="vehicle_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">請選擇車型</option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $repairOrder->vehicle_id) == $vehicle->id)>{{ $vehicle->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="plate_no" value="車牌號碼" />
                                <x-text-input id="plate_no" name="plate_no" type="text" class="mt-1 block w-full" :value="old('plate_no', $repairOrder->plate_no)" />
                                <x-input-error :messages="$errors->get('plate_no')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="mileage" value="里程" />
                                <x-text-input id="mileage" name="mileage" type="number" min="0" class="mt-1 block w-full" :value="old('mileage', $repairOrder->mileage)" />
                                <x-input-error :messages="$errors->get('mileage')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="status" value="狀態" />
                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    @foreach ($statusOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $repairOrder->status) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="complaint" value="客戶描述 / 故障現象" />
                            <textarea id="complaint" name="complaint" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('complaint', $repairOrder->complaint) }}</textarea>
                            <x-input-error :messages="$errors->get('complaint')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="diagnosis" value="檢查結果 / 處理內容" />
                            <textarea id="diagnosis" name="diagnosis" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('diagnosis', $repairOrder->diagnosis) }}</textarea>
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="remark" value="備註" />
                            <textarea id="remark" name="remark" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark', $repairOrder->remark) }}</textarea>
                            <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>儲存</x-primary-button>
                            <a href="{{ route('repair-orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">返回</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
