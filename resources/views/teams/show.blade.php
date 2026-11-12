<x-app-layout>
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
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[720px] table-fixed divide-y divide-gray-200 text-left">
                            <colgroup>
                                <col class="w-16">
                                <col>
                                <col class="w-32">
                                <col class="w-20">
                                <col class="w-32">
                                <col class="w-28">
                            </colgroup>
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">#</th>
                                    <th scope="col" class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Name') }}</th>
                                    <th scope="col" class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Jersey Num') }}</th>
                                    <th scope="col" class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Age') }}</th>
                                    <th scope="col" class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                                    <th scope="col" class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($players as $player)
                                    <tr class="h-16 transition-colors hover:bg-blue-50/50">
                                        <td class="px-5 py-4 text-center align-middle text-sm text-gray-500">{{ $players->firstItem() + $loop->index }}</td>
                                        <td class="px-5 py-4 align-middle text-sm font-semibold text-gray-900">{{ $player->nama }}</td>
                                        <td class="px-5 py-4 text-center align-middle text-sm text-gray-700">{{ $player->no_jersi }}</td>
                                        <td class="px-5 py-4 text-center align-middle text-sm text-gray-700">{{ $player->umur ?? '—' }}</td>
                                        <td class="px-5 py-4 text-center align-middle">
                                            @if ($player->sekatan_count > 0)
                                                <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">{{ __('Blacklist') }}</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">{{ __('Active') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-center align-middle">
                                            <button
                                                type="button"
                                                data-player-name="{{ $player->nama }}"
                                                data-player-age="{{ $player->umur ?? '' }}"
                                                data-player-role="{{ $player->role_pemain }}"
                                                data-player-image="{{ $player->gambar ? asset($player->gambar) : '' }}"
                                                x-on:click="$dispatch('open-player-details', { name: $el.dataset.playerName, age: $el.dataset.playerAge, role: $el.dataset.playerRole, image: $el.dataset.playerImage || null })"
                                                class="rounded-lg bg-blue-900 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
                                                aria-label="{{ __('View :player', ['player' => $player->nama]) }}"
                                            >
                                                {{ __('View') }}
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-500">
                                            {{ __('No players have been registered for this team yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($players->hasPages())
                    <div class="mt-5">
                        {{ $players->links() }}
                    </div>
                @endif
            </div>

            <div id="coaches-panel" role="tabpanel" aria-labelledby="coaches-tab" x-show="activeTab === 'coaches'" style="display: none" class="mt-5">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[600px] table-fixed divide-y divide-gray-200 text-left">
                            <colgroup>
                                <col class="w-16">
                                <col>
                                <col class="w-24">
                                <col class="w-36">
                                <col class="w-32">
                            </colgroup>
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">#</th>
                                    <th scope="col" class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Name') }}</th>
                                    <th scope="col" class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Age') }}</th>
                                    <th scope="col" class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                                    <th scope="col" class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($coaches as $coach)
                                    <tr class="h-16 transition-colors hover:bg-blue-50/50">
                                        <td class="px-5 py-4 text-center align-middle text-sm text-gray-500">{{ $loop->iteration }}</td>
                                        <td class="px-5 py-4 align-middle text-sm font-semibold text-gray-900">{{ $coach['nama'] }}</td>
                                        <td class="px-5 py-4 text-center align-middle text-sm text-gray-700">{{ $coach['umur'] }}</td>
                                        <td class="px-5 py-4 text-center align-middle">
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-green-100 text-green-800' => $coach['status'] === 'Active',
                                                'bg-gray-100 text-gray-700' => $coach['status'] !== 'Active',
                                            ])>
                                                {{ __($coach['status']) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-center align-middle">
                                            <button
                                                type="button"
                                                data-coach-name="{{ $coach['nama'] }}"
                                                data-coach-age="{{ $coach['umur'] }}"
                                                x-on:click="$dispatch('open-coach-details', { name: $el.dataset.coachName, age: $el.dataset.coachAge })"
                                                class="rounded-lg bg-blue-900 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
                                                aria-label="{{ __('View :coach', ['coach' => $coach['nama']]) }}"
                                            >
                                                {{ __('View') }}
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-500">
                                            {{ __('No coaches have been registered for this team yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                    <div class="mt-3 min-h-8"></div>
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
                <div class="flex h-36 w-36 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50 sm:h-40 sm:w-40"></div>
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
