<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold leading-tight text-gray-800">新增角色</h2></x-slot>
    @include('roles.partials.form', ['action' => route('roles.store'), 'method' => 'POST', 'role' => null])
</x-app-layout>
