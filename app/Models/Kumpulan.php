<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_kumpulan'])]
class Kumpulan extends Model
{
    protected $table = 'kumpulan';

    // Satu kumpulan mengandungi banyak perlawanan
    public function perlawanan(): HasMany
    {
        return $this->hasMany(Perlawanan::class, 'id_kumpulan');
    }
}