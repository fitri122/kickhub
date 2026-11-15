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
    <body class="font-sans antialiased text-gray-900">
        <div class="relative isolate flex min-h-screen flex-col bg-gradient-to-br from-[#f7f9fc] via-[#f1f5fb] to-[#eaf0fb]">
            <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
                <div class="absolute -left-40 top-32 h-[28rem] w-[28rem] rounded-full bg-blue-900/[0.04] blur-3xl"></div>
                <div class="absolute -right-40 top-[35rem] h-[32rem] w-[32rem] rounded-full bg-amber-300/[0.08] blur-3xl"></div>
                <div class="absolute bottom-0 left-1/3 h-[26rem] w-[26rem] rounded-full bg-indigo-400/[0.05] blur-3xl"></div>
            </div>
            @include('layouts.navigation')

            @isset($header)
                <header class="border-b border-blue-100/70 bg-white/75 shadow-sm backdrop-blur-xl [&_h2]:text-[#111b48]">
                    <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="relative flex-1">
                {{ $slot }}
            </main>

            <x-site-footer />
        </div>
    </body>
</html>
