<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            編輯進貨入庫
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('purchase-receipts.update', $purchaseReceipt) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <x-input-label for="receipt_no" value="入庫單號" />
                                <x-text-input id="receipt_no" name="receipt_no" type="text" class="mt-1 block w-full" :value="old('receipt_no', $purchaseReceipt->receipt_no)" required />
                                <x-input-error :messages="$errors->get('receipt_no')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="receipt_date" value="入庫日期" />
                                <x-text-input id="receipt_date" name="receipt_date" type="date" class="mt-1 block w-full" :value="old('receipt_date', $purchaseReceipt->receipt_date->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('receipt_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-3">
                            <div>
                                <x-input-label value="進貨單號" />
                                <div class="mt-1 rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                    {{ $purchaseReceipt->purchaseOrder?->po_no ?: '-' }}
                                </div>
                            </div>
                            <div>
                                <x-input-label value="供應商" />
                                <div class="mt-1 rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                    {{ $purchaseReceipt->supplier?->name ?: '-' }}
                                </div>
                            </div>
                            <div>
                                <x-input-label value="倉庫" />
                                <div class="mt-1 rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                    {{ $purchaseReceipt->warehouse?->name ?: '-' }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="remark" value="備註" />
                            <textarea id="remark" name="remark" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark', $purchaseReceipt->remark) }}</textarea>
                            <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>儲存</x-primary-button>
                            <a href="{{ route('purchase-receipts.show', $purchaseReceipt) }}" class="text-sm text-gray-600 hover:text-gray-900">取消</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
