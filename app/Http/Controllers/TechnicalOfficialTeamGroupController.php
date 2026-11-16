<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveGroupTeamsRequest;
use App\Models\Kumpulan;
use App\Models\Pasukan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TechnicalOfficialTeamGroupController extends Controller
{
    public function update(SaveGroupTeamsRequest $request, Kumpulan $kumpulan): RedirectResponse
    {
        $validated = $request->validated();
        $teamIds = array_map('intval', $validated['team_ids'] ?? []);

        DB::transaction(function () use ($kumpulan, $teamIds): void {
            $teamsToUnassign = Pasukan::query()
                ->where('id_kumpulan', $kumpulan->id);

            if ($teamIds !== []) {
                $teamsToUnassign->whereNotIn('id', $teamIds);
            }

            $teamsToUnassign->update(['id_kumpulan' => null]);

            if ($teamIds !== []) {
                Pasukan::query()
                    ->whereIn('id', $teamIds)
                    ->update(['id_kumpulan' => $kumpulan->id]);
            }
        });

        return redirect()
            ->route('league-standings')
            ->with('status', __('Teams updated for :group.', [
                'group' => str_replace('Kumpulan ', 'Group ', $kumpulan->nama_kumpulan),
            ]));
    }
}
