<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">角色詳情</h2>
            <a href="{{ route('roles.edit', $role) }}" class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm hover:bg-gray-700">編輯角色</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="grid gap-6 p-6 text-gray-900 md:grid-cols-2">
                    <div><div class="text-sm font-medium text-gray-500">角色代碼</div><div class="mt-1">{{ $role->code }}</div></div>
                    <div><div class="text-sm font-medium text-gray-500">角色名稱</div><div class="mt-1">{{ $role->name }}</div></div>
                    <div><div class="text-sm font-medium text-gray-500">狀態</div><div class="mt-1">{{ $role->is_active ? '啟用' : '停用' }}</div></div>
                    <div><div class="text-sm font-medium text-gray-500">備註</div><div class="mt-1">{{ $role->remark ?: '-' }}</div></div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900">權限清單</h3>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        @foreach ($role->permissions->groupBy('group_key') as $groupKey => $permissions)
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $groupLabels[$groupKey] ?? $groupKey }}</div>
                                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                                    @foreach ($permissions as $permission)
                                        <li>{{ $permission->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-900">使用者</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead><tr><th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">姓名</th><th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th></tr></thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($role->users as $user)
                                    <tr><td class="px-4 py-3 text-sm text-gray-700">{{ $user->name }}</td><td class="px-4 py-3 text-sm text-gray-700">{{ $user->email }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500">目前沒有使用者指派到此角色。</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
