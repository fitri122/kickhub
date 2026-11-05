<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_pemain',
    'nama_pencapaian',
    'tahun',
    'peringkat',
])]
class PencapaianPemain extends Model
{
    protected $table = 'pencapaian_pemain';

    // Pemain yang memperoleh pencapaian ini
    public function pemain(): BelongsTo
    {
        return $this->belongsTo(Pemain::class, 'id_pemain');
    }
}