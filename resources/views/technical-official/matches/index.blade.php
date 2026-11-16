<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:py-12">
        <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-[#0b1236] via-[#172b67] to-[#3157a4] px-6 py-8 text-white shadow-xl sm:px-9 sm:py-10">
            <div class="pointer-events-none absolute -right-12 -top-20 -z-10 h-72 w-72 rounded-full bg-cyan-300/20 blur-3xl" aria-hidden="true"></div>
            <div class="relative flex flex-wrap items-end justify-between gap-5">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-amber-300">{{ __('KICKHUB · MATCHDAY WORKSPACE') }}</p>
                    <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">{{ __('Match Management') }}</h1>
                    <p class="mt-2 text-base text-blue-100">{{ __('Create fixtures and record match results.') }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if ($matches->total() > 0)
                        <form
                            method="POST"
                            action="{{ route('technical-official.matches.destroy-all') }}"
                            data-confirm-message="{{ __('Delete all matches and their recorded goals and cards? Teams, players and groups will stay. This cannot be undone.') }}"
                            onsubmit="return confirm(this.dataset.confirmMessage)"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex min-h-11 items-center rounded-xl border border-red-200 bg-red-50 px-5 text-sm font-extrabold text-red-800 transition hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-700 focus:ring-offset-2">
                                {{ __('Delete All Matches') }}
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('technical-official.matches.create') }}" class="inline-flex min-h-11 items-center rounded-xl bg-amber-400 px-5 text-sm font-extrabold text-[#111b48] transition hover:bg-amber-300 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-[#172b67]">
                        {{ __('Create Match') }}
                    </a>
                </div>
            </div>
        </section>

        @if (session('status'))
            <div role="status" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <section aria-labelledby="matches-heading">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-800">{{ __('Technical Official') }}</p>
                    <h2 id="matches-heading" class="mt-1 text-xl font-black text-[#111b48]">{{ __('Scheduled Matches') }}</h2>
                </div>
                <a href="{{ route('schedule') }}" class="text-sm font-bold text-blue-800 underline decoration-blue-200 underline-offset-4 hover:text-blue-950">
                    {{ __('View Public Schedule') }}
                </a>
            </div>

            <div class="overflow-hidden rounded-2xl border border-white bg-white/95 shadow-lg shadow-blue-950/5">
                @if ($matches->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left text-sm">
                            <thead class="bg-blue-50 text-xs font-extrabold uppercase tracking-wider text-blue-950">
                                <tr>
                                    <th scope="col" class="px-5 py-4">{{ __('Group') }}</th>
                                    <th scope="col" class="px-5 py-4">{{ __('Match') }}</th>
                                    <th scope="col" class="px-5 py-4">{{ __('Date and Time') }}</th>
                                    <th scope="col" class="px-5 py-4">{{ __('Venue') }}</th>
                                    <th scope="col" class="px-5 py-4">{{ __('Result') }}</th>
                                    <th scope="col" class="px-5 py-4 text-right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($matches as $match)
                                    <tr>
                                        <td class="px-5 py-4 font-semibold text-gray-700">{{ $match->kumpulan?->nama_kumpulan ?? __('Group not assigned') }}</td>
                                        <th scope="row" class="px-5 py-4 font-bold text-gray-900">
                                            <span class="mb-1 block text-xs font-extrabold uppercase tracking-wide text-blue-800">{{ $match->displayLabel($matches->firstItem() + $loop->index) }}</span>
                                            {{ $match->pasukan1->nama_pasukan }} <span class="text-gray-400">vs</span> {{ $match->pasukan2->nama_pasukan }}
                                        </th>
                                        <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                            {{ $match->tarikh->format('D, d M Y') }}<span class="mx-1 text-gray-300">·</span>{{ substr($match->masa, 0, 5) }}
                                        </td>
                                        <td class="px-5 py-4 text-gray-600">{{ $match->tempat }}</td>
                                        <td class="px-5 py-4 font-bold text-[#111b48]">
                                            @if ($match->skor_pasukan_1 !== null && $match->skor_pasukan_2 !== null)
                                                {{ $match->skor_pasukan_1 }} - {{ $match->skor_pasukan_2 }}
                                            @else
                                                {{ __('Upcoming') }}
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <div class="inline-flex items-center gap-2">
                                                <a href="{{ route('technical-official.matches.edit', $match) }}" class="inline-flex min-h-10 items-center rounded-lg bg-blue-900 px-4 text-sm font-bold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                                                    {{ __('Edit') }}
                                                </a>
                                                <form
                                                    method="POST"
                                                    action="{{ route('technical-official.matches.destroy', $match) }}"
                                                    data-confirm-message="{{ __('Delete :team1 vs :team2 and its recorded goals and cards? This cannot be undone.', ['team1' => $match->pasukan1->nama_pasukan, 'team2' => $match->pasukan2->nama_pasukan]) }}"
                                                    onsubmit="return confirm(this.dataset.confirmMessage)"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        aria-label="{{ __('Delete match between :team1 and :team2', ['team1' => $match->pasukan1->nama_pasukan, 'team2' => $match->pasukan2->nama_pasukan]) }}"
                                                        class="inline-flex min-h-10 items-center rounded-lg bg-red-100 px-4 text-sm font-bold text-red-800 transition hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-700 focus:ring-offset-2"
                                                    >
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($matches->hasPages())
                        <div class="border-t border-gray-100 px-5 py-4">{{ $matches->links() }}</div>
                    @endif
                @else
                    <div class="px-6 py-14 text-center">
                        <p class="text-lg font-bold text-gray-900">{{ __('No matches have been created yet.') }}</p>
                        <p class="mt-2 text-sm text-gray-500">{{ __('Create a match by choosing its group and two teams from that group.') }}</p>
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-app-layout>
