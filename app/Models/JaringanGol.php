<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

#[Fillable(['id_perlawanan', 'id_pemain'])]
class JaringanGol extends Model
{
    protected $table = 'jaringan_gol';

    protected static function booted(): void
    {
        static::saving(function (JaringanGol $goal): void {
            $playerTeamId = $goal->pemain()->value('id_pasukan');
            $match = $goal->perlawanan()->first(['id_pasukan_1', 'id_pasukan_2']);

            if ($playerTeamId === null || $match === null
                || ! in_array((int) $playerTeamId, [(int) $match->id_pasukan_1, (int) $match->id_pasukan_2], true)) {
                throw ValidationException::withMessages([
                    'id_pemain' => __('The selected player must belong to a team participating in this match.'),
                ]);
            }
        });
    }

    // Perlawanan di mana gol ini dijaringkan
    public function perlawanan(): BelongsTo
    {
        return $this->belongsTo(Perlawanan::class, 'id_perlawanan');
    }

    // Pemain yang menjaringkan gol ini
    public function pemain(): BelongsTo
    {
        return $this->belongsTo(Pemain::class, 'id_pemain');
    }
}
