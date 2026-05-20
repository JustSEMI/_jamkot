<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['username', 'email', 'password', 'role', 'can_panel', 'can_analisis', 'can_schedule', 'can_view3d', 'can_settings', 'can_admin'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'can_panel' => 'boolean',
            'can_analisis' => 'boolean',
            'can_schedule' => 'boolean',
            'can_view3d' => 'boolean',
            'can_settings' => 'boolean',
            'can_admin' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || (
            $this->can_panel &&
            $this->can_analisis &&
            $this->can_schedule &&
            $this->can_view3d &&
            $this->can_settings &&
            $this->can_admin
        );
    }

    /** @param string $permission  e.g. 'panel', 'analisis', 'schedule', 'view3d', 'settings' */
    public function canAccess(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return (bool) $this->{"can_{$permission}"};
    }
}
