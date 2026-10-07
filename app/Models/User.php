<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    public function teams()
    {
        return $this->belongsToMany(Company::class, 'team_user', 'user_id', 'team_id')
            ->withPivot('role')
            ->withTimestamps()
            ->as('membership');
    }

    public function ownedTeams()
    {
        return $this->hasMany(Company::class, 'user_id');
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'system_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    public function system()
    {
        return $this->belongsTo(System::class, 'system_id');
    }

    public function resolveSystem(): ?System
    {
        if ($this->system_id) {
            return $this->system()->first();
        }

        $team = $this->teams()->with('system')->first();
        return $team?->system;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \App\Notifications\VerifyEmail);
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new \App\Notifications\ResetPassword($token));
    }

    public function isSuperAdmin(): bool
    {
        return $this->role_id === 1;
    }

    public function isAdmin(): bool
    {
        return $this->role_id === 2;
    }

    public function isUser(): bool
    {
        return $this->role_id === 3;
    }

    public function canCreate(): bool
    {
        if ($this->isSuperAdmin()) return true;

        if ($this->system?->slug === 'tbr') return $this->isAdmin();

        if (in_array($this->system?->slug, ['financeiro', 'clientes'])) return true;

        return in_array($this->role_id, [2, 3]);
    }

    public function canEdit(): bool
    {
        if ($this->isSuperAdmin()) return true;

        if ($this->system?->slug === 'tbr') return $this->isAdmin();

        if (in_array($this->system?->slug, ['financeiro', 'clientes'])) return true;

        return in_array($this->role_id, [2]);
    }

    public function canDelete(): bool
    {
        if ($this->isSuperAdmin()) return true;

        if ($this->system?->slug === 'tbr') return false;

        if (in_array($this->system?->slug, ['financeiro', 'clientes'])) return true;

        return $this->role_id === 1;
    }
}
