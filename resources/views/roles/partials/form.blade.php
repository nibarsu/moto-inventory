<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ $action }}" class="space-y-6 p-6">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <x-input-label for="code" value="角色代碼" />
                        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $role?->code)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('code')" />
                    </div>
                    <div>
                        <x-input-label for="name" value="角色名稱" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $role?->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="remark" value="備註" />
                    <textarea id="remark" name="remark" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remark', $role?->remark) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('remark')" />
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_active', $role?->is_active ?? true))>
                    啟用角色
                </label>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900">角色權限</h3>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        @php
                            $selectedPermissions = collect(old('permission_ids', $role?->permissions?->pluck('id')->all() ?? []))->map(fn ($id) => (int) $id)->all();
                            $groupLabels = \App\Support\PermissionRegistry::groups();
                        @endphp
                        @foreach ($permissionGroups as $groupKey => $permissions)
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $groupLabels[$groupKey] ?? $groupKey }}</div>
                                <div class="mt-3 space-y-2">
                                    @foreach ($permissions as $permission)
                                        <label class="flex items-start gap-3 text-sm text-gray-700">
                                            <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(in_array($permission->id, $selectedPermissions, true))>
                                            <span>{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('permission_ids')" />
                </div>

                <div class="flex items-center gap-3">
                    <x-primary-button>儲存</x-primary-button>
                    <a href="{{ route('roles.index') }}" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50">取消</a>
                </div>
            </form>
        </div>
    </div>
</div>
