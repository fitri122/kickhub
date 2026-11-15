@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-gray-200 bg-white shadow-sm focus:border-blue-800 focus:ring-blue-800 disabled:bg-gray-100 disabled:text-gray-500']) }}>
