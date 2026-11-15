<?php

namespace App\Models;

use App\Observers\RekodDisiplinObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\ValidationException;

#[ObservedBy([RekodDisiplinObserver::class])]
#[Fillable([
    'id_perlawanan',
    'id_pemain',
    'jenis_kad',
    'minit',
    'catatan',
])]
class RekodDisiplin extends Model
{
    protected $table = 'rekod_disiplin';

    protected static function booted(): void
    {
        static::saving(function (RekodDisiplin $record): void {
            $playerTeamId = $record->pemain()->value('id_pasukan');
            $match = $record->perlawanan()->first(['id_pasukan_1', 'id_pasukan_2']);

            if ($playerTeamId === null || $match === null
                || ! in_array((int) $playerTeamId, [(int) $match->id_pasukan_1, (int) $match->id_pasukan_2], true)) {
                throw ValidationException::withMessages([
                    'id_pemain' => __('The selected player must belong to a team participating in this match.'),
                ]);
            }
        });
    }

    // Perlawanan di mana kad ini dikeluarkan
    public function perlawanan(): BelongsTo
    {
        return $this->belongsTo(Perlawanan::class, 'id_perlawanan');
    }

    // Pemain yang menerima kad ini
    public function pemain(): BelongsTo
    {
        return $this->belongsTo(Pemain::class, 'id_pemain');
    }

    public function sekatan(): HasOne
    {
        return $this->hasOne(SekatanPemain::class, 'id_rekod_disiplin');
    }
}
