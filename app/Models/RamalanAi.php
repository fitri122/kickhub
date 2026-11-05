<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_perlawanan', 'ulasan_ai'])]
class RamalanAi extends Model
{
    protected $table = 'ramalan_ai';

    // Perlawanan yang diramal atau dianalisis oleh AI
    public function perlawanan(): BelongsTo
    {
        return $this->belongsTo(Perlawanan::class, 'id_perlawanan');
    }
}