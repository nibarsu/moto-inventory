<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            編輯保養紀錄
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('maintenance-records.update', $maintenanceRecord) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <x-input-label for="record_no" value="紀錄編號" />
                                <x-text-input id="record_no" name="record_no" type="text" class="mt-1 block w-full" :value="old('record_no', $maintenanceRecord->record_no)" required />
                                <x-input-error :messages="$errors->get('record_no')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="service_date" value="保養日期" />
                                <x-text-input id="service_date" name="service_date" type="date" class="mt-1 block w-full" :value="old('service_date', $maintenanceRecord->service_date->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('service_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="customer_id" value="客戶" />
                                <select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">請選擇客戶</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" @selected(old('customer_id', $maintenanceRecord->customer_id) == $customer->id)>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="vehicle_id" value="車型" />
                                <select id="vehicle_id" name="vehicle_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">請選擇車型</option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $maintenanceRecord->vehicle_id) == $vehicle->id)>{{ $vehicle->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="repair_order_id" value="關聯工單" />
                                <select id="repair_order_id" name="repair_order_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">不指定工單</option>
                                    @foreach ($repairOrders as $repairOrder)
                                        <option value="{{ $repairOrder->id }}" @selected(old('repair_order_id', $maintenanceRecord->repair_order_id) == $repairOrder->id)>{{ $repairOrder->wo_no }}{{ $repairOrder->customer ? ' / '.$repairOrder->customer->name : '' }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('repair_order_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="service_type" value="保養類型" />
                                <x-text-input id="service_type" name="service_type" type="text" class="mt-1 block w-full" :value="old('service_type', $maintenanceRecord->service_type)" required />
                                <x-input-error :messages="$errors->get('service_type')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="plate_no" value="車牌號碼" />
                                <x-text-input id="plate_no" name="plate_no" type="text" class="mt-1 block w-full" :value="old('plate_no', $maintenanceRecord->plate_no)" />
                                <x-input-error :messages="$errors->get('plate_no')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="mileage" value="目前里程" />
                                <x-text-input id="mileage" name="mileage" type="number" min="0" class="mt-1 block w-full" :value="old('mileage', $maintenanceRecord->mileage)" />
                                <x-input-error :messages="$errors->get('mileage')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="next_service_date" value="下次保養日期" />
                                <x-text-input id="next_service_date" name="next_service_date" type="date" class="mt-1 block w-full" :value="old('next_service_date', optional($maintenanceRecord->next_service_date)->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('next_service_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="next_service_mileage" value="下次保養里程" />
                                <x-text-input id="next_service_mileage" name="next_service_mileage" type="number" min="0" class="mt-1 block w-full" :value="old('next_service_mileage', $maintenanceRecord->next_service_mileage)" />
                                <x-input-error :messages="$errors->get('next_service_mileage')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="service_content" value="保養內容" />
                            <textarea id="service_content" name="service_content" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('service_content', $maintenanceRecord->service_content) }}</textarea>
                            <x-input-error :messages="$errors->get('service_content')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="remark" value="備註" />
                            <textarea id="remark" name="remark" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark', $maintenanceRecord->remark) }}</textarea>
                            <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>儲存</x-primary-button>
                            <a href="{{ route('maintenance-records.index') }}" class="text-sm text-gray-600 hover:text-gray-900">返回</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
