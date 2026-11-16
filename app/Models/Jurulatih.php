<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_pasukan', 'nama', 'umur', 'is_blocked'])]
class Jurulatih extends Model
{
    protected $table = 'jurulatih';

    protected function casts(): array
    {
        return [
            'umur' => 'integer',
            'is_blocked' => 'boolean',
        ];
    }

    public function pasukan(): BelongsTo
    {
        return $this->belongsTo(Pasukan::class, 'id_pasukan');
    }
}
