<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">角色管理</h2>
            <a href="{{ route('roles.create') }}" class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm hover:bg-gray-700">新增角色</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">角色代碼</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">角色名稱</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">權限數</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">使用者數</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">狀態</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">操作</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($roles as $role)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                                            <a href="{{ route('roles.show', $role) }}" class="text-indigo-600 hover:text-indigo-900">{{ $role->code }}</a>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $role->name }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($role->permissions_count) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-700">{{ number_format($role->users_count) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm">
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $role->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $role->is_active ? '啟用' : '停用' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                            <a href="{{ route('roles.edit', $role) }}" class="font-medium text-indigo-600 hover:text-indigo-900">編輯</a>
                                            <form method="POST" action="{{ route('roles.destroy', $role) }}" class="ms-4 inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">刪除</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">目前沒有角色資料。</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $roles->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
