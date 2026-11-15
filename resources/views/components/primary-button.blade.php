<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-xl border border-transparent bg-[#111b48] px-4 py-2 font-semibold text-xs uppercase tracking-widest text-white shadow-sm transition duration-150 ease-in-out hover:bg-blue-900 focus:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2 active:bg-[#0b1236]']) }}>
    {{ $slot }}
</button>
