<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporadaDiaHabil extends Model
{
    use HasFactory;

    protected $table = 'temporada_dias_habiles';

    protected $fillable = [
        'temporada_id',
        'dia_semana',
        'horario_inicio',
        'horario_fin',
        'activo'
    ];

    protected $casts = [
        'dia_semana' => 'integer',
        'horario_inicio' => 'datetime:H:i',
        'horario_fin' => 'datetime:H:i',
        'activo' => 'boolean'
    ];

    // Constantes para días de la semana
    const DIAS_SEMANA = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo'
    ];

    // Validaciones
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            // Validar día de semana
            if ($model->dia_semana < 1 || $model->dia_semana > 7) {
                throw new \Exception('El día de semana debe estar entre 1 y 7');
            }
            
            // Validar horarios si están definidos
            if ($model->horario_inicio && $model->horario_fin) {
                if ($model->horario_fin <= $model->horario_inicio) {
                    throw new \Exception('El horario fin debe ser posterior al horario inicio');
                }
            }
        });
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorDia($query, $dia)
    {
        return $query->where('dia_semana', $dia);
    }

    // Relaciones
    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }

    // Accessors
    public function getDiaSemanaNombreAttribute()
    {
        return self::DIAS_SEMANA[$this->dia_semana] ?? 'Desconocido';
    }

    public function getDiaSemanaCortoAttribute()
    {
        $diasCortos = [
            1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue',
            5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'
        ];
        return $diasCortos[$this->dia_semana] ?? 'N/A';
    }

    // Método estático para obtener días
    public static function getDiasSemana()
    {
        return self::DIAS_SEMANA;
    }

    // Método para verificar si es día hábil típico (L-V)
    public function esLaboral()
    {
        return $this->dia_semana >= 1 && $this->dia_semana <= 5;
    }

    // Método para verificar si es fin de semana
    public function esFinDeSemana()
    {
        return $this->dia_semana == 6 || $this->dia_semana == 7;
    }
}