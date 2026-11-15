<?php

namespace App\Observers;

use App\Models\RekodDisiplin;
use App\Services\PlayerSuspensionService;

class RekodDisiplinObserver
{
    public function __construct(private PlayerSuspensionService $suspensions) {}

    public function created(RekodDisiplin $record): void
    {
        $this->suspensions->recalculateForPlayerId((int) $record->id_pemain);
    }

    public function updating(RekodDisiplin $record): void
    {
        if (! $record->isDirty(['id_pemain', 'id_perlawanan', 'jenis_kad', 'minit'])) {
            return;
        }

        $this->suspensions->recalculateForPlayerId(
            (int) $record->getOriginal('id_pemain'),
            (int) $record->id,
        );
    }

    public function updated(RekodDisiplin $record): void
    {
        $this->suspensions->recalculateForPlayerId((int) $record->id_pemain);
    }

    public function deleted(RekodDisiplin $record): void
    {
        $this->suspensions->recalculateForPlayerId((int) $record->id_pemain);
    }
}
