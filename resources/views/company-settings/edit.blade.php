<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">商家設定</h2>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">返回日常作業</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('company-settings.update') }}" class="space-y-6 p-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="商家名稱" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $companySetting->name)" maxlength="100" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">儲存設定</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
