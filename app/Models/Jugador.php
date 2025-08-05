<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jugador extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'rfc',
        'edad',
        'sexo',
        'direccion',
        'estado_fisico',
        'telefono',
        'email',
        'foto_url',
        'liga_id',
        'categoria_id',
        'contacto_emergencia_nombre', // Corregí el nombre del campo
        'contacto_emergencia_telefono',
        'contacto_emergencia_relacion',
        'fecha_nacimiento',
        'activo',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_nacimiento' => 'date'
    ];

    
    protected $table = 'jugadores';


    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'equipo_jugadores')
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

    public function equipos_actual()
    {
        return $this->hasOne(EquipoJugador::class, 'jugador_id')
            ->where('activo', true)
            ->with('equipo');
    }

    // Accesor para el número de camiseta actual
    public function getNumeroActualAttribute()
    {
        return $this->equipos_actual->numero_camiseta ?? null;
    }

    // Accesor para la posición principal actual
    public function getPosicionActualAttribute()
    {
        return $this->equipos_actual->posicion_principal ?? null;
    }

    // Accesor para el equipo actual (ya deberías tener este)
    public function getEquipoActualAttribute()
    {
        return $this->equipos_actual->equipo ?? null;
    }

    // Relaciones
    public function liga()
    {
        return $this->belongsTo(Liga::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function expedienteMedico()
    {
        return $this->hasOne(ExpedienteMedico::class);
    }

    public function titulares()
    {
        return $this->hasMany(JuegoTitular::class);
    }

}