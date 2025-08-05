<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Arbitro extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nombre',
        'edad',
        'direccion',
        'telefono',
        'correo',
        'foto',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'edad' => 'integer'
    ];
    
    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $dates = ['deleted_at'];

    // Accessor para obtener la URL completa de la foto
    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    // Relación con juegos como árbitro principal
    public function juegosComoPrincipal(): HasMany
    {
        return $this->hasMany(Juego::class, 'arbitro_principal_id');
    }

    // Relación con juegos como árbitro auxiliar
    public function juegosComoAuxiliar(): HasMany
    {
        return $this->hasMany(Juego::class, 'arbitro_auxiliar_id');
    }

    // Scope para obtener solo árbitros activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Scope para obtener solo árbitros inactivos
    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }

    // Método para verificar si el árbitro está asignado a algún juego
    public function estaEnUso(): bool
    {
        return $this->juegosComoPrincipal()->exists() || 
               $this->juegosComoAuxiliar()->exists();
    }
}