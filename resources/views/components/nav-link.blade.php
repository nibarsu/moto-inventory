@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center whitespace-nowrap rounded-md border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-medium leading-5 text-indigo-700 focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center whitespace-nowrap rounded-md border border-transparent px-3 py-2 text-sm font-medium leading-5 text-gray-600 hover:border-gray-200 hover:bg-gray-50 hover:text-gray-900 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
