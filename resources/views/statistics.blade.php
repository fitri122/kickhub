<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Statistics') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <section aria-labelledby="match-statistics-heading">
                <div class="mb-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ __('KickHub overview') }}</p>
                    <h1 id="match-statistics-heading" class="mt-1 text-2xl font-extrabold text-gray-900 sm:text-3xl">
                        {{ __('Match Statistics') }}
                    </h1>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-xs font-extrabold text-blue-900">M</span>
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ __('All fixtures') }}</span>
                        </div>
                        <p class="mt-5 text-3xl font-extrabold text-gray-900">{{ number_format($totalMatches) }}</p>
                        <h2 class="mt-1 text-sm font-medium text-gray-600">{{ __('Total Matches') }}</h2>
                    </article>

                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-xs font-extrabold text-indigo-800">G</span>
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ __('Players on score sheet') }}</span>
                        </div>
                        <p class="mt-5 text-3xl font-extrabold text-gray-900">{{ number_format($totalScorers) }}</p>
                        <h2 class="mt-1 text-sm font-medium text-gray-600">{{ __('Total Scorer') }}</h2>
                    </article>

                    <a
                        href="{{ route('statistics.yellow-cards') }}"
                        aria-label="{{ __('View Yellow Card list') }}"
                        class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-yellow-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-yellow-50 text-xs font-extrabold text-yellow-800">YC</span>
                            <span class="text-lg text-gray-300 transition group-hover:translate-x-1 group-hover:text-yellow-700" aria-hidden="true">&rarr;</span>
                        </div>
                        <p class="mt-5 text-3xl font-extrabold text-gray-900">{{ number_format($yellowCardTotal) }}</p>
                        <h2 class="mt-1 text-sm font-medium text-gray-600">{{ __('Yellow Card') }}</h2>
                    </a>

                    <a
                        href="{{ route('statistics.red-cards') }}"
                        aria-label="{{ __('View Red Card list') }}"
                        class="group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-red-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-xs font-extrabold text-red-800">RC</span>
                            <span class="text-lg text-gray-300 transition group-hover:translate-x-1 group-hover:text-red-700" aria-hidden="true">&rarr;</span>
                        </div>
                        <p class="mt-5 text-3xl font-extrabold text-gray-900">{{ number_format($redCardTotal) }}</p>
                        <h2 class="mt-1 text-sm font-medium text-gray-600">{{ __('Red Card') }}</h2>
                    </a>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm" aria-labelledby="top-scorer-heading">
                    <div class="border-b border-gray-100 px-5 py-4 sm:px-6">
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ __('Player leaderboard') }}</p>
                        <h2 id="top-scorer-heading" class="mt-1 text-lg font-bold text-gray-900">{{ __('Top Scorer') }}</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[360px] text-left">
                            <caption class="sr-only">{{ __('Top three scorers') }}</caption>
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="w-14 px-4 py-3 text-center text-xs font-bold uppercase tracking-wider text-gray-500">#</th>
                                    <th scope="col" class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Player') }}</th>
                                    <th scope="col" class="w-24 px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Score') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($topScorers as $scorer)
                                    <tr class="transition-colors hover:bg-blue-50/50">
                                        <td class="px-4 py-4 text-center text-sm font-semibold text-gray-500">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="break-words text-sm font-bold text-gray-900">{{ $scorer->player_name }}</span>
                                                @if ($loop->first)
                                                    <svg class="h-4 w-4 shrink-0 text-amber-500" viewBox="0 0 20 20" fill="currentColor" role="img" aria-label="{{ __('Top scorer') }}">
                                                        <path d="M5 2a1 1 0 0 0-1 1v2a4 4 0 0 0 3 3.87V11H5a1 1 0 0 0-1 1v2H3a1 1 0 0 0-1 1v2h16v-2a1 1 0 0 0-1-1h-1v-2a1 1 0 0 0-1-1h-2V8.87A4 4 0 0 0 17 5V3a1 1 0 0 0-1-1H5Zm1 3V4h2v3.7A2 2 0 0 1 6 5Zm8 2.7V4h2v1a2 2 0 0 1-2 2.7ZM9 8h2v4H9V8Zm-3 6h8v2H6v-2Z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">{{ $scorer->team_name }}</p>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <span class="text-lg font-extrabold text-gray-900">{{ $scorer->score }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-12 text-center text-sm text-gray-500">
                                            {{ __('No goals have been recorded yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm" aria-labelledby="clean-sheet-heading">
                    <div class="border-b border-gray-100 px-5 py-4 sm:px-6">
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ __('Team leaderboard') }}</p>
                        <h2 id="clean-sheet-heading" class="mt-1 text-lg font-bold text-gray-900">{{ __('Clean Sheet') }}</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[320px] text-left">
                            <caption class="sr-only">{{ __('Top three teams by clean sheets') }}</caption>
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="w-14 px-4 py-3 text-center text-xs font-bold uppercase tracking-wider text-gray-500">#</th>
                                    <th scope="col" class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Team') }}</th>
                                    <th scope="col" class="w-28 px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Clean Sheets') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($cleanSheets as $team)
                                    <tr class="transition-colors hover:bg-blue-50/50">
                                        <td class="px-4 py-4 text-center text-sm font-semibold text-gray-500">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">{{ $team['team_name'] }}</td>
                                        <td class="px-5 py-4 text-right text-lg font-extrabold text-gray-900">{{ $team['clean_sheets'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-12 text-center text-sm text-gray-500">
                                            {{ __('No clean sheets have been recorded yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
