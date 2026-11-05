<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id_pasukan',
    'nama',
    'no_ic',
    'no_jersi',
    'umur',
    'gambar',
    'role_pemain',
    'position',
])]
class Pemain extends Model
{
    protected $table = 'pemain';

    // Pasukan yang memiliki pemain ini
    public function pasukan(): BelongsTo
    {
        return $this->belongsTo(Pasukan::class, 'id_pasukan');
    }

    // Gol yang dijaringkan oleh pemain ini
    public function jaringanGol(): HasMany
    {
        return $this->hasMany(JaringanGol::class, 'id_pemain');
    }

    // Kad kuning dan merah yang diterima pemain ini
    public function rekodDisiplin(): HasMany
    {
        return $this->hasMany(RekodDisiplin::class, 'id_pemain');
    }

    // Pencapaian pemain ini (seorang pemain boleh ada banyak)
    public function pencapaian(): HasMany
    {
        return $this->hasMany(PencapaianPemain::class, 'id_pemain');
    }

    // Rekod sekatan atau penggantungan pemain ini
    public function sekatan(): HasMany
    {
        return $this->hasMany(SekatanPemain::class, 'id_pemain');
    }
}