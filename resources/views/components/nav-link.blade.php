@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex h-10 self-center items-center rounded-xl bg-[#111b48] px-3 text-sm font-bold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2 transition duration-150 ease-in-out'
            : 'inline-flex h-10 self-center items-center rounded-xl px-3 text-sm font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
