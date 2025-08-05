<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Liga extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    // Relaciones
    public function categorias()
    {
        return $this->hasMany(Categoria::class);
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    // En app/Models/Liga.php
    public function jugadores()
    {
        return $this->hasManyThrough(
            Jugador::class,
            Equipo::class,
            'liga_id', // Foreign key on equipos table
            'id',      // Foreign key on jugadores table
            'id',      // Local key on ligas table
            'id'       // Local key on equipos table
        )->join('equipo_jugadores', 'jugadores.id', '=', 'equipo_jugadores.jugador_id')
        ->where('equipo_jugadores.activo', true)
        ->select('jugadores.*')
        ->distinct();
    }

    public function juegos()
    {
        return $this->hasMany(Juego::class);
    }
}