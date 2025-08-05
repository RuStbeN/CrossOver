<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TorneoClasificacion extends Model
{
    use HasFactory;

    protected $table = 'torneo_clasificacion';

    protected $fillable = [
        'torneo_id',
        'equipo_id',
        'partidos_jugados',
        'partidos_ganados',
        'partidos_empatados',
        'partidos_perdidos',
        'puntos_totales',
        'puntos_favor',
        'puntos_contra',
        'diferencia_puntos',
        'posicion'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    /**
     * Actualiza la posición de todos los equipos en el torneo
     */
    public static function actualizarPosiciones($torneoId)
    {
        $clasificaciones = self::where('torneo_id', $torneoId)
            ->orderBy('puntos_totales', 'DESC')
            ->orderBy('diferencia_puntos', 'DESC')
            ->orderBy('puntos_favor', 'DESC')
            ->get();

        $posicion = 1;
        foreach ($clasificaciones as $clasificacion) {
            $clasificacion->update(['posicion' => $posicion++]);
        }
    }
}