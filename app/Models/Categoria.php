<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'edad_minima',
        'edad_maxima',
        'liga_id',
        'activo',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    // Validación personalizada
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->edad_maxima <= $model->edad_minima) {
                throw new \Exception('La edad máxima debe ser mayor que la mínima');
            }
        });
    }

    // Relaciones
    public function liga()
    {
        return $this->belongsTo(Liga::class);
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    public function jugadores()
    {
        return $this->hasMany(Jugador::class);
    }
}