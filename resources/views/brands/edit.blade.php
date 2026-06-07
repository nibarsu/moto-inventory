<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            編輯品牌
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('brands.update', $brand) }}" class="space-y-6 p-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="code" value="品牌代碼" />
                        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $brand->code)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('code')" />
                    </div>

                    <div>
                        <x-input-label for="name" value="品牌名稱" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $brand->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="english_name" value="英文名稱" />
                        <x-text-input id="english_name" name="english_name" type="text" class="mt-1 block w-full" :value="old('english_name', $brand->english_name)" />
                        <x-input-error class="mt-2" :messages="$errors->get('english_name')" />
                    </div>

                    <div>
                        <x-input-label for="remark" value="備註" />
                        <textarea id="remark" name="remark" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark', $brand->remark) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('remark')" />
                    </div>

                    <div>
                        <label for="is_active" class="inline-flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_active', $brand->is_active))>
                            <span class="ms-2 text-sm text-gray-600">啟用</span>
                        </label>
                        <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('brands.index') }}" class="text-sm text-gray-600 hover:text-gray-900">取消</a>
                        <x-primary-button>更新</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
