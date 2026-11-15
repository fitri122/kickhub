<?php

namespace App\Observers;

use App\Models\Perlawanan;
use App\Services\PlayerSuspensionService;

class PerlawananObserver
{
    public function __construct(private PlayerSuspensionService $suspensions) {}

    public function created(Perlawanan $match): void
    {
        $this->recalculateForMatchTeams($match);
    }

    public function updated(Perlawanan $match): void
    {
        if ($match->wasChanged([
            'id_pasukan_1',
            'id_pasukan_2',
            'tarikh',
            'masa',
            'skor_pasukan_1',
            'skor_pasukan_2',
        ])) {
            $teamIds = [$match->id_pasukan_1, $match->id_pasukan_2];

            foreach (['id_pasukan_1', 'id_pasukan_2'] as $teamColumn) {
                if (array_key_exists($teamColumn, $match->getPrevious())) {
                    $teamIds[] = $match->getPrevious()[$teamColumn];
                }
            }

            $this->recalculateForTeamIds($teamIds);
        }
    }

    public function deleted(Perlawanan $match): void
    {
        $this->recalculateForMatchTeams($match);
    }

    private function recalculateForMatchTeams(Perlawanan $match): void
    {
        $this->recalculateForTeamIds([$match->id_pasukan_1, $match->id_pasukan_2]);
    }

    private function recalculateForTeamIds(array $teamIds): void
    {
        foreach (array_unique(array_filter($teamIds)) as $teamId) {
            $this->suspensions->recalculateForTeam((int) $teamId);
        }
    }
}
