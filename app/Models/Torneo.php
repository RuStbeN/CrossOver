<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Torneo extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'liga_id',
        'temporada_id',
        'categoria_id',
        'cancha_id',
        'fecha_inicio',
        'fecha_fin',
        'duracion_cuarto_minutos',
        'tiempo_entre_partidos_minutos',
        'premio_total',
        'puntos_por_victoria',
        'puntos_por_empate',
        'puntos_por_derrota',
        'usa_playoffs',
        'equipos_playoffs',
        'estado',
        'activo',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'usa_playoffs' => 'boolean',
        'activo' => 'boolean',
    ];

    /**
     * Tipos de torneo disponibles
     */
    public const TIPOS = [
        'eliminacion_directa' => 'Eliminación Directa',
        'doble_eliminacion' => 'Doble Eliminación',
        'round_robin' => 'Round Robin',
        'grupos_eliminacion' => 'Grupos + Eliminación',
        'por_puntos' => 'Por Puntos'
    ];

    /**
     * Estados disponibles
     */
    public const ESTADOS = [
        'planificado' => 'Planificado',
        'en_progreso' => 'En Progreso',
        'completado' => 'Completado',
        'cancelado' => 'Cancelado'
    ];

    /**
     * Relación con la liga
     */
    public function liga()
    {
        return $this->belongsTo(Liga::class);
    }

    /**
     * Relación con la temporada
     */
    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }

    /**
     * Relación con la categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relación con las canchas (many-to-many)
     */
    public function canchas()
    {
        return $this->belongsToMany(Cancha::class, 'torneo_cancha')
                    ->withPivot(['es_principal', 'orden_prioridad'])
                    ->withTimestamps();
    }

    // Método conveniente para la cancha principal
    public function canchaPrincipal()
    {
        return $this->belongsToMany(Cancha::class, 'torneo_cancha')
                    ->wherePivot('es_principal', true)
                    ->first();
    }

    // En tu modelo Torneo.php
    public function cancha()
    {
        return $this->belongsTo(Cancha::class);
    }

    /**
     * Relación con los equipos (many-to-many)
     */
    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'torneo_equipo')
                    ->withPivot('grupo')
                    ->withTimestamps();
    }

    /**
     * Relación con el juego
     */
    public function juegos()
    {
        return $this->hasMany(Juego::class);
    }

    public function clasificacion()
    {
        return $this->hasMany(TorneoClasificacion::class)->orderBy('posicion');
    }

    /**
     * Relación con el usuario creador
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que actualizó
     */
    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtener el nombre del tipo formateado
     */
    public function getTipoFormateadoAttribute()
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    /**
     * Obtener el estado formateado
     */
    public function getEstadoFormateadoAttribute()
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    /**
     * Scope para torneos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para torneos por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }
}