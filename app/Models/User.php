<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'password_changed_at', // Añade esto
        'must_change_password' // Añade esto
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'password_changed_at' => 'datetime', // Añade esto
            'must_change_password' => 'boolean' // Añade esto
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'rol_usuario', 'usuario_id', 'rol_id');
    }

    public function tieneRol(string $nombreRol): bool
    {
        return $this->roles()->where('nombre', $nombreRol)->exists();
    }

    public function hasRole(string $role): bool
    {
        return $this->tieneRol($role);
    }


    public function tienePermiso(string $nombrePermiso): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($nombrePermiso) {
            $query->where('nombre', $nombrePermiso);
        })->exists();
    }

    public function tieneAlgunRol(array $roles): bool
    {
        return $this->roles()->whereIn('nombre', $roles)->exists();
    }
}