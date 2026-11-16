<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-8 px-4 py-8 sm:px-6 lg:py-12">
        <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-[#0b1236] via-[#172b67] to-[#3157a4] px-6 py-8 text-white shadow-xl sm:px-9 sm:py-10">
            <div class="pointer-events-none absolute -right-12 -top-20 -z-10 h-72 w-72 rounded-full bg-cyan-300/20 blur-3xl" aria-hidden="true"></div>
            <div class="relative">
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-amber-300">{{ __('KICKHUB · MATCHDAY WORKSPACE') }}</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">{{ $match ? __('Edit Match') : __('Create Match') }}</h1>
                <p class="mt-2 text-base text-blue-100">{{ __('Choose a group first. Both teams must be from the same group.') }}</p>
            </div>
        </section>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('technical-official.matches.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-blue-100 bg-white/80 px-3 text-sm font-bold text-blue-900 shadow-sm transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                <span aria-hidden="true">&larr;</span> {{ __('Back to Matches') }}
            </a>
        </div>

        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-bold">{{ __('Please correct the following before saving:') }}</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div role="status" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <form
            method="POST"
            action="{{ $match ? route('technical-official.matches.update', $match) : route('technical-official.matches.store') }}"
            x-data="{
                teamsByGroup: @js($teamsByGroup),
                playersByTeam: @js($playersByTeam),
                groupId: @js($selectedGroupId),
                homeTeamId: @js($selectedHomeTeamId),
                awayTeamId: @js($selectedAwayTeamId),
                isCompleted: @js($completed),
                goalScorers: @js($goalScorers),
                cards: @js($cards),
                get matchTeams() {
                    const teams = this.teamsByGroup[this.groupId] || [];

                    return [this.homeTeamId, this.awayTeamId]
                        .map(teamId => teams.find(team => String(team.id) === String(teamId)))
                        .filter(team => team !== undefined)
                        .map(team => ({
                            id: String(team.id),
                            name: team.name,
                            players: this.playersByTeam[team.id] || [],
                        }));
                },
                resetGroup() {
                    this.homeTeamId = '';
                    this.awayTeamId = '';
                    this.goalScorers = [];
                    this.cards = [];
                },
                resetEvents() {
                    this.goalScorers = [];
                    this.cards = [];
                },
            }"
            class="space-y-6"
        >
            @csrf
            @if ($match)
                @method('PUT')
            @endif

            <section class="rounded-2xl border border-white bg-white/95 p-5 shadow-lg shadow-blue-950/5 sm:p-7" aria-labelledby="fixture-details-heading">
                <div class="mb-5">
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-800">{{ __('Fixture Setup') }}</p>
                    <h2 id="fixture-details-heading" class="mt-1 text-xl font-black text-[#111b48]">{{ __('Match Details') }}</h2>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="id_kumpulan" class="mb-2 block text-sm font-bold text-gray-800">{{ __('Group') }}</label>
                        <select id="id_kumpulan" name="id_kumpulan" x-model="groupId" @change="resetGroup()" required class="block min-h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700">
                            <option value="">{{ __('Choose a group first') }}</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->nama_kumpulan }}</option>
                            @endforeach
                        </select>
                        @error('id_kumpulan') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="id_pasukan_1" class="mb-2 block text-sm font-bold text-gray-800">{{ __('Team 1') }}</label>
                        <select id="id_pasukan_1" name="id_pasukan_1" x-model="homeTeamId" @change="resetEvents()" x-bind:disabled="!groupId" required class="block min-h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700 disabled:bg-gray-100">
                            <option value="">{{ __('Choose team 1') }}</option>
                            <template x-for="team in teamsByGroup[groupId] || []" :key="team.id">
                                <option x-bind:value="String(team.id)" x-bind:disabled="String(team.id) === String(awayTeamId)" x-text="team.name"></option>
                            </template>
                        </select>
                        @error('id_pasukan_1') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="id_pasukan_2" class="mb-2 block text-sm font-bold text-gray-800">{{ __('Team 2') }}</label>
                        <select id="id_pasukan_2" name="id_pasukan_2" x-model="awayTeamId" @change="resetEvents()" x-bind:disabled="!groupId" required class="block min-h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700 disabled:bg-gray-100">
                            <option value="">{{ __('Choose team 2') }}</option>
                            <template x-for="team in teamsByGroup[groupId] || []" :key="team.id">
                                <option x-bind:value="String(team.id)" x-bind:disabled="String(team.id) === String(homeTeamId)" x-text="team.name"></option>
                            </template>
                        </select>
                        @error('id_pasukan_2') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="match_label" class="mb-2 block text-sm font-bold text-gray-800">{{ __('Match Name / Stage (Optional)') }}</label>
                        <input id="match_label" name="match_label" type="text" value="{{ $matchLabelValue }}" maxlength="255" placeholder="{{ __('e.g. Semi Final or Final') }}" aria-describedby="match-label-help" class="block min-h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700">
                        <p id="match-label-help" class="mt-1 text-sm text-gray-500">{{ __('Leave blank to keep the default Match #number label.') }}</p>
                        @error('match_label') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tarikh" class="mb-2 block text-sm font-bold text-gray-800">{{ __('Date') }}</label>
                        <input id="tarikh" name="tarikh" type="date" value="{{ $dateValue }}" required class="block min-h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700">
                        @error('tarikh') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="masa" class="mb-2 block text-sm font-bold text-gray-800">{{ __('Time') }}</label>
                        <input id="masa" name="masa" type="time" value="{{ $timeValue }}" required class="block min-h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700">
                        @error('masa') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="tempat" class="mb-2 block text-sm font-bold text-gray-800">{{ __('Venue') }}</label>
                        <input id="tempat" name="tempat" type="text" value="{{ $venueValue }}" maxlength="255" required class="block min-h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700">
                        @error('tempat') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-white bg-white/95 p-5 shadow-lg shadow-blue-950/5 sm:p-7" aria-labelledby="match-result-heading">
                <div class="mb-5 flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-800">{{ __('After the Match') }}</p>
                        <h2 id="match-result-heading" class="mt-1 text-xl font-black text-[#111b48]">{{ __('Result and Events') }}</h2>
                    </div>
                    <label class="inline-flex cursor-pointer items-center gap-3 rounded-xl bg-blue-50 px-4 py-3 text-sm font-bold text-blue-950">
                        <input type="checkbox" name="is_completed" value="1" x-model="isCompleted" @change="if (!isCompleted) resetEvents()" class="rounded border-gray-300 text-blue-900 shadow-sm focus:ring-blue-700">
                        {{ __('Match completed') }}
                    </label>
                </div>
                <p class="mb-5 text-sm text-gray-600">{{ __('The score is calculated from the goal scorers you enter. Marking a match as upcoming clears its recorded goals and cards.') }}</p>

                <div x-show="isCompleted" class="space-y-6">
                    <div class="rounded-xl border border-blue-100 p-4 sm:p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h3 class="font-extrabold text-[#111b48]">{{ __('Goal Scorers') }}</h3>
                                <p class="mt-1 text-xs text-gray-500">{{ __('Add one row for every goal. A player can appear more than once.') }}</p>
                            </div>
                            <button type="button" @click="goalScorers.push('')" class="inline-flex min-h-10 items-center rounded-lg border border-blue-200 bg-blue-50 px-4 text-sm font-bold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                                {{ __('Add Goal') }}
                            </button>
                        </div>
                        <div class="mt-4 space-y-3">
                            <template x-for="(playerId, index) in goalScorers" :key="index">
                                <div class="flex items-center gap-3">
                                    <select x-bind:name="`goal_scorers[${index}]`" x-model="goalScorers[index]" required class="block min-h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700">
                                        <option value="">{{ __('Choose a scorer') }}</option>
                                        <template x-for="team in matchTeams" :key="team.id">
                                            <optgroup x-bind:label="team.name">
                                                <template x-for="player in team.players" :key="player.id">
                                                    <option x-bind:value="String(player.id)" x-text="player.name + ' (#' + player.jersey + ')'"></option>
                                                </template>
                                            </optgroup>
                                        </template>
                                    </select>
                                    <button type="button" @click="goalScorers.splice(index, 1)" aria-label="{{ __('Remove goal') }}" class="grid h-10 w-10 shrink-0 place-items-center rounded-lg text-lg font-bold text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-700">
                                        &times;
                                    </button>
                                </div>
                            </template>
                            <p x-show="goalScorers.length === 0" class="rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-500">{{ __('No goals recorded.') }}</p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-blue-100 p-4 sm:p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h3 class="font-extrabold text-[#111b48]">{{ __('Cards') }}</h3>
                                <p class="mt-1 text-xs text-gray-500">{{ __('Record each yellow or red card and the minute it was given.') }}</p>
                            </div>
                            <button type="button" @click="cards.push({ player_id: '', jenis_kad: 'kuning', minit: '', catatan: '' })" class="inline-flex min-h-10 items-center rounded-lg border border-blue-200 bg-blue-50 px-4 text-sm font-bold text-blue-900 transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                                {{ __('Add Card') }}
                            </button>
                        </div>
                        <div class="mt-4 space-y-4">
                            <template x-for="(card, index) in cards" :key="index">
                                <div class="grid gap-3 rounded-xl bg-gray-50 p-4 sm:grid-cols-2 xl:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,1fr)_auto]">
                                    <select x-bind:name="`cards[${index}][player_id]`" x-model="card.player_id" required class="block min-h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700">
                                        <option value="">{{ __('Choose a player') }}</option>
                                        <template x-for="team in matchTeams" :key="team.id">
                                            <optgroup x-bind:label="team.name">
                                                <template x-for="player in team.players" :key="player.id">
                                                    <option x-bind:value="String(player.id)" x-text="player.name + ' (#' + player.jersey + ')'"></option>
                                                </template>
                                            </optgroup>
                                        </template>
                                    </select>
                                    <select x-bind:name="`cards[${index}][jenis_kad]`" x-model="card.jenis_kad" required class="block min-h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700">
                                        <option value="kuning">{{ __('Yellow Card') }}</option>
                                        <option value="merah">{{ __('Red Card') }}</option>
                                    </select>
                                    <input x-bind:name="`cards[${index}][minit]`" x-model="card.minit" type="number" min="0" max="255" required aria-label="{{ __('Card minute') }}" placeholder="{{ __('Minute') }}" class="block min-h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700">
                                    <button type="button" @click="cards.splice(index, 1)" aria-label="{{ __('Remove card') }}" class="grid h-10 w-10 place-items-center rounded-lg text-lg font-bold text-red-700 transition hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-700">
                                        &times;
                                    </button>
                                    <textarea x-bind:name="`cards[${index}][catatan]`" x-model="card.catatan" rows="2" maxlength="1000" aria-label="{{ __('Card notes') }}" placeholder="{{ __('Optional notes') }}" class="block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700 sm:col-span-2 xl:col-span-4"></textarea>
                                </div>
                            </template>
                            <p x-show="cards.length === 0" class="rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-500">{{ __('No cards recorded.') }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="flex flex-wrap justify-end gap-3">
                <a href="{{ route('technical-official.matches.index') }}" class="inline-flex min-h-11 items-center rounded-xl border border-gray-200 bg-white px-5 text-sm font-bold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="inline-flex min-h-11 items-center rounded-xl bg-blue-900 px-6 text-sm font-extrabold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                    {{ $match ? __('Save Changes') : __('Create Match') }}
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
