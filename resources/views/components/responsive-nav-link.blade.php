@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-xl bg-blue-50 px-4 py-3 text-start text-sm font-bold text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-700 transition duration-150 ease-in-out'
            : 'block w-full rounded-xl px-4 py-3 text-start text-sm font-semibold text-gray-600 hover:bg-gray-50 hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
