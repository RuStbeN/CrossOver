<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionFisica extends Model
{
    use HasFactory;

    protected $fillable = [
        'expediente_medico_id',
        'peso',
        'talla',
        'grasa_corporal',
        'imc',
        'frecuencia_cardiaca_reposo',
        'presion_arterial_sistolica',
        'presion_arterial_diastolica',
        'observaciones',
        'fecha_evaluacion',
        'evaluador',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'peso' => 'decimal:2',
        'talla' => 'decimal:2',
        'grasa_corporal' => 'decimal:1',
        'imc' => 'decimal:1',
        'fecha_evaluacion' => 'date'
    ];

    // Relación
    public function expedienteMedico()
    {
        return $this->belongsTo(ExpedienteMedico::class);
    }
}