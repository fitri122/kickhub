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

        <section class="mt-12" aria-labelledby="teams-heading">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-800">{{ __('Meet the contenders') }}</p>
                    <h2 id="teams-heading" class="mt-1 text-2xl font-extrabold text-gray-900 sm:text-3xl">
                        {{ __('Tournament Teams') }}
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
                                <img
                                    src="{{ $team->logo ? (str_starts_with($team->logo, 'http') || str_starts_with($team->logo, 'images/') ? asset($team->logo) : asset('storage/'.$team->logo)) : asset('images/team-placeholder.svg') }}"
                                    alt="{{ __(':team logo', ['team' => $team->nama_pasukan]) }}"
                                    class="h-full w-full object-contain"
                                    loading="lazy"
                                >
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
