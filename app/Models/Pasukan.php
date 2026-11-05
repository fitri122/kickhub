<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_pasukan', 'nama_kolej', 'id_pengurus'])]
class Pasukan extends Model
{
    protected $table = 'pasukan';

    // Pengurus yang mengendalikan pasukan ini
    public function pengurus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengurus');
    }

    // Satu pasukan mempunyai banyak pemain
    public function pemain(): HasMany
    {
        return $this->hasMany(Pemain::class, 'id_pasukan');
    }

    // Perlawanan di mana pasukan ini ialah pasukan pertama
    public function perlawananSebagaiPasukan1(): HasMany
    {
        return $this->hasMany(Perlawanan::class, 'id_pasukan_1');
    }

    // Perlawanan di mana pasukan ini ialah pasukan kedua
    public function perlawananSebagaiPasukan2(): HasMany
    {
        return $this->hasMany(Perlawanan::class, 'id_pasukan_2');
    }

    // Rekod eksport Excel bagi pasukan ini
    public function eksportExcel(): HasMany
    {
        return $this->hasMany(EksportExcel::class, 'id_pasukan');
    }

    // Rekod import fail bagi pasukan ini
    public function rekodImport(): HasMany
    {
        return $this->hasMany(RekodImport::class, 'id_pasukan');
    }
}