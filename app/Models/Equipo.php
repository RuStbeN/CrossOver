<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'categoria_id',
        'liga_id',
        'entrenador_id',
        'logo_url',
        'color_primario',
        'color_secundario',
        'fecha_fundacion',
        'activo',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_fundacion' => 'date'
    ];

    protected $table = 'equipos';

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function liga()
    {
        return $this->belongsTo(Liga::class);
    }

    public function entrenador()
    {
        return $this->belongsTo(Entrenador::class);
    }

    public function juegosComoLocal()
    {
        return $this->hasMany(Juego::class, 'equipo_local_id');
    }

    public function juegosComoVisitante()
    {
        return $this->hasMany(Juego::class, 'equipo_visitante_id');
    }

    public function jugadores()
    {
        return $this->belongsToMany(Jugador::class, 'equipo_jugadores')
            ->using(EquipoJugador::class)
            ->withPivot([
                'numero_camiseta',
                'posicion_principal',
                'posicion_secundaria',
                'fecha_ingreso',
                'fecha_salida',
                'es_capitan',
                'activo',
                'temporada_id',
                'torneo_id'
            ]);
    }

    public function jugadores_actuales()
    {
        return $this->belongsToMany(Jugador::class, 'equipo_jugadores')
            ->wherePivot('activo', true);
    }

}
