<x-app-layout>
    @php
        $samplePositions = ['Center Back', 'Left Back', 'Right Wing', 'Midfield', 'Striker'];
    @endphp

    <div
        x-data="{ activeTab: 'players', selectedPlayer: null, selectedCoach: null }"
        x-on:open-player-details="selectedPlayer = $event.detail; $dispatch('open-modal', 'player-details')"
        x-on:open-coach-details="selectedCoach = $event.detail; $dispatch('open-modal', 'coach-details')"
    >
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
        <a href="{{ url('/') }}" class="text-sm font-semibold text-blue-800 hover:text-blue-950">
            &larr; {{ __('Back to Dashboard') }}
        </a>

        <section class="mt-5 flex flex-col items-center gap-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:flex-row sm:p-8">
            <div class="flex h-36 w-36 shrink-0 items-center justify-center rounded-xl bg-gray-50 p-4 sm:h-40 sm:w-40">
                @if ($team->logo)
                    <img
                        src="{{ str_starts_with($team->logo, 'http') || str_starts_with($team->logo, 'images/') ? asset($team->logo) : asset('storage/'.$team->logo) }}"
                        alt="{{ __(':team logo', ['team' => $team->nama_pasukan]) }}"
                        class="h-full w-full object-contain"
                    >
                @endif
            </div>
            <div class="min-w-0 flex-1 text-center sm:text-left">
                <p class="text-sm font-bold uppercase tracking-widest text-blue-800">{{ __('SuKoV    Team') }}</p>
                <h1 class="mt-2 break-words text-2xl font-extrabold leading-tight text-gray-900 sm:text-3xl">{{ $team->nama_pasukan }}</h1>
                <p class="mt-3 text-gray-600">{{ $team->nama_kolej }}</p>
            </div>
        </section>

        <section class="mt-8">
            <div class="flex border-b border-gray-200" role="tablist" aria-label="{{ __('Team information') }}">
                <button
                    id="coaches-tab"
                    type="button"
                    role="tab"
                    aria-controls="coaches-panel"
                    @click="activeTab = 'coaches'"
                    :aria-selected="activeTab === 'coaches'"
                    :class="activeTab === 'coaches' ? 'border-blue-900 text-blue-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
                    class="border-b-2 px-5 py-3 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-inset"
                >
                    {{ __('Coaches') }}
                </button>
                <button
                    id="players-tab"
                    type="button"
                    role="tab"
                    aria-controls="players-panel"
                    @click="activeTab = 'players'"
                    :aria-selected="activeTab === 'players'"
                    :class="activeTab === 'players' ? 'border-blue-900 text-blue-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
                    class="border-b-2 px-5 py-3 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-inset"
                >
                    {{ __('Players') }}
                </button>
            </div>

            <div id="players-panel" role="tabpanel" aria-labelledby="players-tab" x-show="activeTab === 'players'" class="mt-5">
                <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ __('Team roster') }}</p>
                        <h2 class="mt-1 text-xl font-extrabold text-gray-900">{{ __('Players') }}</h2>
                    </div>
                    <p class="text-sm text-gray-500">
                        {{ trans_choice(':count player|:count players', $players->total(), ['count' => $players->total()]) }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($players as $player)
                        @php
                            $position = $samplePositions[array_rand($samplePositions)];
                            $initials = collect(explode(' ', trim($player->nama)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($part) => substr($part, 0, 1))
                                ->implode('');
                        @endphp
                        <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
                            <div class="flex items-start gap-4">
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-blue-50 text-sm font-extrabold text-blue-900">
                                    @if ($player->gambar)
                                        <img src="{{ asset($player->gambar) }}" alt="{{ $player->nama }}" class="h-full w-full object-cover">
                                    @else
                                        {{ $initials }}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <h3 class="break-words font-bold text-gray-900">{{ $player->nama }}</h3>
                                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800">{{ $position }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        #{{ $player->no_jersi }} <span class="px-1 text-gray-300">•</span> {{ $player->umur ?? '—' }} {{ __('years old') }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-5 flex items-center justify-between gap-3 border-t border-gray-100 pt-4">
                                @if ($player->sekatan_count > 0)
                                    <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">{{ __('Blacklist') }}</span>
                                @else
                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">{{ __('Active') }}</span>
                                @endif
                                <button
                                    type="button"
                                    data-player-name="{{ $player->nama }}"
                                    data-player-age="{{ $player->umur ?? '' }}"
                                    data-player-role="{{ $player->role_pemain }}"
                                    data-player-image="{{ $player->gambar ? asset($player->gambar) : '' }}"
                                    data-player-position="{{ $position }}"
                                    data-player-initials="{{ $initials }}"
                                    x-on:click="$dispatch('open-player-details', { name: $el.dataset.playerName, age: $el.dataset.playerAge, role: $el.dataset.playerRole, image: $el.dataset.playerImage || null, position: $el.dataset.playerPosition, initials: $el.dataset.playerInitials })"
                                    class="rounded-lg bg-blue-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
                                    aria-label="{{ __('View :player', ['player' => $player->nama]) }}"
                                >
                                    {{ __('View') }}
                                </button>
                            </div>
                        </article>
                    @empty
                        <p class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white px-5 py-12 text-center text-sm text-gray-500">
                            {{ __('No players have been registered for this team yet.') }}
                        </p>
                    @endforelse
                </div>

                @if ($players->hasPages())
                    <div class="mt-5">
                        {{ $players->links() }}
                    </div>
                @endif
            </div>

            <div id="coaches-panel" role="tabpanel" aria-labelledby="coaches-tab" x-show="activeTab === 'coaches'" style="display: none" class="mt-5">
                <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-800">{{ __('Team staff') }}</p>
                        <h2 class="mt-1 text-xl font-extrabold text-gray-900">{{ __('Coaches') }}</h2>
                    </div>
                    <p class="text-sm text-gray-500">
                        {{ trans_choice(':count coach|:count coaches', count($coaches), ['count' => count($coaches)]) }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($coaches as $coach)
                        @php
                            $initials = collect(explode(' ', trim($coach['nama'])))
                                ->filter()
                                ->take(2)
                                ->map(fn ($part) => substr($part, 0, 1))
                                ->implode('');
                        @endphp
                        <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
                            <div class="flex items-start gap-4">
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-sm font-extrabold text-amber-800">{{ $initials }}</div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="break-words font-bold text-gray-900">{{ $coach['nama'] }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ $coach['umur'] }} {{ __('years old') }}</p>
                                </div>
                            </div>

                            <div class="mt-5 flex items-center justify-between gap-3 border-t border-gray-100 pt-4">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-green-100 text-green-800' => $coach['status'] === 'Active',
                                    'bg-gray-100 text-gray-700' => $coach['status'] !== 'Active',
                                ])>
                                    {{ __($coach['status']) }}
                                </span>
                                <button
                                    type="button"
                                    data-coach-name="{{ $coach['nama'] }}"
                                    data-coach-age="{{ $coach['umur'] }}"
                                    data-coach-initials="{{ $initials }}"
                                    x-on:click="$dispatch('open-coach-details', { name: $el.dataset.coachName, age: $el.dataset.coachAge, initials: $el.dataset.coachInitials })"
                                    class="rounded-lg bg-blue-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
                                    aria-label="{{ __('View :coach', ['coach' => $coach['nama']]) }}"
                                >
                                    {{ __('View') }}
                                </button>
                            </div>
                        </article>
                    @empty
                        <p class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white px-5 py-12 text-center text-sm text-gray-500">
                            {{ __('No coaches have been registered for this team yet.') }}
                        </p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>

    <x-modal
        name="player-details"
        maxWidth="xl"
        centered
        blurBackground
        labelledBy="player-details-heading"
        focusable
    >
        <div class="p-5 sm:p-8">
            <div class="flex flex-col items-center gap-5 text-center">
                <div class="flex h-36 w-36 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50 sm:h-40 sm:w-40">
                    <template x-if="selectedPlayer && selectedPlayer.image">
                        <img
                            x-bind:src="selectedPlayer.image"
                            x-bind:alt="selectedPlayer.name"
                            class="h-full w-full object-cover"
                        >
                    </template>
                    <template x-if="selectedPlayer && !selectedPlayer.image">
                        <span class="text-2xl font-extrabold text-blue-900" x-text="selectedPlayer.initials"></span>
                    </template>
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-baseline justify-center gap-x-2 gap-y-1">
                        <h2 id="player-details-heading" class="break-words text-xl font-bold text-gray-900 sm:text-2xl" x-text="selectedPlayer ? selectedPlayer.name : ''"></h2>
                        <span class="text-sm font-medium text-gray-500" x-text="selectedPlayer && selectedPlayer.age ? '(' + selectedPlayer.age + ')' : ''"></span>
                    </div>
                    <p class="mt-1 text-sm text-gray-600" x-text="selectedPlayer ? selectedPlayer.role : ''"></p>
                </div>
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2">
                <section class="rounded-xl border border-gray-200 p-4 sm:p-5">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Position') }}</h3>
                    <div class="mt-3">
                        <span class="inline-flex rounded-full bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-800" x-text="selectedPlayer ? selectedPlayer.position : ''"></span>
                    </div>
                </section>
                <section class="rounded-xl border border-gray-200 p-4 sm:p-5">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Achievements') }}</h3>
                    <div class="mt-3 min-h-8"></div>
                </section>
            </div>

            <div class="mt-7 flex justify-end border-t border-gray-100 pt-5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'player-details')"
                    class="rounded-lg bg-blue-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
                >
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </x-modal>

    <x-modal
        name="coach-details"
        maxWidth="xl"
        centered
        blurBackground
        labelledBy="coach-details-heading"
        focusable
    >
        <div class="p-5 sm:p-8">
            <div class="flex flex-col items-center gap-5 text-center">
                <div class="flex h-36 w-36 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-amber-50 text-2xl font-extrabold text-amber-800 sm:h-40 sm:w-40" x-text="selectedCoach ? selectedCoach.initials : ''"></div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-baseline justify-center gap-x-2 gap-y-1">
                        <h2 id="coach-details-heading" class="break-words text-xl font-bold text-gray-900 sm:text-2xl" x-text="selectedCoach ? selectedCoach.name : ''"></h2>
                        <span class="text-sm font-medium text-gray-500" x-text="selectedCoach && selectedCoach.age ? '(' + selectedCoach.age + ')' : ''"></span>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Coach') }}</p>
                </div>
            </div>

            <div class="mt-8">
                <section class="rounded-xl border border-gray-200 p-4 sm:p-5">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Achievements') }}</h3>
                    <div class="mt-3 min-h-8"></div>
                </section>
            </div>

            <div class="mt-7 flex justify-end border-t border-gray-100 pt-5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'coach-details')"
                    class="rounded-lg bg-blue-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
                >
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </x-modal>
    </div>
</x-app-layout>
