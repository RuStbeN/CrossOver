<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Temporada extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'horario_inicio',
        'horario_fin',
        'descripcion',
        'activo',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'horario_inicio' => 'datetime:H:i',
        'horario_fin' => 'datetime:H:i'
    ];

    // Validación de fechas y horarios
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            // Validar fechas
            if ($model->fecha_fin <= $model->fecha_inicio) {
                throw new \Exception('La fecha fin debe ser posterior a la fecha inicio');
            }
            
            // Validar horarios
            if ($model->horario_fin <= $model->horario_inicio) {
                throw new \Exception('El horario fin debe ser posterior al horario inicio');
            }
        });
    }

    // Scopes útiles
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeEnPeriodo($query, $fecha = null)
    {
        $fecha = $fecha ?? today();
        return $query->where('fecha_inicio', '<=', $fecha)
                    ->where('fecha_fin', '>=', $fecha);
    }

    // Relaciones
    public function diasHabiles()
    {
        return $this->hasMany(TemporadaDiaHabil::class);
    }

    public function juegos()
    {
        return $this->hasMany(Juego::class);
    }

    // Relaciones de auditoría
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors útiles
    public function getPeriodoAttribute()
    {
        return $this->fecha_inicio->format('d/m/Y') . ' - ' . $this->fecha_fin->format('d/m/Y');
    }

    public function getHorarioAttribute()
    {
        return $this->horario_inicio->format('H:i') . ' - ' . $this->horario_fin->format('H:i');
    }
}