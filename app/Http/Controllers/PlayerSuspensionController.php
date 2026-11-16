<?php

namespace App\Http\Controllers;

use App\Models\Jurulatih;
use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\SekatanPemain;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlayerSuspensionController extends Controller
{
    public function index(Request $request): View
    {
        return view('technical-official.player-suspensions', [
            'teams' => $this->teams(),
            'selectedTeam' => null,
            'section' => $this->section($request->query('section')),
            'players' => null,
            'coaches' => null,
        ]);
    }

    public function show(Pasukan $pasukan, Request $request): View
    {
        $section = $this->section($request->query('section'));
        $players = $section === 'players'
            ? $pasukan->pemain()
                ->withCount([
                    'manualSekatan',
                    'suspensions as pending_suspensions_count' => fn ($query) => $query
                        ->where('status', SekatanPemain::STATUS_PENDING),
                ])
                ->orderBy('nama')
                ->paginate(20)
            : null;
        $coaches = $section === 'coaches'
            ? $pasukan->jurulatih()->orderBy('nama')->paginate(20)
            : null;

        return view('technical-official.player-suspensions', [
            'teams' => $this->teams(),
            'selectedTeam' => $pasukan,
            'section' => $section,
            'players' => $players,
            'coaches' => $coaches,
        ]);
    }

    public function store(Pemain $pemain): RedirectResponse
    {
        $pemain->manualSekatan()->firstOrCreate(
            ['jenis_sekatan' => SekatanPemain::TYPE_MANUAL],
            ['status' => SekatanPemain::STATUS_BLOCKED],
        );

        return redirect()
            ->route('technical-official.player-suspensions.teams.show', $pemain->id_pasukan)
            ->with('status', __('Player blocked successfully.'));
    }

    public function destroy(Pemain $pemain): RedirectResponse
    {
        $pemain->manualSekatan()->delete();

        return redirect()
            ->route('technical-official.player-suspensions.teams.show', $pemain->id_pasukan)
            ->with('status', __('Player activated successfully.'));
    }

    public function toggleCoach(Jurulatih $jurulatih): RedirectResponse
    {
        $jurulatih->is_blocked = ! $jurulatih->is_blocked;
        $jurulatih->save();

        return redirect()
            ->route('technical-official.player-suspensions.teams.show', [
                'pasukan' => $jurulatih->id_pasukan,
                'section' => 'coaches',
            ])
            ->with('status', $jurulatih->is_blocked
                ? __('Coach blocked successfully.')
                : __('Coach activated successfully.'));
    }

    /**
     * @return Collection<int, Pasukan>
     */
    private function teams(): Collection
    {
        return Pasukan::query()
            ->withCount(['pemain', 'jurulatih'])
            ->orderBy('nama_pasukan')
            ->get(['id', 'nama_pasukan']);
    }

    private function section(mixed $section): string
    {
        return $section === 'coaches' ? 'coaches' : 'players';
    }
}
