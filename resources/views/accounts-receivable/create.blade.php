<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            新增應收帳款
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('accounts-receivable.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <x-input-label for="ar_no" value="應收編號" />
                                <x-text-input id="ar_no" name="ar_no" type="text" class="mt-1 block w-full" :value="old('ar_no', $defaultArNo)" required />
                                <x-input-error :messages="$errors->get('ar_no')" class="mt-2" />
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
                                <x-input-label for="source_type" value="來源類型" />
                                <x-text-input id="source_type" name="source_type" type="text" class="mt-1 block w-full" :value="old('source_type')" />
                                <x-input-error :messages="$errors->get('source_type')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="source_id" value="來源編號 ID" />
                                <x-text-input id="source_id" name="source_id" type="number" min="1" class="mt-1 block w-full" :value="old('source_id')" />
                                <x-input-error :messages="$errors->get('source_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="ar_date" value="應收日期" />
                                <x-text-input id="ar_date" name="ar_date" type="date" class="mt-1 block w-full" :value="old('ar_date', now()->toDateString())" required />
                                <x-input-error :messages="$errors->get('ar_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="due_date" value="到期日" />
                                <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date')" />
                                <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="total_amount" value="應收金額" />
                                <x-text-input id="total_amount" name="total_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('total_amount', '0.00')" required />
                                <x-input-error :messages="$errors->get('total_amount')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="received_amount" value="已收金額" />
                                <x-text-input id="received_amount" name="received_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('received_amount', '0.00')" required />
                                <x-input-error :messages="$errors->get('received_amount')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="remark" value="備註" />
                            <textarea id="remark" name="remark" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark') }}</textarea>
                            <x-input-error :messages="$errors->get('remark')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>儲存</x-primary-button>
                            <a href="{{ route('accounts-receivable.index') }}" class="text-sm text-gray-600 hover:text-gray-900">返回</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
