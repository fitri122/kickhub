<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-[#0b1236] via-[#172b67] to-[#3157a4] px-6 py-7 text-white shadow-xl sm:px-8 sm:py-9" aria-labelledby="league-heading">
                <div class="pointer-events-none absolute -right-12 -top-20 -z-10 h-64 w-64 rounded-full bg-amber-300/15 blur-3xl" aria-hidden="true"></div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-amber-300">{{ __('KICKHUB · LEAGUE CENTRE') }}</p>
                <h1 id="league-heading" class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">
                    {{ $activePage === 'overall' ? __('Overall League Standings') : __('League Standings') }}
                </h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-blue-100 sm:text-base">{{ __('Follow every team’s progress, points and goal difference throughout the tournament.') }}</p>
                @auth
                    @if (Auth::user()->role === \App\Models\User::ROLE_TECHNICAL_OFFICIAL && $activePage === 'overall')
                        <a href="{{ route('league-standings') }}#team-group-assignments" class="mt-4 inline-flex min-h-10 items-center rounded-xl bg-amber-400 px-4 text-sm font-extrabold text-[#111b48] transition hover:bg-amber-300 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#172b67]">
                            {{ __('Manage Team Groups') }}
                        </a>
                    @endif
                @endauth
            </section>

            <nav class="flex flex-wrap gap-3" aria-label="{{ __('League table views') }}">
                <a
                    href="{{ route('league-table') }}"
                    aria-current="{{ $activePage === 'overall' ? 'page' : 'false' }}"
                    class="inline-flex items-center rounded-lg px-5 py-3 text-sm font-semibold transition {{ $activePage === 'overall' ? 'bg-blue-900 text-white shadow-sm' : 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }}"
                >
                    {{ __('Overall League Standings') }}
                </a>
                <a
                    href="{{ route('league-standings') }}"
                    aria-current="{{ $activePage === 'groups' ? 'page' : 'false' }}"
                    class="inline-flex items-center rounded-lg px-5 py-3 text-sm font-semibold transition {{ $activePage === 'groups' ? 'bg-blue-900 text-white shadow-sm' : 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }}"
                >
                    {{ __('League Standings') }}
                </a>
            </nav>

            @if ($activePage === 'groups')
                @auth
                    @if (Auth::user()->role === \App\Models\User::ROLE_TECHNICAL_OFFICIAL)
                        @if ($errors->any())
                            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        @if (session('status'))
                            <div role="status" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                                {{ session('status') }}
                            </div>
                        @endif

                        <section id="team-group-assignments" class="space-y-4" aria-labelledby="team-group-assignments-heading">
                            <div class="border-b border-blue-100 bg-gradient-to-r from-blue-50 to-white px-5 py-4 sm:px-6">
                                <h2 id="team-group-assignments-heading" class="text-lg font-extrabold text-[#111b48]">{{ __('Manage Team Groups') }}</h2>
                                <p class="mt-1 text-sm text-gray-600">{{ __('Select up to four teams for each group. Saving moves selected teams into that group.') }}</p>
                            </div>
                            <div class="grid gap-5 lg:grid-cols-2">
                                @foreach ($groups as $group)
                                    <section id="group-{{ $group->id }}-team-selection" class="overflow-hidden rounded-2xl border border-white bg-white/95 shadow-lg shadow-blue-950/5" aria-labelledby="group-{{ $group->id }}-selection-heading">
                                        <form
                                            method="POST"
                                            action="{{ route('technical-official.league-standings.groups.teams.update', $group) }}"
                                            x-data="{ teamIds: @js($assignedTeamIdsByGroup[$group->id] ?? []) }"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-blue-100 bg-gradient-to-r from-blue-50 to-white px-5 py-4">
                                                <h3 id="group-{{ $group->id }}-selection-heading" class="text-lg font-extrabold text-[#111b48]">{{ str_replace('Kumpulan ', 'Group ', $group->nama_kumpulan) }}</h3>
                                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-extrabold text-blue-900">
                                                    <span x-text="teamIds.length">{{ count($assignedTeamIdsByGroup[$group->id] ?? []) }}</span> / 4 {{ __('selected') }}
                                                </span>
                                            </div>
                                            <div class="overflow-x-auto">
                                                <table class="w-full min-w-[320px] divide-y divide-gray-100 text-left text-sm">
                                                    <thead class="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-600">
                                                        <tr>
                                                            <th scope="col" class="px-5 py-3">{{ __('Team') }}</th>
                                                            <th scope="col" class="px-5 py-3 text-center">{{ __('Select') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-100">
                                                        @forelse ($teams as $team)
                                                            <tr class="hover:bg-blue-50/50">
                                                                <th scope="row" class="px-5 py-3 font-semibold text-gray-900">{{ $team->nama_pasukan }}</th>
                                                                <td class="px-5 py-3 text-center">
                                                                    <input
                                                                        type="checkbox"
                                                                        name="team_ids[]"
                                                                        value="{{ $team->id }}"
                                                                        x-model="teamIds"
                                                                        x-bind:disabled="teamIds.length >= 4 && !teamIds.includes('{{ $team->id }}')"
                                                                        @checked($team->id_kumpulan === $group->id)
                                                                        aria-label="{{ __('Select :team for :group', ['team' => $team->nama_pasukan, 'group' => $group->nama_kumpulan]) }}"
                                                                        class="rounded border-gray-300 text-blue-900 shadow-sm focus:ring-blue-700 disabled:cursor-not-allowed disabled:opacity-40"
                                                                    >
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="2" class="px-5 py-8 text-center text-sm text-gray-500">{{ __('No teams are available to assign.') }}</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 px-5 py-4">
                                                <p class="text-xs text-gray-500">{{ __('Unselected teams in this group will become unassigned.') }}</p>
                                                <button type="submit" class="inline-flex min-h-10 items-center rounded-lg bg-blue-900 px-4 text-sm font-bold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                                                    {{ __('Save :group', ['group' => str_replace('Kumpulan ', '', $group->nama_kumpulan)]) }}
                                                </button>
                                            </div>
                                        </form>
                                    </section>
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endauth
            @endif

            @foreach ($tables as $table)
                <section id="{{ $table['id'] }}" class="overflow-hidden rounded-2xl border border-white bg-white/95 shadow-lg shadow-blue-950/5">
                    <div class="border-b border-blue-100 bg-gradient-to-r from-blue-50 to-white px-5 py-4 sm:px-6">
                        <h3 class="text-lg font-extrabold text-[#111b48]">{{ $table['title'] }}</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full table-fixed divide-y divide-gray-200 text-left {{ $hasGroupColumn ? 'min-w-[1080px]' : 'min-w-[980px]' }}">
                            <caption class="sr-only">{{ $table['title'] }}</caption>
                            <colgroup>
                                <col class="w-16">
                                <col>
                                @if ($hasGroupColumn)
                                    <col class="w-28">
                                @endif
                                <col class="w-14">
                                <col class="w-14">
                                <col class="w-14">
                                <col class="w-14">
                                <col class="w-16">
                                <col class="w-16">
                                <col class="w-16">
                                <col class="w-16">
                                <col class="w-16">
                                <col class="w-20">
                            </colgroup>
                            <thead class="bg-[#111b48]">
                                <tr>
                                    <th scope="col" class="px-3 py-4 text-center text-xs font-bold uppercase tracking-wider text-blue-100">#</th>
                                    <th scope="col" class="px-4 py-4 text-xs font-bold uppercase tracking-wider text-blue-100">{{ __('Team') }}</th>
                                    @if ($hasGroupColumn)
                                        <th scope="col" class="whitespace-nowrap px-3 py-4 text-center text-xs font-bold uppercase tracking-wider text-blue-100">GP (Group)</th>
                                    @endif
                                    <th scope="col" class="px-2 py-4 text-center text-xs font-bold uppercase tracking-wider text-blue-100">P</th>
                                    <th scope="col" class="px-2 py-4 text-center text-xs font-bold uppercase tracking-wider text-blue-100">W</th>
                                    <th scope="col" class="px-2 py-4 text-center text-xs font-bold uppercase tracking-wider text-blue-100">D</th>
                                    <th scope="col" class="px-2 py-4 text-center text-xs font-bold uppercase tracking-wider text-blue-100">L</th>
                                    <th scope="col" class="px-2 py-4 text-center text-xs font-bold uppercase tracking-wider text-blue-100">GF</th>
                                    <th scope="col" class="px-2 py-4 text-center text-xs font-bold uppercase tracking-wider text-blue-100">GA</th>
                                    <th scope="col" class="px-2 py-4 text-center text-xs font-bold uppercase tracking-wider text-blue-100">GD</th>
                                    <th scope="col" class="px-2 py-4 text-center text-xs font-bold uppercase tracking-wider text-blue-100">YC</th>
                                    <th scope="col" class="px-2 py-4 text-center text-xs font-bold uppercase tracking-wider text-blue-100">RC</th>
                                    <th scope="col" class="px-3 py-4 text-center text-xs font-bold uppercase tracking-wider text-blue-100">PTS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($table['standings'] as $standing)
                                    <tr class="h-16 transition-colors odd:bg-white even:bg-blue-50/40 hover:bg-amber-50">
                                        <td class="px-3 py-4 text-center align-middle text-sm font-semibold text-gray-500">{{ $standing['rank'] }}</td>
                                        <td class="px-4 py-4 align-middle text-sm font-semibold text-gray-900">{{ $standing['team_name'] }}</td>
                                        @if ($hasGroupColumn)
                                            <td class="px-3 py-4 text-center align-middle">
                                                <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800">
                                                    {{ $standing['group_label'] }}
                                                </span>
                                            </td>
                                        @endif
                                        <td class="px-2 py-4 text-center align-middle text-sm text-gray-700">{{ $standing['played'] }}</td>
                                        <td class="px-2 py-4 text-center align-middle text-sm text-gray-700">{{ $standing['wins'] }}</td>
                                        <td class="px-2 py-4 text-center align-middle text-sm text-gray-700">{{ $standing['draws'] }}</td>
                                        <td class="px-2 py-4 text-center align-middle text-sm text-gray-700">{{ $standing['losses'] }}</td>
                                        <td class="px-2 py-4 text-center align-middle text-sm text-gray-700">{{ $standing['goals_for'] }}</td>
                                        <td class="px-2 py-4 text-center align-middle text-sm text-gray-700">{{ $standing['goals_against'] }}</td>
                                        <td class="px-2 py-4 text-center align-middle text-sm text-gray-700">{{ $standing['goal_difference'] }}</td>
                                        <td class="px-2 py-4 text-center align-middle text-sm text-gray-700">{{ $standing['yellow_cards'] }}</td>
                                        <td class="px-2 py-4 text-center align-middle text-sm text-gray-700">{{ $standing['red_cards'] }}</td>
                                        <td class="px-3 py-4 text-center align-middle text-sm font-extrabold text-[#111b48]">{{ $standing['points'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $hasGroupColumn ? 13 : 12 }}" class="px-5 py-12 text-center text-sm text-gray-500">
                                            {{ __('No teams are registered in this group yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endforeach

            <section class="rounded-2xl border border-white bg-white/95 p-5 shadow-lg shadow-blue-950/5 sm:p-6">
                <h3 class="text-lg font-extrabold text-[#111b48]">{{ __('Notes') }}</h3>
                <dl class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    @if ($hasGroupColumn)
                        <div class="flex items-center gap-3 rounded-lg bg-gray-50 px-3 py-2">
                            <dt class="w-10 shrink-0 text-xs font-bold text-blue-900">GP</dt>
                            <dd class="text-sm text-gray-600">{{ __('Group') }}</dd>
                        </div>
                    @endif
                    @foreach ([
                        'P' => 'Played',
                        'W' => 'Win',
                        'D' => 'Draw',
                        'L' => 'Loss',
                        'GF' => 'Goals For',
                        'GA' => 'Goals Against',
                        'GD' => 'Goal Difference',
                        'YC' => 'Yellow Card',
                        'RC' => 'Red Card',
                        'PTS' => 'Points',
                    ] as $abbreviation => $description)
                        <div class="flex items-center gap-3 rounded-lg bg-gray-50 px-3 py-2">
                            <dt class="w-10 shrink-0 text-xs font-bold text-blue-900">{{ $abbreviation }}</dt>
                            <dd class="text-sm text-gray-600">{{ __($description) }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        </div>
    </div>
</x-app-layout>
