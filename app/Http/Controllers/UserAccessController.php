<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\Role;
use App\Models\User;

class UserAccessController extends Controller
{
    public function index()
    {
        return view('user-access.index', [
            'users' => User::query()->with('roles')->orderBy('name')->paginate(10),
        ]);
    }

    public function edit(User $user)
    {
        $user->load('roles');

        return view('user-access.edit', [
            'user' => $user,
            'roles' => Role::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateUserRoleRequest $request, User $user)
    {
        $user->roles()->sync($request->validated('role_ids', []));

        return redirect()->route('user-access.index')->with('success', '使用者角色已更新。');
    }
}
