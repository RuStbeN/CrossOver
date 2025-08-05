<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Entrenador extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'cedula_profesional',
        'fecha_nacimiento',
        'experiencia',
        'activo',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_nacimiento' => 'date'
    ];

    protected $table = 'entrenadores';

    protected $appends = ['edad'];
    
    // Accessor para calcular la edad
    public function getEdadAttribute()
    {
        if (!$this->fecha_nacimiento) {
            return null;
        }
        
        return Carbon::parse($this->fecha_nacimiento)->age;
    }

    // Relaciones
    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }
}