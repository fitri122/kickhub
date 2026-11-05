<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_pemain', 'status'])]
class SekatanPemain extends Model
{
    protected $table = 'sekatan_pemain';

    // Pemain yang disekat atau digantung
    public function pemain(): BelongsTo
    {
        return $this->belongsTo(Pemain::class, 'id_pemain');
    }
}