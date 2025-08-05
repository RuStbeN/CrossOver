<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JuegoAlineacion extends Model
{
    use HasFactory;

    protected $table = 'juego_alineaciones';

    protected $fillable = [
        'juego_id',
        'jugador_id',
        'equipo_id',
        'tipo_equipo',
        'es_titular',
        'numero_camiseta',
        'posicion_jugada',
        'minutos_jugados',
        'puntos',
        'tiros_libres_anotados',
        'tiros_libres_intentados',
        'tiros_2pts_anotados',
        'tiros_2pts_intentados',
        'tiros_3pts_anotados',
        'tiros_3pts_intentados',
        'asistencias',
        'rebotes_defensivos',
        'rebotes_ofensivos',
        'rebotes_totales',
        'robos',
        'bloqueos',
        'perdidas',
        'faltas_personales',
        'faltas_tecnicas',
        'faltas_descalificantes',
        'esta_en_cancha',
        'minuto_entrada',
        'minuto_salida',
        'observaciones'
    ];

    protected $casts = [
        'es_titular' => 'boolean',
        'esta_en_cancha' => 'boolean',
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

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    // Métodos para calcular estadísticas
    public function porcentajeTirosLibres()
    {
        if ($this->tiros_libres_intentados == 0) return 0;
        return round(($this->tiros_libres_anotados / $this->tiros_libres_intentados) * 100, 1);
    }

    public function porcentajeTiros2Puntos()
    {
        if ($this->tiros_2pts_intentados == 0) return 0;
        return round(($this->tiros_2pts_anotados / $this->tiros_2pts_intentados) * 100, 1);
    }

    public function porcentajeTiros3Puntos()
    {
        if ($this->tiros_3pts_intentados == 0) return 0;
        return round(($this->tiros_3pts_anotados / $this->tiros_3pts_intentados) * 100, 1);
    }

    public function eficiencia()
    {
        return ($this->puntos + $this->rebotes_totales + $this->asistencias + $this->robos + $this->bloqueos) 
               - ($this->tiros_libres_intentados - $this->tiros_libres_anotados) 
               - ($this->tiros_2pts_intentados - $this->tiros_2pts_anotados) 
               - ($this->tiros_3pts_intentados - $this->tiros_3pts_anotados) 
               - $this->perdidas;
    }
}