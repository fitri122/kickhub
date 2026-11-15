<?php

namespace App\Observers;

use App\Models\Pemain;
use App\Services\PlayerSuspensionService;

class PemainObserver
{
    public function __construct(private PlayerSuspensionService $suspensions) {}

    public function updated(Pemain $player): void
    {
        if ($player->wasChanged('id_pasukan')) {
            $this->suspensions->recalculateForPlayer($player);
        }
    }
}
