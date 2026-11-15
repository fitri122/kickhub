<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
        <section
            x-data="{ activeSlide: 0 }"
            class="overflow-hidden rounded-2xl bg-[#111b48] text-white shadow-lg"
            aria-label="{{ __('KickHub tournament highlights') }}"
        >
            <div class="grid items-center lg:grid-cols-2">
                <div class="px-6 py-10 sm:px-10 lg:px-12 lg:py-14">
                    <p class="mb-3 text-sm font-bold uppercase tracking-[0.2em] text-amber-300">
                        {{ __('KickHub : Sistem Pengurusan Kejohanan Bola Sepak SuKoV Melaka & Negeri Sembilan') }}
                    </p>
                    <h1 class="max-w-xl text-3xl font-extrabold leading-tight sm:text-4xl lg:text-5xl">
                        {{ __('Every team. One tournament.') }}
                    </h1>
                    <p class="mt-5 max-w-lg text-base leading-7 text-blue-100 sm:text-lg">
                        {{ __('Kolej Vokasional Kuala Klawang is the team that has secured three consecutive victories in 2024, 2025 and 2026. Will they make history by achieving their fourth consecutive victory next year in 2027?. See you in SuKoV Bola Sepak Melan 2027') }}
                    </p>
                    <a
                        href="{{ route('schedule') }}"
                        class="mt-7 inline-flex items-center rounded-lg bg-amber-400 px-5 py-3 text-sm font-bold text-[#111b48] transition hover:bg-amber-300 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#111b48]"
                    >
                        {{ __('View Schedule') }}
                        <span class="ms-2" aria-hidden="true">&rarr;</span>
                    </a>
                </div>

                <div class="relative min-h-64 bg-[#0b1236] sm:min-h-80 lg:min-h-[25rem]">
                    <div class="absolute inset-0">
                        <img
                            x-show="activeSlide === 0"
                            x-transition.opacity
                            src="{{ asset('images/banner1.jpeg') }}"
                            alt="{{ __('SuKoV football tournament banner') }}"
                            class="h-full w-full object-cover"
                        >
                        <img
                            x-show="activeSlide === 1"
                            x-transition.opacity
                            src="{{ asset('images/banner2.jpeg') }}"
                            alt="{{ __('SuKoV football tournament banner') }}"
                            class="h-full w-full object-cover"
                            style="display: none"
                        >
                        <img
                            x-show="activeSlide === 2"
                            x-transition.opacity
                            src="{{ asset('images/banner3.jpeg') }}"
                            alt="{{ __('SuKoV football tournament banner') }}"
                            class="h-full w-full object-cover"
                            style="display: none"
                        >
                    </div>
                    <div class="absolute inset-x-0 bottom-0 flex items-center justify-between bg-gradient-to-t from-[#090f2c]/80 to-transparent px-5 pb-5 pt-12">
                        <button
                            type="button"
                            @click="activeSlide = (activeSlide + 2) % 3"
                            aria-label="{{ __('Previous banner') }}"
                            class="grid h-10 w-10 place-items-center rounded-full bg-white/15 text-xl text-white backdrop-blur transition hover:bg-white/25 focus:outline-none focus:ring-2 focus:ring-white"
                        >
                            <span aria-hidden="true">&lsaquo;</span>
                        </button>
                        <div class="flex items-center gap-2" aria-label="{{ __('Choose banner') }}">
                            <template x-for="slide in [0, 1, 2]" :key="slide">
                                <button
                                    type="button"
                                    @click="activeSlide = slide"
                                    :aria-label="`{{ __('Show banner') }} ${slide + 1}`"
                                    :aria-current="activeSlide === slide ? 'true' : 'false'"
                                    class="h-2.5 rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#111b48]"
                                    :class="activeSlide === slide ? 'w-7 bg-amber-300' : 'w-2.5 bg-white/60 hover:bg-white'"
                                ></button>
                            </template>
                        </div>
                        <button
                            type="button"
                            @click="activeSlide = (activeSlide + 1) % 3"
                            aria-label="{{ __('Next banner') }}"
                            class="grid h-10 w-10 place-items-center rounded-full bg-white/15 text-xl text-white backdrop-blur transition hover:bg-white/25 focus:outline-none focus:ring-2 focus:ring-white"
                        >
                            <span aria-hidden="true">&rsaquo;</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative isolate mt-8 overflow-hidden rounded-3xl bg-gradient-to-br from-[#0b1236] via-[#172b67] to-[#3157a4] text-white shadow-xl" aria-labelledby="ai-promo-title">
            <div class="pointer-events-none absolute -right-16 -top-24 -z-10 h-80 w-80 rounded-full bg-cyan-300/20 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-28 left-1/4 -z-10 h-64 w-64 rounded-full bg-indigo-400/20 blur-3xl" aria-hidden="true"></div>

            <div class="grid items-center gap-8 p-6 sm:p-8 lg:grid-cols-[1.1fr_0.9fr] lg:p-10">
                <div>
                    <p class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-extrabold uppercase tracking-[0.16em] text-cyan-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 13.9 9l5.35 1.9L13.9 12.8 12 18.15l-1.9-5.35L4.75 10.9 10.1 9 12 3.75Z" />
                        </svg>
                        {{ __('KickHub AI · Powered by Gemini') }}
                    </p>
                    <h2 id="ai-promo-title" class="mt-4 max-w-xl text-3xl font-black leading-tight tracking-tight sm:text-4xl">
                        {{ __('Bola sepak lagi mudah difahami.') }}
                    </h2>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-blue-100 sm:text-base">
                        {{ __('Nak tahu peraturan, taktikal atau format kejohanan? Tanya KickHub AI dan teroka permainan dengan lebih yakin.') }}
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-4">
                        <a
                            href="{{ route('ai-chat') }}"
                            class="inline-flex min-h-12 items-center justify-center gap-3 rounded-xl bg-amber-400 px-5 py-3 text-sm font-extrabold text-[#111b48] shadow-sm transition hover:bg-amber-300 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#172b67]"
                        >
                            {{ __('Mula chat AI') }}
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                        <span class="text-sm font-medium text-blue-100">{{ __('Percuma · Tak perlu log masuk') }}</span>
                    </div>
                </div>

                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 shadow-inner backdrop-blur sm:p-5">
                    <p class="px-1 text-xs font-extrabold uppercase tracking-[0.16em] text-cyan-100">{{ __('Tanya tentang') }}</p>
                    <div class="mt-3 grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                        <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-[#0b1236]/40 p-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-cyan-300/15 text-cyan-200" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="9" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5M8.5 3.8l1 2M15.5 3.8l-1 2" />
                                </svg>
                            </span>
                            <span>
                                <span class="block text-sm font-bold">{{ __('Peraturan bola sepak') }}</span>
                                <span class="mt-0.5 block text-xs text-blue-100">{{ __('Offside, foul & kad') }}</span>
                            </span>
                        </div>
                        <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-[#0b1236]/40 p-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-violet-300/15 text-violet-200" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2ZM8 7h8M8 11h6" />
                                </svg>
                            </span>
                            <span>
                                <span class="block text-sm font-bold">{{ __('Taktik & posisi') }}</span>
                                <span class="mt-0.5 block text-xs text-blue-100">{{ __('Formasi & peranan pemain') }}</span>
                            </span>
                        </div>
                        <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-[#0b1236]/40 p-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-amber-300/15 text-amber-200" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 12 3l9 4.5-9 4.5-9-4.5ZM3 12l9 4.5 9-4.5M3 16.5 12 21l9-4.5" />
                                </svg>
                            </span>
                            <span>
                                <span class="block text-sm font-bold">{{ __('Info kejohanan') }}</span>
                                <span class="mt-0.5 block text-xs text-blue-100">{{ __('Format & kiraan mata') }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-12" aria-labelledby="teams-heading">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-800">{{ __('Meet the contenders') }}</p>
                    <h2 id="teams-heading" class="mt-1 text-2xl font-extrabold text-gray-900 sm:text-3xl">
                        {{ __('SuKoV Teams') }}
                    </h2>
                </div>
                <p class="text-sm text-gray-600">   
                    {{ trans_choice(':count registered team|:count registered teams', $teams->count(), ['count' => $teams->count()]) }}
                </p>
            </div>

            @if ($teams->isNotEmpty())
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($teams as $team)
                        <article data-team-card="{{ $team->id }}" class="flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex h-48 items-center justify-center bg-gray-50 p-5">
                                @if ($team->logo)
                                    <img
                                        src="{{ str_starts_with($team->logo, 'http') || str_starts_with($team->logo, 'images/') ? asset($team->logo) : asset('storage/'.$team->logo) }}"
                                        alt="{{ __(':team logo', ['team' => $team->nama_pasukan]) }}"
                                        class="h-full w-full object-contain"
                                        loading="lazy"
                                    >
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col p-5">
                                <h3 class="min-h-14 break-words text-base font-bold leading-7 text-gray-900">{{ $team->nama_pasukan }}</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ $team->nama_kolej }}</p>
                                <a
                                    href="{{ route('teams.show', $team) }}"
                                    class="mt-5 inline-flex w-fit items-center rounded-md bg-[#111b48] px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
                                >
                                    {{ __('View') }}
                                    <span class="ms-2" aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
                    <p class="text-base font-semibold text-gray-800">{{ __('No teams have been registered yet.') }}</p>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Registered teams will appear here.') }}</p>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
