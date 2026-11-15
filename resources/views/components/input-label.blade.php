@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-[#111b48]']) }}>
    {{ $value ?? $slot }}
</label>
