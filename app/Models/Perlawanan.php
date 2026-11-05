<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id_kumpulan',
    'id_pasukan_1',
    'id_pasukan_2',
    'tarikh',
    'masa',
    'tempat',
    'skor_pasukan_1',
    'skor_pasukan_2',
])]
class Perlawanan extends Model
{
    protected $table = 'perlawanan';

    protected function casts(): array
    {
        return [
            'tarikh' => 'date',
        ];
    }

    // Kumpulan perlawanan ini (boleh kosong)
    public function kumpulan(): BelongsTo
    {
        return $this->belongsTo(Kumpulan::class, 'id_kumpulan');
    }

    // Pasukan pertama
    public function pasukan1(): BelongsTo
    {
        return $this->belongsTo(Pasukan::class, 'id_pasukan_1');
    }

    // Pasukan kedua
    public function pasukan2(): BelongsTo
    {
        return $this->belongsTo(Pasukan::class, 'id_pasukan_2');
    }

    // Gol yang dijaringkan dalam perlawanan ini
    public function jaringanGol(): HasMany
    {
        return $this->hasMany(JaringanGol::class, 'id_perlawanan');
    }

    // Kad kuning dan merah dalam perlawanan ini
    public function rekodDisiplin(): HasMany
    {
        return $this->hasMany(RekodDisiplin::class, 'id_perlawanan');
    }

    // Ramalan AI bagi perlawanan ini
    public function ramalanAi(): HasMany
    {
        return $this->hasMany(RamalanAi::class, 'id_perlawanan');
    }
}