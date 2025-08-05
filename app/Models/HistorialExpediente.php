<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialExpediente extends Model
{
    use HasFactory;

    protected $fillable = [
        'expediente_medico_id',
        'accion',
        'cambios',
        'datos_anteriores',
        'datos_nuevos',
        'usuario',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array'
    ];

    // Relación
    public function expedienteMedico()
    {
        return $this->belongsTo(ExpedienteMedico::class);
    }
}