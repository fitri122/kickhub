<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}