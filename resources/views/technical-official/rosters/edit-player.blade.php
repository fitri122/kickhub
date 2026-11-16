<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-8 px-4 py-8 sm:px-6 lg:py-12">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('technical-official.rosters.teams.show', $team) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-blue-100 bg-white/80 px-3 text-sm font-bold text-blue-900 shadow-sm transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                <span aria-hidden="true">&larr;</span> {{ __('Back to :team roster', ['team' => $team->nama_pasukan]) }}
            </a>
            <a href="{{ route('technical-official.dashboard') }}" class="text-sm font-semibold text-blue-900 underline decoration-blue-200 underline-offset-4 hover:text-blue-700">{{ __('Technical Official Dashboard') }}</a>
        </div>

        @if (session('status'))
            <div role="status" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('status') }}</div>
        @endif

        <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-[#0b1236] via-[#172b67] to-[#3157a4] px-6 py-8 text-white shadow-xl sm:px-9 sm:py-10">
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-amber-300">{{ __('KICKHUB · PLAYER DETAILS') }}</p>
            <h1 class="mt-3 break-words text-3xl font-black tracking-tight sm:text-4xl">{{ __('Edit :player', ['player' => $player->nama]) }}</h1>
            <p class="mt-2 text-blue-100">{{ $team->nama_pasukan }}</p>
        </section>

        <form method="POST" action="{{ route('technical-official.rosters.players.update', [$team, $player]) }}" class="space-y-7 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7">
            @csrf
            @method('PUT')

            <section class="space-y-4">
                <h2 class="text-lg font-extrabold text-[#111b48]">{{ __('Player information') }}</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="player-name" class="text-sm font-semibold text-gray-700">{{ __('Player name') }}</label>
                        <input id="player-name" name="name" value="{{ old('name', $player->nama) }}" required maxlength="255" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                        @error('name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="player-national-id" class="text-sm font-semibold text-gray-700">{{ __('National ID (12 digits)') }}</label>
                        <input id="player-national-id" name="national_id" value="{{ old('national_id', $player->no_ic) }}" required maxlength="12" inputmode="numeric" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                        @error('national_id')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="player-jersey" class="text-sm font-semibold text-gray-700">{{ __('Jersey number') }}</label>
                        <input id="player-jersey" name="jersey_number" type="number" min="1" max="65535" value="{{ old('jersey_number', $player->no_jersi) }}" required class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                        @error('jersey_number')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="player-age" class="text-sm font-semibold text-gray-700">{{ __('Age') }}</label>
                        <input id="player-age" name="age" type="number" min="1" max="255" value="{{ old('age', $player->umur) }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                        @error('age')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="player-role" class="text-sm font-semibold text-gray-700">{{ __('Role') }}</label>
                        <input id="player-role" name="role" value="{{ old('role', $player->role_pemain) }}" required maxlength="255" list="player-role-options" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                        <datalist id="player-role-options">
                            <option value="Player"></option>
                            <option value="Captain"></option>
                            <option value="Vice Captain"></option>
                            <option value="Demo Player"></option>
                        </datalist>
                        @error('role')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="player-position" class="text-sm font-semibold text-gray-700">{{ __('Position') }}</label>
                        <input id="player-position" name="position" value="{{ old('position', $player->position) }}" required maxlength="255" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                        @error('position')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <section class="space-y-4 border-t border-gray-100 pt-6">
                <div>
                    <h2 class="text-lg font-extrabold text-[#111b48]">{{ __('Achievements') }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Fill all three fields for each achievement. Leave a row empty to remove it.') }}</p>
                </div>

                @foreach (old('achievements', $achievementRows) as $index => $achievement)
                    <div class="grid gap-3 rounded-xl border border-gray-200 p-4 sm:grid-cols-3">
                        <div>
                            <label for="achievement-name-{{ $index }}" class="text-sm font-semibold text-gray-700">{{ __('Achievement') }}</label>
                            <input id="achievement-name-{{ $index }}" name="achievements[{{ $index }}][name]" value="{{ old("achievements.{$index}.name", $achievement['name'] ?? '') }}" maxlength="255" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                            @error("achievements.{$index}.name")<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="achievement-year-{{ $index }}" class="text-sm font-semibold text-gray-700">{{ __('Year') }}</label>
                            <input id="achievement-year-{{ $index }}" name="achievements[{{ $index }}][year]" type="number" min="1901" max="2155" value="{{ old("achievements.{$index}.year", $achievement['year'] ?? '') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                            @error("achievements.{$index}.year")<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="achievement-level-{{ $index }}" class="text-sm font-semibold text-gray-700">{{ __('Level / placing') }}</label>
                            <input id="achievement-level-{{ $index }}" name="achievements[{{ $index }}][level]" value="{{ old("achievements.{$index}.level", $achievement['level'] ?? '') }}" maxlength="255" placeholder="{{ __('e.g. District, 1st place') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                            @error("achievements.{$index}.level")<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                    </div>
                @endforeach

                @error('achievements')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
            </section>

            <div class="flex flex-wrap justify-end gap-3 border-t border-gray-100 pt-5">
                <a href="{{ route('technical-official.rosters.teams.show', $team) }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">{{ __('Cancel') }}</a>
                <button type="submit" class="rounded-lg bg-blue-900 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">{{ __('Save changes') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
