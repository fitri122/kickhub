<x-app-layout>
    @php
        $badgeClasses = $isYellowCard
            ? 'border-yellow-200 bg-yellow-50 text-yellow-800'
            : 'border-red-200 bg-red-50 text-red-800';
        $iconClasses = $isYellowCard
            ? 'bg-yellow-50 text-yellow-800'
            : 'bg-red-50 text-red-800';
    @endphp

    <div class="mx-auto max-w-5xl space-y-6 px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <a href="{{ route('statistics') }}" class="text-sm font-semibold text-blue-800 hover:text-blue-950">
            &larr; {{ __('Back to Statistics') }}
        </a>

        <div class="space-y-6">
            <section class="flex flex-col gap-5 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-7">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-xs font-extrabold {{ $iconClasses }}">
                        {{ $isYellowCard ? 'YC' : 'RC' }}
                    </span>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ __('Disciplinary records') }}</p>
                        <h1 class="mt-1 text-2xl font-extrabold text-gray-900 sm:text-3xl">{{ strtoupper($cardTitle) }}</h1>
                        <p class="mt-2 text-sm text-gray-500">{{ __('Players ranked by cards received') }}</p>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-5 py-3 sm:min-w-[9rem] sm:text-right">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Total Cards') }}</p>
                    <p class="mt-1 text-2xl font-extrabold text-gray-900">{{ number_format($totalCards) }}</p>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm" aria-labelledby="card-players-heading">
                <div class="border-b border-gray-100 px-5 py-4 sm:px-6">
                    <h2 id="card-players-heading" class="text-lg font-bold text-gray-900">{{ __('Players') }}</h2>
                </div>

                <ol class="divide-y divide-gray-100">
                    @forelse ($cardPlayers as $player)
                        <li data-player-id="{{ $player->player_id }}" class="flex items-center justify-between gap-4 px-5 py-4 transition-colors hover:bg-gray-50 sm:px-6">
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
