<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            編輯銷貨出庫單
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('sales-shipments.update', $salesShipment) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <x-input-label for="shipment_no" value="出庫單號" />
                                <x-text-input id="shipment_no" name="shipment_no" type="text" class="mt-1 block w-full" :value="old('shipment_no', $salesShipment->shipment_no)" required />
                                <x-input-error :messages="$errors->get('shipment_no')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="shipment_date" value="出庫日期" />
                                <x-text-input id="shipment_date" name="shipment_date" type="date" class="mt-1 block w-full" :value="old('shipment_date', $salesShipment->shipment_date->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('shipment_date')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="remark" value="備註" />
                            <textarea id="remark" name="remark" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark', $salesShipment->remark) }}</textarea>
                            <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>更新</x-primary-button>
                            <a href="{{ route('sales-shipments.show', $salesShipment) }}" class="text-sm text-gray-600 hover:text-gray-900">返回出庫單</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
