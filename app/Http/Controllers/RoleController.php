<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Support\PermissionRegistry;
use Illuminate\Support\Collection;

class RoleController extends Controller
{
    public function index()
    {
        return view('roles.index', [
            'roles' => Role::query()
                ->withCount(['permissions', 'users'])
                ->orderBy('name')
                ->paginate(10),
        ]);
    }

    public function create()
    {
        return view('roles.create', [
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->safe()->except('permission_ids'));
        $role->permissions()->sync($request->validated('permission_ids', []));

        return redirect()->route('roles.index')->with('success', '角色已建立。');
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);

        return view('roles.show', [
            'role' => $role,
            'groupLabels' => PermissionRegistry::groups(),
        ]);
    }

    public function edit(Role $role)
    {
        $role->load('permissions');

        return view('roles.edit', [
            'role' => $role,
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->safe()->except('permission_ids'));
        $role->permissions()->sync($request->validated('permission_ids', []));

        return redirect()->route('roles.index')->with('success', '角色已更新。');
    }

    public function destroy(Role $role)
    {
        if ($role->code === 'admin') {
            return redirect()->route('roles.index')->with('success', '系統管理員角色不可刪除。');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', '角色已刪除。');
    }

    /**
     * @return array<string, Collection<int, Permission>>
     */
    private function permissionGroups(): array
    {
        return Permission::query()
            ->orderBy('group_key')
            ->orderBy('name')
            ->get()
            ->groupBy('group_key')
            ->all();
    }
}
