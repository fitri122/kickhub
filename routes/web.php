<?php

use App\Http\Controllers\LeagueTableController;
use App\Http\Controllers\MatchScheduleController;
use App\Http\Controllers\PlayerSuspensionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TechnicalOfficialMatchController;
use App\Http\Controllers\TechnicalOfficialRosterController;
use App\Http\Controllers\TechnicalOfficialTeamGroupController;
use App\Models\Pasukan;
use App\Models\SekatanPemain;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard', [
        'teams' => Pasukan::query()->orderBy('nama_pasukan')->get(),
    ]);
});

Route::get('/dashboard', function () {
    return view('dashboard', [
        'teams' => Pasukan::query()->orderBy('nama_pasukan')->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/teams/{pasukan}', function (Pasukan $pasukan) {
    return view('teams.show', [
        'team' => $pasukan,
        'players' => $pasukan->pemain()
            ->with('pencapaian')
            ->withCount([
                'sekatan as sekatan_count' => fn (Builder $query) => $query
                    ->where('jenis_sekatan', SekatanPemain::TYPE_MANUAL),
                'suspensions as pending_suspensions_count' => fn (Builder $query) => $query
                    ->where('status', SekatanPemain::STATUS_PENDING),
            ])
            ->orderBy('nama')
            ->paginate(15),
        'coaches' => $pasukan->jurulatih()->orderBy('nama')->get(),
    ]);
})->name('teams.show');

Route::view('/team-manager', 'dashboards.team-manager')
    ->middleware(['auth', 'verified', 'role:team_manager'])
    ->name('team-manager.dashboard');

Route::view('/technical-official', 'dashboards.technical-official')
    ->middleware(['auth', 'verified', 'role:technical_official'])
    ->name('technical-official.dashboard');

Route::middleware(['auth', 'verified', 'role:technical_official'])
    ->prefix('/technical-official/rosters')
    ->name('technical-official.rosters.')
    ->controller(TechnicalOfficialRosterController::class)
    ->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/player-template', 'playerTemplate')->name('player-template');
        Route::get('/coach-template', 'coachTemplate')->name('coach-template');
        Route::get('/teams/{pasukan}', 'show')->name('teams.show');
        Route::get('/teams/{pasukan}/players/{pemain}/edit', 'editPlayer')->name('players.edit');
        Route::post('/teams/{pasukan}/players', 'storePlayer')->name('players.store');
        Route::put('/teams/{pasukan}/players/{pemain}', 'updatePlayer')->name('players.update');
        Route::post('/teams/{pasukan}/players/import', 'importPlayers')->name('players.import');
        Route::post('/teams/{pasukan}/coaches', 'storeCoach')->name('coaches.store');
        Route::post('/teams/{pasukan}/coaches/import', 'importCoaches')->name('coaches.import');
    });

Route::middleware(['auth', 'verified', 'role:technical_official'])
    ->prefix('/technical-official/player-suspensions')
    ->name('technical-official.player-suspensions.')
    ->controller(PlayerSuspensionController::class)
    ->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/teams/{pasukan}', 'show')->name('teams.show');
        Route::post('/{pemain}', 'store')->name('store');
        Route::delete('/{pemain}', 'destroy')->name('destroy');
        Route::patch('/coaches/{jurulatih}', 'toggleCoach')->name('coaches.toggle');
    });

Route::middleware(['auth', 'verified', 'role:technical_official'])
    ->prefix('/technical-official/matches')
    ->name('technical-official.matches.')
    ->controller(TechnicalOfficialMatchController::class)
    ->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{perlawanan}/edit', 'edit')->name('edit');
        Route::put('/{perlawanan}', 'update')->name('update');
        Route::delete('/delete-all', 'destroyAll')->name('destroy-all');
        Route::delete('/{perlawanan}', 'destroy')->name('destroy');
    });

Route::patch('/technical-official/league-standings/groups/{kumpulan}/teams', [TechnicalOfficialTeamGroupController::class, 'update'])
    ->middleware(['auth', 'verified', 'role:technical_official'])
    ->name('technical-official.league-standings.groups.teams.update');

Route::get('/schedule', [MatchScheduleController::class, 'index'])->name('schedule');
Route::view('/ai-chat', 'ai-chat')->name('ai-chat');
Route::get('/matches/{perlawanan}', [MatchScheduleController::class, 'show'])->name('matches.show');
Route::get('/league-table', [LeagueTableController::class, 'overall'])->name('league-table');
Route::get('/league-standings', [LeagueTableController::class, 'groups'])->name('league-standings');
Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');
Route::get('/statistics/yellow-cards', [StatisticsController::class, 'yellowCards'])->name('statistics.yellow-cards');
Route::get('/statistics/red-cards', [StatisticsController::class, 'redCards'])->name('statistics.red-cards');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
