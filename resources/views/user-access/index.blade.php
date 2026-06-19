<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">使用者權限</h2>
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
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">姓名</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">角色</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">操作</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($users as $user)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $user->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $user->email }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $user->roles->pluck('name')->join('、') ?: '未指派' }}</td>
                                        <td class="px-4 py-3 text-right text-sm"><a href="{{ route('user-access.edit', $user) }}" class="font-medium text-indigo-600 hover:text-indigo-900">編輯</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">目前沒有使用者資料。</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $users->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
