<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LesionPrevia extends Model
{
    use HasFactory;

    protected $fillable = [
        'expediente_medico_id',
        'tipo',
        'fecha_lesion',
        'tratamiento',
        'tiempo_recuperacion',
        'gravedad',
        'medico_tratante',
        'recuperacion_completa',
        'observaciones',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'fecha_lesion' => 'date',
        'recuperacion_completa' => 'boolean'
    ];

    // Relación
    public function expedienteMedico()
    {
        return $this->belongsTo(ExpedienteMedico::class);
    }
}