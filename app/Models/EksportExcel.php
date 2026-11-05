<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_pasukan',
    'id_pengurus',
    'nama_fail_excel',
    'tarikh_muat_turun',
])]
class EksportExcel extends Model
{
    protected $table = 'eksport_excel';

    protected function casts(): array
    {
        return [
            'tarikh_muat_turun' => 'datetime',
        ];
    }

    // Pasukan yang datanya dieksport
    public function pasukan(): BelongsTo
    {
        return $this->belongsTo(Pasukan::class, 'id_pasukan');
    }

    // Pengurus yang memuat turun fail Excel ini
    public function pengurus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengurus');
    }
}