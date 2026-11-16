<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:py-12">
        <a href="{{ route('technical-official.dashboard') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-blue-100 bg-white/80 px-3 text-sm font-bold text-blue-900 shadow-sm transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
            <span aria-hidden="true">&larr;</span> {{ __('Technical Official Dashboard') }}
        </a>

        <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-[#0b1236] via-[#172b67] to-[#3157a4] px-6 py-8 text-white shadow-xl sm:px-9 sm:py-10">
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-amber-300">{{ __('KICKHUB · TEAM MANAGEMENT') }}</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">{{ __('Team Rosters') }}</h1>
            <p class="mt-2 text-blue-100">{{ __('Choose a team to add or update its players and coaches.') }}</p>
        </section>

        <section aria-labelledby="team-roster-list">
            <div class="mb-5">
                <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-800">{{ __('Technical official tools') }}</p>
                <h2 id="team-roster-list" class="mt-1 text-xl font-black text-[#111b48]">{{ __('Select a team') }}</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($teams as $team)
                    <a href="{{ route('technical-official.rosters.teams.show', $team) }}" class="group rounded-2xl border border-white bg-white/90 p-5 shadow-lg shadow-blue-950/5 transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-extrabold text-[#111b48]">{{ $team->nama_pasukan }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ $team->nama_kolej }}</p>
                            </div>
                            <span class="text-lg text-blue-800 transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                        </div>
                        <div class="mt-5 flex flex-wrap gap-2 text-xs font-semibold">
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-blue-800">{{ trans_choice(':count player|:count players', $team->pemain_count, ['count' => $team->pemain_count]) }}</span>
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-800">{{ trans_choice(':count coach|:count coaches', $team->jurulatih_count, ['count' => $team->jurulatih_count]) }}</span>
                        </div>
                    </a>
                @empty
                    <p class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white px-5 py-12 text-center text-sm text-gray-500">
                        {{ __('No teams have been registered yet.') }}
                    </p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
