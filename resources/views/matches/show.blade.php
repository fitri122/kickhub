<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:py-12">
        <a
            href="{{ route('schedule') }}"
            class="text-sm font-semibold text-blue-800 hover:text-blue-950"
        >
            &larr; {{ __('Back to Schedule') }}
        </a>

        <section class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm" aria-labelledby="match-details-heading">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-100 px-5 py-5 sm:px-8">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ __('Match #:number', ['number' => $match->id]) }}</p>
                    <h1 id="match-details-heading" class="mt-1 text-2xl font-extrabold text-gray-900 sm:text-3xl">
                        {{ __('Match Details') }}
                    </h1>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-800">{{ $groupLabel }}</span>
                    <span class="{{ $isCompleted ? 'bg-indigo-50 text-indigo-800' : 'bg-gray-100 text-gray-700' }} rounded-full px-3 py-1.5 text-xs font-bold">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] items-center gap-2 px-4 py-8 sm:gap-8 sm:px-10 sm:py-12">
                <div class="flex min-w-0 flex-col items-center gap-3 text-center">
                    <x-team-emblem :team="$match->pasukan1" />
                    <h2 class="max-w-full break-words text-sm font-extrabold leading-6 text-gray-900 sm:text-lg">
                        {{ $match->pasukan1->nama_pasukan }}
                    </h2>
                </div>

                <div class="min-w-[5rem] text-center sm:min-w-[8rem]">
                    @if ($isCompleted)
                        <p class="text-3xl font-extrabold tracking-wider text-[#111b48] sm:text-5xl" data-match-score>
                            {{ $match->skor_pasukan_1 }} - {{ $match->skor_pasukan_2 }}
                        </p>
                        <p class="mt-2 text-xs font-bold uppercase tracking-widest text-gray-400">{{ __('Full time') }}</p>
                    @else
                        <p class="text-xl font-extrabold tracking-[0.2em] text-gray-400 sm:text-2xl">VS</p>
                    @endif
                </div>

                <div class="flex min-w-0 flex-col items-center gap-3 text-center">
                    <x-team-emblem :team="$match->pasukan2" />
                    <h2 class="max-w-full break-words text-sm font-extrabold leading-6 text-gray-900 sm:text-lg">
                        {{ $match->pasukan2->nama_pasukan }}
                    </h2>
                </div>
            </div>

            <dl class="grid gap-px border-t border-gray-100 bg-gray-100 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white px-5 py-4 sm:px-6">
                    <dt class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Match date') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $match->tarikh->format('D, d M Y') }}</dd>
                </div>
                <div class="bg-white px-5 py-4 sm:px-6">
                    <dt class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Kick-off') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $kickoffTime }}</dd>
                </div>
                <div class="bg-white px-5 py-4 sm:px-6">
                    <dt class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Stadium') }}</dt>
                    <dd class="mt-1 break-words text-sm font-semibold text-gray-900">{{ $match->tempat }}</dd>
                </div>
                <div class="bg-white px-5 py-4 sm:px-6">
                    <dt class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Match status') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $statusLabel }}</dd>
                </div>
            </dl>
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm" aria-labelledby="goal-scorers-heading">
                <div class="border-b border-gray-100 px-5 py-4 sm:px-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ __('Match report') }}</p>
                    <h2 id="goal-scorers-heading" class="mt-1 text-lg font-extrabold text-gray-900">{{ __('Goal Scorers') }}</h2>
                </div>
                <div class="p-5 sm:p-6">
                    @forelse ($goalScorers as $scorer)
                        <div class="flex items-center justify-between gap-4 border-b border-gray-100 py-4 first:pt-0 last:border-b-0 last:pb-0">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-green-50 text-green-700" aria-hidden="true">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="m9 8 3-2 3 2 1 3-2 3h-4l-2-3 1-3Zm3-2v4m-4-1 4 1 4-1m-6 5-2 4m4-4 2 4" />
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="break-words text-sm font-bold text-gray-900">{{ $scorer['player_name'] }}</p>
                                    <p class="mt-1 break-words text-xs text-gray-500">{{ $scorer['team_name'] }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ __('Goal minute not recorded') }}</p>
                                </div>
                            </div>
                            <span class="shrink-0 rounded-full bg-green-50 px-3 py-1.5 text-xs font-extrabold text-green-800">
                                {{ $scorer['goals'] }} {{ $scorer['goals'] === 1 ? __('goal') : __('goals') }}
                            </span>
                        </div>
                    @empty
                        <p class="rounded-xl bg-gray-50 px-4 py-8 text-center text-sm text-gray-500">
                            {{ __('No goals have been recorded for this match.') }}
                        </p>
                    @endforelse
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm" aria-labelledby="match-timeline-heading">
                <div class="border-b border-gray-100 px-5 py-4 sm:px-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ __('Match events') }}</p>
                    <h2 id="match-timeline-heading" class="mt-1 text-lg font-extrabold text-gray-900">{{ __('Timeline') }}</h2>
                </div>
                <div class="p-5 sm:p-6">
                    @if ($events->isNotEmpty())
                        <ol class="space-y-5">
                            @foreach ($events as $event)
                                <li
                                    data-match-event="{{ $event['type'] }}"
                                    data-match-minute="{{ $event['minute'] ?? 'unknown' }}"
                                    class="relative flex gap-4"
                                >
                                    <div class="relative flex w-12 shrink-0 justify-center">
                                        @if (! $loop->last)
                                            <span class="absolute bottom-[-1.5rem] top-8 w-px bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        @if ($event['type'] === 'goal')
                                            <span class="grid h-8 w-8 place-items-center rounded-full bg-green-50 text-green-700" aria-hidden="true">
                                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <circle cx="12" cy="12" r="9" />
                                                    <path d="m9 8 3-2 3 2 1 3-2 3h-4l-2-3 1-3Zm3-2v4m-4-1 4 1 4-1m-6 5-2 4m4-4 2 4" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="{{ $event['type'] === 'yellow-card' ? 'bg-yellow-400' : 'bg-red-600' }} h-6 w-4 rounded-sm shadow-sm" aria-hidden="true"></span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1 pb-1">
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                            @if ($event['minute'] !== null)
                                                <time class="text-xs font-extrabold text-gray-500">{{ $event['minute'] }}'</time>
                                            @else
                                                <span class="text-xs font-semibold text-gray-400">{{ __('Minute not recorded') }}</span>
                                            @endif
                                            <span class="text-sm font-bold text-gray-900">
                                                {{ match ($event['type']) {
                                                    'goal' => __('Goal'),
                                                    'yellow-card' => __('Yellow card'),
                                                    'red-card' => __('Red card'),
                                                } }}
                                            </span>
                                        </div>
                                        <p class="mt-1 break-words text-sm text-gray-700">{{ $event['player_name'] }}</p>
                                        <p class="mt-0.5 break-words text-xs text-gray-500">{{ $event['team_name'] }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <p class="rounded-xl bg-gray-50 px-4 py-8 text-center text-sm text-gray-500">
                            {{ __('No match events have been recorded.') }}
                        </p>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
