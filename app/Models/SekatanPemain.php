<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_pemain',
    'status',
    'jenis_sekatan',
    'id_rekod_disiplin',
    'id_perlawanan_digantung',
])]
class SekatanPemain extends Model
{
    public const TYPE_MANUAL = 'manual';

    public const TYPE_DISCIPLINARY = 'disciplinary';

    public const STATUS_PENDING = 'Pending';

    public const STATUS_SERVED = 'Served';

    protected $table = 'sekatan_pemain';

    // Pemain yang disekat atau digantung
    public function pemain(): BelongsTo
    {
        return $this->belongsTo(Pemain::class, 'id_pemain');
    }

    public function rekodDisiplin(): BelongsTo
    {
        return $this->belongsTo(RekodDisiplin::class, 'id_rekod_disiplin');
    }

    public function perlawananDigantung(): BelongsTo
    {
        return $this->belongsTo(Perlawanan::class, 'id_perlawanan_digantung');
    }
}
