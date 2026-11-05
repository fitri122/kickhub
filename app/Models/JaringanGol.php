<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_perlawanan', 'id_pemain'])]
class JaringanGol extends Model
{
    protected $table = 'jaringan_gol';

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