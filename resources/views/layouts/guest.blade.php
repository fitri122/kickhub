<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="relative isolate flex min-h-screen flex-col overflow-hidden bg-[#f3f6fb]">
            <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
                <div class="absolute -left-40 -top-40 h-[30rem] w-[30rem] rounded-full bg-blue-900/10 blur-3xl"></div>
                <div class="absolute -right-40 top-1/4 h-[32rem] w-[32rem] rounded-full bg-amber-300/20 blur-3xl"></div>
                <div class="absolute -bottom-48 left-1/4 h-[28rem] w-[28rem] rounded-full bg-indigo-400/10 blur-3xl"></div>
            </div>

            <main class="flex flex-1 items-center justify-center px-4 py-10 sm:px-6">
                <div class="w-full max-w-md">
                    <div class="mb-6 flex flex-col items-center text-center">
                        <a href="{{ url('/') }}" aria-label="{{ __('KickHub home') }}" class="inline-flex rounded-3xl bg-white p-3 shadow-lg shadow-blue-950/10 ring-1 ring-blue-950/5 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-800 focus:ring-offset-2">
                            <img src="{{ asset('images/Logo.png') }}" alt="{{ __('KickHub logo') }}" class="block h-20 w-20 object-contain sm:h-24 sm:w-24">
                        </a>
                        <p class="mt-4 text-xs font-extrabold uppercase tracking-[0.2em] text-[#111b48]">{{ __('KICKHUB · SISTEM PENGURUSAN KEJOHANAN BOLA SEPAK SUKOV MELAKA & NEGERI SEMBILAN') }}</p>
                    </div>

                    <div class="overflow-hidden rounded-3xl border border-white/80 bg-white/95 p-6 shadow-[0_24px_70px_rgba(17,27,72,0.12)] ring-1 ring-[#111b48]/5 backdrop-blur sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>

            @unless (request()->routeIs('login', 'password.request'))
                <x-site-footer />
            @endunless
        </div>
    </body>
</html>
