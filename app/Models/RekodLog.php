<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_pengguna', 'aktiviti', 'tarikh_masa'])]
class RekodLog extends Model
{
    protected $table = 'rekod_log';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'tarikh_masa' => 'datetime',
        ];
    }

    // Pengguna yang melakukan aktiviti ini
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }
}