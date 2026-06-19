<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold leading-tight text-gray-800">編輯角色</h2></x-slot>
    @include('roles.partials.form', ['action' => route('roles.update', $role), 'method' => 'PUT', 'role' => $role])
</x-app-layout>
