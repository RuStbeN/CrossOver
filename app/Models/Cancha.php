<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cancha extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'direccion',
        'capacidad',
        'tipo_superficie',
        'techada',
        'iluminacion',
        'equipamiento',
        'tarifa_por_hora',
        'activo',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'techada' => 'boolean',
        'iluminacion' => 'boolean',
        'activo' => 'boolean',
        'tarifa_por_hora' => 'decimal:2'
    ];

    // Relaciones
    public function juegos()
    {
        return $this->hasMany(Juego::class);
    }

    public function torneos()
    {
        return $this->belongsToMany(Torneo::class, 'torneo_cancha')
                    ->withTimestamps();
    }
}