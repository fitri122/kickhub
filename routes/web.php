<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeagueTableController;
use App\Models\Pasukan;
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
            ->withCount('sekatan')
            ->orderBy('nama')
            ->paginate(15),
        'coaches' => [
            ['nama' => 'Ahmad Firdaus', 'umur' => 45, 'status' => 'Active'],
            ['nama' => 'Hafiz Rahman', 'umur' => 39, 'status' => 'Active'],
            ['nama' => 'Daniel Zulkifli', 'umur' => 42, 'status' => 'Active'],
        ],
    ]);
})->name('teams.show');

Route::view('/team-manager', 'dashboards.team-manager')
    ->middleware(['auth', 'verified', 'role:team_manager'])
    ->name('team-manager.dashboard');

Route::view('/technical-official', 'dashboards.technical-official')
    ->middleware(['auth', 'verified', 'role:technical_official'])
    ->name('technical-official.dashboard');

Route::view('/schedule', 'schedule')->name('schedule');
Route::get('/league-table', [LeagueTableController::class, 'overall'])->name('league-table');
Route::get('/league-standings', [LeagueTableController::class, 'groups'])->name('league-standings');
Route::view('/statistics', 'statistics')->name('statistics');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
