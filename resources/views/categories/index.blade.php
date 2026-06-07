<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                商品分類
            </h2>
            <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                新增分類
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">分類代碼</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">分類名稱</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">類型</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">是否啟用</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">操作</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($categories as $category)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                                            <a href="{{ route('categories.show', $category) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $category->code }}
                                            </a>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $category->name }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                            {{ $category->type === 'part' ? '零件' : '整車' }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                                {{ $category->is_active ? '啟用' : '停用' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium">
                                            <a href="{{ route('categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900">編輯</a>
                                            <form method="POST" action="{{ route('categories.destroy', $category) }}" class="ms-4 inline" onsubmit="return confirm('確定要刪除此商品分類嗎？');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">刪除</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">目前沒有商品分類資料。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
