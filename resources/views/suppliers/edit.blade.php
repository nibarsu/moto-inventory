<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            編輯供應商
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-6 p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="code" value="供應商代碼" />
                            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $supplier->code)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('code')" />
                        </div>

                        <div>
                            <x-input-label for="name" value="供應商名稱" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $supplier->name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="tax_id" value="統一編號" />
                            <x-text-input id="tax_id" name="tax_id" type="text" class="mt-1 block w-full" :value="old('tax_id', $supplier->tax_id)" />
                            <x-input-error class="mt-2" :messages="$errors->get('tax_id')" />
                        </div>

                        <div>
                            <x-input-label for="contact_person" value="聯絡人" />
                            <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" :value="old('contact_person', $supplier->contact_person)" />
                            <x-input-error class="mt-2" :messages="$errors->get('contact_person')" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-3">
                        <div>
                            <x-input-label for="phone" value="電話" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $supplier->phone)" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>

                        <div>
                            <x-input-label for="mobile" value="手機" />
                            <x-text-input id="mobile" name="mobile" type="text" class="mt-1 block w-full" :value="old('mobile', $supplier->mobile)" />
                            <x-input-error class="mt-2" :messages="$errors->get('mobile')" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $supplier->email)" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="address" value="地址" />
                        <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $supplier->address) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>

                    <div>
                        <x-input-label for="remark" value="備註" />
                        <textarea id="remark" name="remark" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark', $supplier->remark) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('remark')" />
                    </div>

                    <div>
                        <label for="is_active" class="inline-flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_active', $supplier->is_active))>
                            <span class="ms-2 text-sm text-gray-600">啟用</span>
                        </label>
                        <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('suppliers.index') }}" class="text-sm text-gray-600 hover:text-gray-900">取消</a>
                        <x-primary-button>更新</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
