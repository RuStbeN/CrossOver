<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EquipoJugador extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'equipo_id',
        'jugador_id',
        'temporada_id',
        'torneo_id',
        'fecha_ingreso',
        'fecha_salida',
        'numero_camiseta',
        'posicion_principal',
        'posicion_secundaria',
        'es_capitan',
        'activo',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'es_capitan' => 'boolean',
        'activo' => 'boolean',
        'fecha_ingreso' => 'date',
        'fecha_salida' => 'date'
    ];

    protected $table = 'equipo_jugadores';

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }

    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }
}