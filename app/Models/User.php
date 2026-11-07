<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const ROLE_REGULAR_USER = 'regular_user';

    public const ROLE_TEAM_MANAGER = 'team_manager';

    public const ROLE_TECHNICAL_OFFICIAL = 'technical_official';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_TEAM_MANAGER => 'team-manager.dashboard',
            self::ROLE_TECHNICAL_OFFICIAL => 'technical-official.dashboard',
            default => 'dashboard',
        };
    }

    // Seorang pengurus boleh mengurus banyak pasukan
    public function pasukan(): HasMany
    {
        return $this->hasMany(Pasukan::class, 'id_pengurus');
    }

    // Log aktiviti pengguna ini
    public function rekodLog(): HasMany
    {
        return $this->hasMany(RekodLog::class, 'id_pengguna');
    }

    // Rekod import fail oleh pengurus ini
    public function rekodImport(): HasMany
    {
        return $this->hasMany(RekodImport::class, 'id_pengurus');
    }

    // Rekod eksport Excel oleh pengurus ini
    public function eksportExcel(): HasMany
    {
        return $this->hasMany(EksportExcel::class, 'id_pengurus');
    }
}