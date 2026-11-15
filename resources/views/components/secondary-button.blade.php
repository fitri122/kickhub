<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-xl border border-blue-100 bg-white px-4 py-2 font-semibold text-xs uppercase tracking-widest text-[#111b48] shadow-sm transition duration-150 ease-in-out hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
