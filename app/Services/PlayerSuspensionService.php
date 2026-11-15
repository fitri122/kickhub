<?php

namespace App\Services;

use App\Models\Pemain;
use App\Models\Perlawanan;
use App\Models\SekatanPemain;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PlayerSuspensionService
{
    public function recalculateForPlayer(Pemain $player, ?int $excludingDisciplineId = null): void
    {
        DB::transaction(function () use ($player, $excludingDisciplineId): void {
            $player = Pemain::query()
                ->lockForUpdate()
                ->findOrFail($player->getKey());

            $player->suspensions()->delete();

            $matches = Perlawanan::query()
                ->where(function (Builder $query) use ($player): void {
                    $query->where('id_pasukan_1', $player->id_pasukan)
                        ->orWhere('id_pasukan_2', $player->id_pasukan);
                })
                ->orderBy('tarikh')
                ->orderBy('masa')
                ->orderBy('id')
                ->get([
                    'id',
                    'id_pasukan_1',
                    'id_pasukan_2',
                    'tarikh',
                    'masa',
                    'skor_pasukan_1',
                    'skor_pasukan_2',
                ]);
            $matchPositions = $matches->pluck('id')->flip();

            $cards = $player->rekodDisiplin()
                ->when(
                    $excludingDisciplineId !== null,
                    fn (Builder $query): Builder => $query->where('id', '!=', $excludingDisciplineId),
                )
                ->get()
                ->filter(fn ($card): bool => $matchPositions->has($card->id_perlawanan))
                ->sort(function ($left, $right) use ($matchPositions): int {
                    return ($matchPositions->get($left->id_perlawanan) <=> $matchPositions->get($right->id_perlawanan))
                        ?: ((int) $left->minit <=> (int) $right->minit)
                        ?: ($left->id <=> $right->id);
                })
                ->values();

            $triggerCards = [];
            $triggeredMatches = [];
            $yellowCardCount = 0;

            foreach ($cards as $card) {
                $trigger = $card->jenis_kad === 'merah';

                if ($card->jenis_kad === 'kuning') {
                    $yellowCardCount++;
                    $trigger = $yellowCardCount >= 2;

                    if ($trigger) {
                        $yellowCardCount = 0;
                    }
                }

                if ($trigger && ! isset($triggeredMatches[$card->id_perlawanan])) {
                    $triggerCards[] = $card;
                    $triggeredMatches[$card->id_perlawanan] = true;
                }
            }

            $usedServingMatches = [];

            foreach ($triggerCards as $card) {
                $incidentPosition = $matchPositions->get($card->id_perlawanan);
                $servingMatch = $matches->first(
                    fn (Perlawanan $match, int $position): bool => $position > $incidentPosition
                        && ! isset($usedServingMatches[$match->id]),
                );

                if ($servingMatch !== null) {
                    $usedServingMatches[$servingMatch->id] = true;
                }

                $isServed = $servingMatch !== null
                    && $servingMatch->skor_pasukan_1 !== null
                    && $servingMatch->skor_pasukan_2 !== null;

                $player->suspensions()->create([
                    'status' => $isServed
                        ? SekatanPemain::STATUS_SERVED
                        : SekatanPemain::STATUS_PENDING,
                    'jenis_sekatan' => SekatanPemain::TYPE_DISCIPLINARY,
                    'id_rekod_disiplin' => $card->id,
                    'id_perlawanan_digantung' => $servingMatch?->id,
                ]);
            }
        });
    }

    public function recalculateForPlayerId(int $playerId, ?int $excludingDisciplineId = null): void
    {
        $player = Pemain::query()->find($playerId);

        if ($player !== null) {
            $this->recalculateForPlayer($player, $excludingDisciplineId);
        }
    }

    public function recalculateForTeam(int $teamId): void
    {
        Pemain::query()
            ->where('id_pasukan', $teamId)
            ->get(['id', 'id_pasukan'])
            ->each(fn (Pemain $player) => $this->recalculateForPlayer($player));
    }

    public function isEligibleForMatch(Pemain $player, Perlawanan $match): bool
    {
        if ($player->manualSekatan()->exists()) {
            return false;
        }

        if (! in_array((int) $player->id_pasukan, [(int) $match->id_pasukan_1, (int) $match->id_pasukan_2], true)) {
            return false;
        }

        $suspensions = $player->suspensions()
            ->with(['rekodDisiplin.perlawanan', 'perlawananDigantung'])
            ->get();

        foreach ($suspensions as $suspension) {
            $servingMatch = $suspension->perlawananDigantung;

            if ($servingMatch?->is($match)) {
                return false;
            }

            if ($servingMatch !== null) {
                $isServed = $servingMatch->skor_pasukan_1 !== null
                    && $servingMatch->skor_pasukan_2 !== null;

                if (! $isServed && $this->compareMatches($match, $servingMatch) >= 0) {
                    return false;
                }

                continue;
            }

            $incidentMatch = $suspension->rekodDisiplin?->perlawanan;

            if ($incidentMatch !== null && $this->compareMatches($match, $incidentMatch) > 0) {
                return false;
            }
        }

        return true;
    }

    private function compareMatches(Perlawanan $left, Perlawanan $right): int
    {
        return strcmp($left->tarikh->toDateString(), $right->tarikh->toDateString())
            ?: strcmp($left->masa, $right->masa)
            ?: ($left->id <=> $right->id);
    }
}
