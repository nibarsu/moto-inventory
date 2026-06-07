<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            新增整車商品
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('vehicles.store') }}" class="space-y-6 p-6">
                    @csrf

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="model_code" value="車型代碼" />
                            <x-text-input id="model_code" name="model_code" type="text" class="mt-1 block w-full" :value="old('model_code')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('model_code')" />
                        </div>

                        <div>
                            <x-input-label for="barcode" value="條碼" />
                            <x-text-input id="barcode" name="barcode" type="text" class="mt-1 block w-full" :value="old('barcode')" />
                            <x-input-error class="mt-2" :messages="$errors->get('barcode')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="name" value="車名" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="brand_id" value="品牌" />
                            <select id="brand_id" name="brand_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">請選擇</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" @selected((string) old('brand_id') === (string) $brand->id)>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('brand_id')" />
                        </div>

                        <div>
                            <x-input-label for="category_id" value="分類" />
                            <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">請選擇</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-4">
                        <div>
                            <x-input-label for="year" value="年份" />
                            <x-text-input id="year" name="year" type="number" min="1900" max="2100" class="mt-1 block w-full" :value="old('year')" />
                            <x-input-error class="mt-2" :messages="$errors->get('year')" />
                        </div>

                        <div>
                            <x-input-label for="color" value="顏色" />
                            <x-text-input id="color" name="color" type="text" class="mt-1 block w-full" :value="old('color')" />
                            <x-input-error class="mt-2" :messages="$errors->get('color')" />
                        </div>

                        <div>
                            <x-input-label for="engine_displacement" value="排氣量" />
                            <x-text-input id="engine_displacement" name="engine_displacement" type="text" class="mt-1 block w-full" :value="old('engine_displacement')" />
                            <x-input-error class="mt-2" :messages="$errors->get('engine_displacement')" />
                        </div>

                        <div>
                            <x-input-label for="sale_price" value="售價" />
                            <x-text-input id="sale_price" name="sale_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('sale_price', '0.00')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('sale_price')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="last_cost_price" value="最近成本" />
                        <x-text-input id="last_cost_price" name="last_cost_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('last_cost_price', '0.00')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('last_cost_price')" />
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
                        <a href="{{ route('vehicles.index') }}" class="text-sm text-gray-600 hover:text-gray-900">取消</a>
                        <x-primary-button>儲存</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
