<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:py-12">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('technical-official.rosters.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-blue-100 bg-white/80 px-3 text-sm font-bold text-blue-900 shadow-sm transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">
                <span aria-hidden="true">&larr;</span> {{ __('All teams') }}
            </a>
            <a href="{{ route('technical-official.dashboard') }}" class="text-sm font-semibold text-blue-900 underline decoration-blue-200 underline-offset-4 hover:text-blue-700">{{ __('Technical Official Dashboard') }}</a>
        </div>

        @if (session('status'))
            <div role="status" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('status') }}</div>
        @endif

        <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-[#0b1236] via-[#172b67] to-[#3157a4] px-6 py-8 text-white shadow-xl sm:px-9 sm:py-10">
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-amber-300">{{ __('KICKHUB · TEAM ROSTER') }}</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">{{ $team->nama_pasukan }}</h1>
            <p class="mt-2 text-blue-100">{{ $team->nama_kolej }} · {{ __('Manage players and coaches for this team.') }}</p>
        </section>

        <section aria-labelledby="players-tools-heading">
            <div class="mb-5">
                <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-800">{{ __('Player roster') }}</p>
                <h2 id="players-tools-heading" class="mt-1 text-xl font-black text-[#111b48]">{{ __('Add players') }}</h2>
            </div>

            <div class="grid gap-5 xl:grid-cols-2">
                <form method="POST" action="{{ route('technical-official.rosters.players.store', $team) }}" class="space-y-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    @csrf
                    <h3 class="font-extrabold text-[#111b48]">{{ __('Add one player') }}</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="player-name" class="text-sm font-semibold text-gray-700">{{ __('Player name') }}</label>
                            <input id="player-name" name="player_name" value="{{ old('player_name') }}" required maxlength="255" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                            @error('player_name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="player-national-id" class="text-sm font-semibold text-gray-700">{{ __('National ID (12 digits)') }}</label>
                            <input id="player-national-id" name="player_national_id" value="{{ old('player_national_id') }}" required maxlength="12" inputmode="numeric" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                            @error('player_national_id')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="player-jersey" class="text-sm font-semibold text-gray-700">{{ __('Jersey number') }}</label>
                            <input id="player-jersey" name="player_jersey_number" type="number" min="1" max="65535" value="{{ old('player_jersey_number') }}" required class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                            @error('player_jersey_number')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="player-age" class="text-sm font-semibold text-gray-700">{{ __('Age') }}</label>
                            <input id="player-age" name="player_age" type="number" min="1" max="255" value="{{ old('player_age') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                            @error('player_age')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="player-role" class="text-sm font-semibold text-gray-700">{{ __('Role') }}</label>
                            <input id="player-role" name="player_role" value="{{ old('player_role', 'Player') }}" required maxlength="255" list="player-role-options" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                            <datalist id="player-role-options">
                                <option value="Player"></option>
                                <option value="Captain"></option>
                                <option value="Vice Captain"></option>
                                <option value="Demo Player"></option>
                            </datalist>
                            @error('player_role')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="player-position" class="text-sm font-semibold text-gray-700">{{ __('Position') }}</label>
                            <input id="player-position" name="player_position" value="{{ old('player_position') }}" required maxlength="255" placeholder="{{ __('e.g. Goalkeeper, Defender, Midfielder, Forward') }}" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                            @error('player_position')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">{{ __('After adding a player, use Edit to add achievements or update their role and position.') }}</p>
                    <button type="submit" class="rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">{{ __('Add player') }}</button>
                </form>

                <form method="POST" action="{{ route('technical-official.rosters.players.import', $team) }}" enctype="multipart/form-data" class="space-y-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    @csrf
                    <h3 class="font-extrabold text-[#111b48]">{{ __('Import players from CSV or Excel') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('Upload a CSV or .xlsx file with these columns: name, national_id, jersey_number, age, role, position. National IDs must contain exactly 12 digits; format this column as text in Excel to preserve leading zeroes.') }}</p>
                    <a href="{{ route('technical-official.rosters.player-template') }}" class="inline-flex text-sm font-semibold text-blue-900 underline decoration-blue-200 underline-offset-4 hover:text-blue-700">{{ __('Download player CSV template') }}</a>
                    <div>
                        <label for="players-file" class="text-sm font-semibold text-gray-700">{{ __('Player roster file') }}</label>
                        <input id="players-file" name="players_file" type="file" accept=".csv,.xlsx" required class="mt-1 block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-blue-900 hover:file:bg-blue-100">
                        @error('players_file')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                    @error('file')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-500">{{ __('Maximum 500 players per import. Duplicate or invalid rows reject the entire file.') }}</p>
                    <button type="submit" class="rounded-lg bg-blue-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">{{ __('Import players') }}</button>
                </form>
            </div>

            <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                    <h3 class="font-extrabold text-[#111b48]">{{ __('Players') }}</h3>
                    <span class="text-sm text-gray-500">{{ trans_choice(':count player|:count players', $players->total(), ['count' => $players->total()]) }}</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($players as $player)
                        <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4">
                            <div class="min-w-0">
                                <h4 class="break-words font-bold text-gray-900">{{ $player->nama }}</h4>
                                <p class="mt-1 text-sm text-gray-500">#{{ $player->no_jersi }} · {{ $player->position }} · {{ $player->role_pemain }} · {{ trans_choice(':count achievement|:count achievements', $player->pencapaian->count(), ['count' => $player->pencapaian->count()]) }}</p>
                            </div>
                            <a href="{{ route('technical-official.rosters.players.edit', [$team, $player]) }}" class="rounded-lg border border-blue-200 px-3 py-2 text-sm font-semibold text-blue-900 transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2">{{ __('Edit player') }}</a>
                        </div>
                    @empty
                        <p class="px-5 py-10 text-center text-sm text-gray-500">{{ __('No players have been registered for this team yet.') }}</p>
                    @endforelse
                </div>
                @if ($players->hasPages())
                    <div class="border-t border-gray-100 px-5 py-4">{{ $players->links() }}</div>
                @endif
            </div>
        </section>

        <section aria-labelledby="coaches-tools-heading">
            <div class="mb-5">
                <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-amber-800">{{ __('Coaching staff') }}</p>
                <h2 id="coaches-tools-heading" class="mt-1 text-xl font-black text-[#111b48]">{{ __('Add coaches') }}</h2>
            </div>

            <div class="grid gap-5 xl:grid-cols-2">
                <form method="POST" action="{{ route('technical-official.rosters.coaches.store', $team) }}" class="space-y-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    @csrf
                    <h3 class="font-extrabold text-[#111b48]">{{ __('Add one coach') }}</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="coach-name" class="text-sm font-semibold text-gray-700">{{ __('Coach name') }}</label>
                            <input id="coach-name" name="coach_name" value="{{ old('coach_name') }}" required maxlength="255" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                            @error('coach_name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="coach-age" class="text-sm font-semibold text-gray-700">{{ __('Age') }}</label>
                            <input id="coach-age" name="coach_age" type="number" min="1" max="255" value="{{ old('coach_age') }}" required class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-700 focus:ring-blue-700">
                            @error('coach_age')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <button type="submit" class="rounded-lg bg-amber-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-700 focus:ring-offset-2">{{ __('Add coach') }}</button>
                </form>

                <form method="POST" action="{{ route('technical-official.rosters.coaches.import', $team) }}" enctype="multipart/form-data" class="space-y-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    @csrf
                    <h3 class="font-extrabold text-[#111b48]">{{ __('Import coaches from CSV or Excel') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('Upload a CSV or .xlsx file with these columns: name, age.') }}</p>
                    <a href="{{ route('technical-official.rosters.coach-template') }}" class="inline-flex text-sm font-semibold text-blue-900 underline decoration-blue-200 underline-offset-4 hover:text-blue-700">{{ __('Download coach CSV template') }}</a>
                    <div>
                        <label for="coaches-file" class="text-sm font-semibold text-gray-700">{{ __('Coach roster file') }}</label>
                        <input id="coaches-file" name="coaches_file" type="file" accept=".csv,.xlsx" required class="mt-1 block w-full rounded-lg border border-gray-300 bg-white text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-amber-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-amber-900 hover:file:bg-amber-100">
                        @error('coaches_file')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                    @error('file')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-500">{{ __('Maximum 500 coaches per import. Duplicate or invalid rows reject the entire file.') }}</p>
                    <button type="submit" class="rounded-lg bg-amber-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-700 focus:ring-offset-2">{{ __('Import coaches') }}</button>
                </form>
            </div>

            <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                    <h3 class="font-extrabold text-[#111b48]">{{ __('Coaches') }}</h3>
                    <span class="text-sm text-gray-500">{{ trans_choice(':count coach|:count coaches', $coaches->total(), ['count' => $coaches->total()]) }}</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($coaches as $coach)
                        <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4">
                            <div class="min-w-0">
                                <h4 class="break-words font-bold text-gray-900">{{ $coach->nama }}</h4>
                                <p class="mt-1 text-sm text-gray-500">{{ $coach->umur }} {{ __('years old') }}</p>
                            </div>
                            <span @class([
                                'rounded-full px-3 py-1 text-xs font-semibold',
                                'bg-green-100 text-green-800' => ! $coach->is_blocked,
                                'bg-red-100 text-red-800' => $coach->is_blocked,
                            ])>{{ $coach->is_blocked ? __('Blocked') : __('Active') }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-10 text-center text-sm text-gray-500">{{ __('No coaches have been registered for this team yet.') }}</p>
                    @endforelse
                </div>
                @if ($coaches->hasPages())
                    <div class="border-t border-gray-100 px-5 py-4">{{ $coaches->links() }}</div>
                @endif
            </div>
        </section>
    </div>
</x-app-layout>
