<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
        <a href="{{ url('/') }}" class="text-sm font-semibold text-blue-800 hover:text-blue-950">
            &larr; {{ __('Back to Dashboard') }}
        </a>

        <section class="mt-5 flex flex-col items-center gap-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:flex-row sm:p-8">
            <div class="flex h-36 w-36 shrink-0 items-center justify-center rounded-xl bg-gray-50 p-4 sm:h-40 sm:w-40">
                <img
                    src="{{ $team->logo ? (str_starts_with($team->logo, 'http') || str_starts_with($team->logo, 'images/') ? asset($team->logo) : asset('storage/'.$team->logo)) : asset('images/team-placeholder.svg') }}"
                    alt="{{ __(':team logo', ['team' => $team->nama_pasukan]) }}"
                    class="h-full w-full object-contain"
                >
            </div>
            <div class="min-w-0 flex-1 text-center sm:text-left">
                <p class="text-sm font-bold uppercase tracking-widest text-blue-800">{{ __('Tournament Team') }}</p>
                <h1 class="mt-2 break-words text-2xl font-extrabold leading-tight text-gray-900 sm:text-3xl">{{ $team->nama_pasukan }}</h1>
                <p class="mt-3 text-gray-600">{{ $team->nama_kolej }}</p>
            </div>
        </section>

        <section x-data="{ activeTab: 'players' }" class="mt-8">
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
                        <table class="w-full min-w-[720px] divide-y divide-gray-200 text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="w-16 px-4 py-3 text-xs font-bold uppercase tracking-wide text-gray-600">#</th>
                                    <th scope="col" class="px-4 py-3 text-xs font-bold uppercase tracking-wide text-gray-600">{{ __('Name') }}</th>
                                    <th scope="col" class="px-4 py-3 text-xs font-bold uppercase tracking-wide text-gray-600">{{ __('Jersey Num') }}</th>
                                    <th scope="col" class="px-4 py-3 text-xs font-bold uppercase tracking-wide text-gray-600">{{ __('Age') }}</th>
                                    <th scope="col" class="px-4 py-3 text-xs font-bold uppercase tracking-wide text-gray-600">{{ __('Status') }}</th>
                                    <th scope="col" class="px-4 py-3 text-xs font-bold uppercase tracking-wide text-gray-600">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($players as $player)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 text-sm text-gray-600">{{ $players->firstItem() + $loop->index }}</td>
                                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">{{ $player->nama }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-700">{{ $player->no_jersi }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-700">{{ $player->umur ?? '—' }}</td>
                                        <td class="px-4 py-4">
                                            @if ($player->sekatan_count > 0)
                                                <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">{{ __('Blacklist') }}</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">{{ __('Active') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <button type="button" disabled class="cursor-not-allowed rounded-md bg-gray-100 px-3 py-1.5 text-sm font-semibold text-gray-500" aria-label="{{ __('View :player', ['player' => $player->nama]) }}">
                                                {{ __('View') }}
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-600">
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

            <div id="coaches-panel" role="tabpanel" aria-labelledby="coaches-tab" x-show="activeTab === 'coaches'" style="display: none" class="mt-5 rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
                <p class="text-sm text-gray-600">{{ __('Coach information will be available here.') }}</p>
            </div>
        </section>
    </div>
</x-app-layout>
