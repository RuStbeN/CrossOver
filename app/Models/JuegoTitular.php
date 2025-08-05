<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JuegoTitular extends Model
{
    use HasFactory;

    protected $fillable = [
        'juego_id',
        'jugador_id',
        'tipo_equipo',
        'es_titular',
        'minutos_jugados',
        'puntos',
        'asistencias',
        'rebotes',
        'faltas',
        'observaciones'
    ];

    protected $casts = [
        'es_titular' => 'boolean'
    ];

    // Relaciones
    public function juego()
    {
        return $this->belongsTo(Juego::class);
    }

    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }
}