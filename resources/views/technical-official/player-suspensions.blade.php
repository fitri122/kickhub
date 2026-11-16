<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:py-12">
        <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-[#0b1236] via-[#172b67] to-[#3157a4] px-6 py-8 text-white shadow-xl sm:px-9 sm:py-10">
            <div class="pointer-events-none absolute -right-12 -top-20 -z-10 h-72 w-72 rounded-full bg-red-300/20 blur-3xl" aria-hidden="true"></div>
            <div class="relative">
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-amber-300">{{ __('KICKHUB · MATCHDAY WORKSPACE') }}</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">{{ __('Player Suspension') }}</h1>
                <p class="mt-2 text-base text-blue-100">{{ __('One red card or two yellow cards automatically suspend a player for one match. Use manual blocking only for assaults after a match.') }}</p>
            </div>
        </section>

        <nav class="flex flex-wrap gap-3" aria-label="{{ __('Choose who to manage') }}">
            <a
                href="{{ route('technical-official.player-suspensions.index', ['section' => 'players']) }}"
                @class([
                    'inline-flex min-h-11 items-center rounded-xl px-5 text-sm font-extrabold transition focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2',
                    'bg-blue-900 text-white shadow-sm' => $section === 'players',
                    'border border-blue-100 bg-white/90 text-blue-900 hover:bg-blue-50' => $section !== 'players',
                ])
                @if ($section === 'players') aria-current="page" @endif
            >
                {{ __('Players') }}
            </a>
            <a
                href="{{ route('technical-official.player-suspensions.index', ['section' => 'coaches']) }}"
                @class([
                    'inline-flex min-h-11 items-center rounded-xl px-5 text-sm font-extrabold transition focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2',
                    'bg-blue-900 text-white shadow-sm' => $section === 'coaches',
                    'border border-blue-100 bg-white/90 text-blue-900 hover:bg-blue-50' => $section !== 'coaches',
                ])
                @if ($section === 'coaches') aria-current="page" @endif
            >
                {{ __('Coaches') }}
            </a>
        </nav>

        <section aria-labelledby="team-selection-heading">
            <div class="mb-5">
                <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-800">{{ __('Technical Official') }}</p>
                <h2 id="team-selection-heading" class="mt-1 text-xl font-black text-[#111b48]">{{ __('Choose a team') }}</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($teams as $team)
                    <a
                        href="{{ route('technical-official.player-suspensions.teams.show', ['pasukan' => $team, 'section' => $section]) }}"
                        @if ($selectedTeam?->is($team)) aria-current="page" @endif
                        @class([
                            'group rounded-2xl border p-5 shadow-lg shadow-blue-950/5 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2',
                            'border-blue-700 bg-blue-50' => $selectedTeam?->is($team),
                            'border-white bg-white/90 hover:border-blue-200' => ! $selectedTeam?->is($team),
                        ])
                    >
                        <span class="block font-extrabold text-[#111b48]">{{ $team->nama_pasukan }}</span>
                        @if ($section === 'players')
                            <span class="mt-1 block text-sm text-gray-500">{{ trans_choice(':count player|:count players', $team->pemain_count, ['count' => $team->pemain_count]) }}</span>
                        @else
                            <span class="mt-1 block text-sm text-gray-500">{{ trans_choice(':count coach|:count coaches', $team->jurulatih_count, ['count' => $team->jurulatih_count]) }}</span>
                        @endif
                    </a>
                @empty
                    <p class="rounded-2xl border border-white bg-white/90 p-5 text-sm text-gray-500">{{ __('No teams found.') }}</p>
                @endforelse
            </div>
        </section>

        @if ($selectedTeam)
            <x-modal
                name="player-suspension-team"
                :show="$selectedTeam !== null"
                maxWidth="2xl"
                centered
                blurBackground
                labelledBy="player-status-heading"
                focusable
            >
                <div class="p-5 sm:p-8">
                    <div class="text-center">
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-800">{{ $section === 'players' ? __('Player Status') : __('Coach Status') }}</p>
                        <h2 id="player-status-heading" class="mt-2 text-xl font-black text-[#111b48] sm:text-2xl">{{ $selectedTeam->nama_pasukan }}</h2>
                    </div>

                    @if (session('status'))
                        <div role="status" class="mt-6 mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mt-6 max-h-[55vh] overflow-auto rounded-xl border border-gray-200">
                        <table class="w-full min-w-[480px] text-left text-sm">
                            <thead class="sticky top-0 bg-blue-50 text-xs font-extrabold uppercase tracking-wider text-blue-950">
                                <tr>
                                    <th scope="col" class="px-5 py-4">{{ $section === 'players' ? __('Player') : __('Coach') }}</th>
                                    <th scope="col" class="px-5 py-4">{{ $section === 'players' ? __('Jersey Number') : __('Age') }}</th>
                                    <th scope="col" class="px-5 py-4 text-right">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @if ($section === 'players')
                                    @forelse ($players as $player)
                                        <tr>
                                            <th scope="row" class="px-5 py-4 font-bold text-gray-900">{{ $player->nama }}</th>
                                            <td class="px-5 py-4 text-gray-600">{{ $player->no_jersi }}</td>
                                            <td class="px-5 py-4 text-right">
                                                <div class="flex flex-col items-end gap-2">
                                                @if ($player->manual_sekatan_count > 0)
                                                    <form method="POST" action="{{ route('technical-official.player-suspensions.destroy', $player) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            type="submit"
                                                            aria-label="{{ __('Activate :player', ['player' => $player->nama]) }}"
                                                            class="inline-flex min-h-10 min-w-24 items-center justify-center rounded-full bg-red-100 px-4 text-xs font-extrabold text-red-800 transition hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-700 focus:ring-offset-2"
                                                        >
                                                            {{ __('Blocked') }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('technical-official.player-suspensions.store', $player) }}">
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            aria-label="{{ __('Block :player', ['player' => $player->nama]) }}"
                                                            class="inline-flex min-h-10 min-w-24 items-center justify-center rounded-full bg-green-100 px-4 text-xs font-extrabold text-green-800 transition hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-700 focus:ring-offset-2"
                                                        >
                                                            {{ __('Active') }}
                                                        </button>
                                                    </form>
                                                @endif
                                                    @if ($player->pending_suspensions_count > 0)
                                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">{{ __('Automatic suspension') }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-5 py-12 text-center text-sm text-gray-500">{{ __('No players found for this team.') }}</td>
                                        </tr>
                                    @endforelse
                                @else
                                    @forelse ($coaches as $coach)
                                        <tr>
                                            <th scope="row" class="px-5 py-4 font-bold text-gray-900">{{ $coach->nama }}</th>
                                            <td class="px-5 py-4 text-gray-600">{{ $coach->umur }}</td>
                                            <td class="px-5 py-4 text-right">
                                                <form method="POST" action="{{ route('technical-official.player-suspensions.coaches.toggle', $coach) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        type="submit"
                                                        aria-label="{{ $coach->is_blocked ? __('Activate :coach', ['coach' => $coach->nama]) : __('Block :coach', ['coach' => $coach->nama]) }}"
                                                        class="{{ $coach->is_blocked ? 'bg-red-100 text-red-800 hover:bg-red-200 focus:ring-red-700' : 'bg-green-100 text-green-800 hover:bg-green-200 focus:ring-green-700' }} inline-flex min-h-10 min-w-24 items-center justify-center rounded-full px-4 text-xs font-extrabold transition focus:outline-none focus:ring-2 focus:ring-offset-2"
                                                    >
                                                        {{ $coach->is_blocked ? __('Blocked') : __('Active') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-5 py-12 text-center text-sm text-gray-500">{{ __('No coaches found for this team.') }}</td>
                                        </tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @if ($section === 'players' && $players->hasPages())
                        <div class="mt-4">{{ $players->links() }}</div>
                    @elseif ($section === 'coaches' && $coaches->hasPages())
                        <div class="mt-4">{{ $coaches->links() }}</div>
                    @endif

                    <div class="mt-7 flex justify-end border-t border-gray-100 pt-5">
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'player-suspension-team')"
                            class="rounded-lg bg-blue-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
                        >
                            {{ __('Close') }}
                        </button>
                    </div>
                </div>
            </x-modal>
        @endif
    </div>
</x-app-layout>
