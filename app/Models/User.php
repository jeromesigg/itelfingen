<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'role_id', 'is_active', 'fullname', 'phone', 'signature',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'role_id' => UserRole::class,
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Selbst registrierte Benutzer haben keine Rolle, deshalb überall null-sicher.
     */
    public function isAdmin()
    {
        return $this->role_id?->isAdmin() && $this->is_active == 1;
    }

    /**
     * Zugang zum Admin-Bereich überhaupt (Team, Verwalter und Admin).
     */
    public function isTeam()
    {
        return $this->role_id?->hasAdminArea() && $this->is_active == 1;
    }

    public function isManager()
    {
        return $this->role_id === UserRole::Verwalter && $this->is_active == 1;
    }

    /**
     * Buchungen, Anfragen, Bewerbungen, Checkliste und Newsletter bearbeiten (Verwalter und Admin).
     */
    public function canManageBookings()
    {
        return $this->isAdmin() || $this->isManager();
    }

    /**
     * Stufe für die Middleware "role": team < verwalter < admin.
     */
    public function hasRoleLevel(string $level)
    {
        return match ($level) {
            'admin' => $this->isAdmin(),
            'verwalter' => $this->canManageBookings(),
            'team' => $this->isTeam(),
        };
    }
}
