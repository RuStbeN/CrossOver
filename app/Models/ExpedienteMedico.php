<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpedienteMedico extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'jugador_id',
        'grupo_sanguineo',
        'alergias',
        'vacunas',
        'enfermedades_cronicas',
        'medicamentos_permanentes',
        'coordinador_autorizado',
        'restricciones_medicas',
        'constancia_medica_url',
        'fecha_expedicion',
        'medico_responsable',
        'vigencia_meses',
        'firma_responsable',
        'fecha_vencimiento',
        'version',
        'activo',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_expedicion' => 'date',
        'fecha_vencimiento' => 'date'
    ];

    // Relaciones
    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }

    public function evaluacionesFisicas()
    {
        return $this->hasMany(EvaluacionFisica::class);
    }

    public function lesionesPrevias()
    {
        return $this->hasMany(LesionPrevia::class);
    }

    public function cirugias()
    {
        return $this->hasMany(Cirugia::class);
    }

    public function historial()
    {
        return $this->hasMany(HistorialExpediente::class);
    }
}