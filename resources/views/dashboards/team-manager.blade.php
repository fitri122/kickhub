<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:py-12">
        <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-[#0b1236] via-[#172b67] to-[#3157a4] px-6 py-8 text-white shadow-xl sm:px-9 sm:py-10" aria-labelledby="team-manager-heading">
            <div class="pointer-events-none absolute -right-12 -top-20 -z-10 h-72 w-72 rounded-full bg-cyan-300/20 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-amber-300">{{ __('KICKHUB · TEAM WORKSPACE') }}</p>
                    <h1 id="team-manager-heading" class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">{{ __('Team Manager') }}</h1>
                    <p class="mt-2 text-base text-blue-100">{{ __('Team Manager dashboard') }}</p>
                </div>
                <span class="inline-flex items-center gap-2 self-start rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-semibold text-blue-50 backdrop-blur sm:self-auto">
                    <span class="h-2 w-2 rounded-full bg-emerald-300" aria-hidden="true"></span>
                    {{ __('Workspace') }}
                </span>
            </div>
        </section>

        <section aria-labelledby="manager-links-heading">
            <div class="mb-5">
                <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-800">{{ __('Tournament centre') }}</p>
                <h2 id="manager-links-heading" class="mt-1 text-xl font-black text-[#111b48]">{{ __('Quick access') }}</h2>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <a href="{{ route('schedule') }}" class="group rounded-2xl border border-white bg-white/90 p-5 shadow-lg shadow-blue-950/5 transition hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-blue-50 text-blue-900" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="3.5" y="5" width="17" height="16" rx="2" />
                            <path stroke-linecap="round" d="M7.5 3v4M16.5 3v4M3.5 9.5h17M8 13h3M8 16.5h3M14 13h2.5" />
                        </svg>
                    </span>
                    <span class="mt-4 flex items-center justify-between gap-3">
                        <span>
                            <span class="block font-extrabold text-[#111b48]">{{ __('Match Schedule') }}</span>
                            <span class="mt-1 block text-sm text-gray-500">{{ __('View upcoming and completed fixtures.') }}</span>
                        </span>
                        <span class="text-lg text-blue-800 transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                    </span>
                </a>

                <a href="{{ route('league-table') }}" class="group rounded-2xl border border-white bg-white/90 p-5 shadow-lg shadow-blue-950/5 transition hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-amber-50 text-amber-800" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20V10m8 10V4m8 16v-7M2 20h20" />
                        </svg>
                    </span>
                    <span class="mt-4 flex items-center justify-between gap-3">
                        <span>
                            <span class="block font-extrabold text-[#111b48]">{{ __('League Standings') }}</span>
                            <span class="mt-1 block text-sm text-gray-500">{{ __('Follow team rankings and points.') }}</span>
                        </span>
                        <span class="text-lg text-blue-800 transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                    </span>
                </a>

                <a href="{{ route('statistics') }}" class="group rounded-2xl border border-white bg-white/90 p-5 shadow-lg shadow-blue-950/5 transition hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-indigo-50 text-indigo-800" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 14h17M8 15v-4m5 4V7m5 8v-6" />
                        </svg>
                    </span>
                    <span class="mt-4 flex items-center justify-between gap-3">
                        <span>
                            <span class="block font-extrabold text-[#111b48]">{{ __('Tournament Statistics') }}</span>
                            <span class="mt-1 block text-sm text-gray-500">{{ __('Explore scorers and clean sheets.') }}</span>
                        </span>
                        <span class="text-lg text-blue-800 transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                    </span>
                </a>
            </div>
        </section>
    </div>
</x-app-layout>
