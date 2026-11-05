<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_pasukan',
    'id_pengurus',
    'nama_fail',
    'jenis_fail',
    'tarikh_import',
    'status',
])]
class RekodImport extends Model
{
    protected $table = 'rekod_import';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'tarikh_import' => 'datetime',
        ];
    }

    // Pasukan yang menerima data import
    public function pasukan(): BelongsTo
    {
        return $this->belongsTo(Pasukan::class, 'id_pasukan');
    }

    // Pengurus yang memuat naik fail import
    public function pengurus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengurus');
    }
}