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
