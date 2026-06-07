<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            新增倉庫
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('warehouses.store') }}" class="space-y-6 p-6">
                    @csrf

                    <div>
                        <x-input-label for="code" value="倉庫代碼" />
                        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('code')" />
                    </div>

                    <div>
                        <x-input-label for="name" value="倉庫名稱" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="address" value="地址" />
                        <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="contact_person" value="聯絡人" />
                            <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" :value="old('contact_person')" />
                            <x-input-error class="mt-2" :messages="$errors->get('contact_person')" />
                        </div>

                        <div>
                            <x-input-label for="phone" value="電話" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="remark" value="備註" />
                        <textarea id="remark" name="remark" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('remark')" />
                    </div>

                    <div>
                        <label for="is_active" class="inline-flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_active', true))>
                            <span class="ms-2 text-sm text-gray-600">啟用</span>
                        </label>
                        <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('warehouses.index') }}" class="text-sm text-gray-600 hover:text-gray-900">取消</a>
                        <x-primary-button>儲存</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
