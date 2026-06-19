<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">編輯使用者角色</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('user-access.update', $user) }}" class="space-y-6 p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-6 md:grid-cols-2">
                        <div><div class="text-sm font-medium text-gray-500">姓名</div><div class="mt-1 text-base text-gray-900">{{ $user->name }}</div></div>
                        <div><div class="text-sm font-medium text-gray-500">Email</div><div class="mt-1 text-base text-gray-900">{{ $user->email }}</div></div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">指派角色</h3>
                        @php
                            $selectedRoles = collect(old('role_ids', $user->roles->pluck('id')->all()))->map(fn ($id) => (int) $id)->all();
                        @endphp
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            @foreach ($roles as $role)
                                <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-4 text-sm text-gray-700">
                                    <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(in_array($role->id, $selectedRoles, true))>
                                    <span>
                                        <span class="block font-semibold text-gray-900">{{ $role->name }}</span>
                                        <span class="mt-1 block text-xs text-gray-500">{{ $role->code }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('role_ids')" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>儲存</x-primary-button>
                        <a href="{{ route('user-access.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">取消</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
