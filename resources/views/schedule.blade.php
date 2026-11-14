<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Schedule') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:py-8">
        <section x-data="{ activeDay: '1' }" class="space-y-5" aria-label="{{ __('Match day filters') }}">
            <div class="flex flex-wrap gap-2" role="group" aria-label="{{ __('Filter matches by day') }}">
                @foreach (['1', '2', '3'] as $day)
                    <button
                        type="button"
                        @click="activeDay = '{{ $day }}'"
                        :aria-pressed="activeDay === '{{ $day }}' ? 'true' : 'false'"
                        :class="activeDay === '{{ $day }}' ? 'bg-blue-900 text-white shadow-sm' : 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
                        class="inline-flex min-h-11 items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
                    >
                        <span>{{ __('Day :day', ['day' => $day]) }}</span>
                        <span class="rounded-lg bg-white/15 px-2 py-1 text-xs" :class="activeDay === '{{ $day }}' ? 'text-white' : 'bg-gray-100 text-gray-600'">
                            {{ $dayCounts[$day] }}
                        </span>
                    </button>
                @endforeach
            </div>

            @if ($matches->isNotEmpty())
                <div class="grid gap-4">
                    @foreach ($matches as $match)
                        <article
                            x-show="activeDay === '{{ $match->schedule_day }}'"
                            x-transition.opacity
                            data-match-card="{{ $match->id }}"
                            data-match-day="{{ $match->schedule_day }}"
                            class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md"
                        >
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-4 sm:px-6">
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm">
                                    <span class="font-bold text-gray-900">{{ __('Match #:number', ['number' => $match->id]) }}</span>
                                    <span class="text-gray-300" aria-hidden="true">|</span>
                                    <span class="font-medium text-gray-600">{{ $match->tarikh->format('D, d M Y') }}</span>
                                    <span class="font-medium text-gray-600">{{ $match->display_time }}</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-800">
                                        {{ $match->display_group }}
                                    </span>
                                    <span
                                        data-match-status="{{ $match->is_completed ? 'completed' : 'upcoming' }}"
                                        class="{{ $match->is_completed ? 'bg-indigo-50 text-indigo-800' : 'bg-gray-100 text-gray-700' }} rounded-full px-3 py-1 text-xs font-bold"
                                    >
                                        {{ $match->is_completed ? __('Completed') : __('Upcoming') }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] items-center gap-2 px-3 py-6 sm:gap-6 sm:px-8 sm:py-8">
                                <div class="flex min-w-0 flex-col items-center gap-3 text-center">
                                    <x-team-emblem :team="$match->pasukan1" />
                                    <h2 class="max-w-full break-words text-xs font-extrabold leading-5 text-gray-900 sm:text-sm">
                                        {{ $match->pasukan1->nama_pasukan }}
                                    </h2>
                                </div>

                                <div class="min-w-[4.5rem] text-center">
                                    @if ($match->is_completed)
                                        <p class="rounded-xl bg-[#111b48] px-3 py-2 text-xl font-extrabold tracking-wider text-white sm:px-5 sm:py-3 sm:text-2xl" data-match-score>
                                            {{ $match->skor_pasukan_1 }} - {{ $match->skor_pasukan_2 }}
                                        </p>
                                    @else
                                        <span class="inline-flex rounded-xl bg-gray-100 px-4 py-2 text-sm font-extrabold tracking-[0.2em] text-gray-500 sm:px-5 sm:py-3">
                                            VS
                                        </span>
                                    @endif
                                </div>

                                <div class="flex min-w-0 flex-col items-center gap-3 text-center">
                                    <x-team-emblem :team="$match->pasukan2" />
                                    <h2 class="max-w-full break-words text-xs font-extrabold leading-5 text-gray-900 sm:text-sm">
                                        {{ $match->pasukan2->nama_pasukan }}
                                    </h2>
                                </div>
                            </div>

                            <div class="flex flex-col gap-4 border-t border-gray-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                                <div class="flex min-w-0 items-center gap-2 text-sm text-gray-600">
                                    <svg class="h-4 w-4 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.05 3.636a7 7 0 0 1 9.9 9.9L10 18.485l-4.95-4.95a7 7 0 0 1 0-9.9ZM10 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="truncate">{{ $match->tempat }}</span>
                                </div>
                                <a
                                    href="{{ route('matches.show', $match) }}"
                                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-amber-400 px-4 py-2.5 text-sm font-extrabold text-[#111b48] transition hover:bg-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
                                >
                                    {{ __('View Match') }}
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                @foreach (['1', '2', '3'] as $day)
                    @if ($dayCounts[$day] === 0)
                        <div
                            x-show="activeDay === '{{ $day }}'"
                            class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center"
                        >
                            <p class="text-base font-bold text-gray-800">{{ __('No matches scheduled for Day :day.', ['day' => $day]) }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Fixtures for this day will appear here when available.') }}</p>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center">
                    <p class="text-lg font-bold text-gray-900">{{ __('No matches scheduled yet.') }}</p>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Confirmed fixtures will appear here once they are added.') }}</p>
                </div>
            @endif
        </section>

        <section class="flex flex-col gap-5 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ __('Tournament insights') }}</p>
                <h2 class="mt-1 text-lg font-extrabold text-gray-900">{{ __('Match Analysis') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Explore scorers, clean sheets and disciplinary statistics.') }}</p>
            </div>
            <a
                href="{{ route('statistics') }}"
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-[#111b48] px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
            >
                {{ __('Analysis Match') }}
                <span aria-hidden="true">&rarr;</span>
            </a>
        </section>
    </div>
</x-app-layout>
