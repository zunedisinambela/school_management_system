<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Binafy\LaravelUserMonitoring\Traits\Actionable;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use Actionable, HasFactory, HasRoles, Notifiable;

    /**
     * Only users holding at least one role may enter the panel; Shield permissions decide the rest.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->roles()->exists();
    }

    /**
     * Whether the user holds Shield's super admin role, which bypasses every permission check.
     */
    public function isDeveloper(): bool
    {
        return $this->hasRole(config('filament-shield.super_admin.name'));
    }

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
}
