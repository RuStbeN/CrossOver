<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cirugia extends Model
{
    use HasFactory;

    protected $fillable = [
        'expediente_medico_id',
        'tipo',
        'lugar',
        'fecha_cirugia',
        'cirujano',
        'motivo',
        'complicaciones',
        'exitosa',
        'observaciones',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'fecha_cirugia' => 'date',
        'exitosa' => 'boolean'
    ];

    // Relación
    public function expedienteMedico()
    {
        return $this->belongsTo(ExpedienteMedico::class);
    }
}