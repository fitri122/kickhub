<?php

namespace App\Models;

use App\Observers\PerlawananObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([PerlawananObserver::class])]
#[Fillable([
    'match_label',
    'id_kumpulan',
    'id_pasukan_1',
    'id_pasukan_2',
    'tarikh',
    'masa',
    'tempat',
    'skor_pasukan_1',
    'skor_pasukan_2',
])]
class Perlawanan extends Model
{
    protected $table = 'perlawanan';

    public function displayLabel(?int $number = null): string
    {
        return filled($this->match_label)
            ? $this->match_label
            : __('Match #:number', ['number' => $number ?? $this->displayNumber()]);
    }

    public function displayNumber(): int
    {
        $date = $this->tarikh->toDateString();

        return static::query()
            ->where(function (Builder $query) use ($date): void {
                $query->whereDate('tarikh', '<', $date)
                    ->orWhere(function (Builder $query) use ($date): void {
                        $query->whereDate('tarikh', $date)
                            ->where('masa', '<', $this->masa);
                    })
                    ->orWhere(function (Builder $query) use ($date): void {
                        $query->whereDate('tarikh', $date)
                            ->where('masa', $this->masa)
                            ->where('id', '<=', $this->id);
                    });
            })
            ->count();
    }

    protected function casts(): array
    {
        return [
            'tarikh' => 'date',
        ];
    }

    // Kumpulan perlawanan ini (boleh kosong)
    public function kumpulan(): BelongsTo
    {
        return $this->belongsTo(Kumpulan::class, 'id_kumpulan');
    }

    // Pasukan pertama
    public function pasukan1(): BelongsTo
    {
        return $this->belongsTo(Pasukan::class, 'id_pasukan_1');
    }

    // Pasukan kedua
    public function pasukan2(): BelongsTo
    {
        return $this->belongsTo(Pasukan::class, 'id_pasukan_2');
    }

    // Gol yang dijaringkan dalam perlawanan ini
    public function jaringanGol(): HasMany
    {
        return $this->hasMany(JaringanGol::class, 'id_perlawanan');
    }

    // Kad kuning dan merah dalam perlawanan ini
    public function rekodDisiplin(): HasMany
    {
        return $this->hasMany(RekodDisiplin::class, 'id_perlawanan');
    }

    public function suspensionsToServe(): HasMany
    {
        return $this->hasMany(SekatanPemain::class, 'id_perlawanan_digantung')
            ->where('jenis_sekatan', SekatanPemain::TYPE_DISCIPLINARY);
    }

    // Ramalan AI bagi perlawanan ini
    public function ramalanAi(): HasMany
    {
        return $this->hasMany(RamalanAi::class, 'id_perlawanan');
    }
}
