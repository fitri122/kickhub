<x-app-layout>
    @php
        $badgeClasses = $isYellowCard
            ? 'border-yellow-200 bg-yellow-50 text-yellow-800'
            : 'border-red-200 bg-red-50 text-red-800';
        $iconClasses = 'bg-white/10 text-white ring-1 ring-white/20';
        $heroClasses = $isYellowCard
            ? 'from-amber-500 via-amber-600 to-orange-700'
            : 'from-rose-700 via-red-700 to-red-900';
    @endphp

    <div class="mx-auto max-w-5xl space-y-6 px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <a href="{{ route('statistics') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-blue-100 bg-white/80 px-3 text-sm font-bold text-blue-900 shadow-sm transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
            <span aria-hidden="true">&larr;</span> {{ __('Back to Statistics') }}
        </a>

        <div class="space-y-6">
            <section class="relative isolate overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br {{ $heroClasses }} p-5 text-white shadow-xl sm:p-7">
                <div class="pointer-events-none absolute -right-12 -top-20 -z-10 h-64 w-64 rounded-full bg-white/20 blur-3xl" aria-hidden="true"></div>
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-xs font-extrabold {{ $iconClasses }}">
                        {{ $isYellowCard ? 'YC' : 'RC' }}
                    </span>
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-white/80">{{ __('Disciplinary records') }}</p>
                        <h1 class="mt-1 text-2xl font-black sm:text-3xl">{{ strtoupper($cardTitle) }}</h1>
                        <p class="mt-2 text-sm text-white/80">{{ __('Players ranked by cards received') }}</p>
                    </div>
                </div>
                <div class="rounded-2xl border border-white/20 bg-white/10 px-5 py-3 backdrop-blur sm:min-w-[9rem] sm:text-right">
                    <p class="text-xs font-bold uppercase tracking-wide text-white/75">{{ __('Total Cards') }}</p>
                    <p class="mt-1 text-2xl font-black">{{ number_format($totalCards) }}</p>
                </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-3xl border border-white bg-white/95 shadow-lg shadow-blue-950/5" aria-labelledby="card-players-heading">
                <div class="border-b border-blue-100 bg-gradient-to-r from-blue-50 to-white px-5 py-4 sm:px-6">
                    <h2 id="card-players-heading" class="text-lg font-extrabold text-[#111b48]">{{ __('Players') }}</h2>
                </div>

                <ol class="divide-y divide-gray-100">
                    @forelse ($cardPlayers as $player)
                        <li data-player-id="{{ $player->player_id }}" class="flex items-center justify-between gap-4 px-5 py-4 transition-colors odd:bg-white even:bg-blue-50/40 hover:bg-amber-50 sm:px-6">
                            <div class="flex min-w-0 items-center gap-4">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-bold text-gray-600">{{ $loop->iteration }}</span>
                                <div class="min-w-0">
                                    <h3 class="break-words text-sm font-bold text-gray-900 sm:text-base">{{ $player->player_name }}</h3>
                                    <p class="mt-1 break-words text-xs text-gray-500 sm:text-sm">{{ $player->team_name }}</p>
                                </div>
                            </div>
                            <span class="inline-flex shrink-0 items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold {{ $badgeClasses }}">
                                <span>{{ $cardTitle }}</span>
                                <span class="rounded-full bg-white/80 px-2 py-0.5 text-sm font-extrabold">{{ $player->card_count }}</span>
                            </span>
                        </li>
                    @empty
                        <li class="px-5 py-12 text-center text-sm text-gray-500">
                            {{ __('No players have received this card yet.') }}
                        </li>
                    @endforelse
                </ol>
            </section>
        </div>
    </div>
</x-app-layout>
