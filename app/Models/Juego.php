<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Juego extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'liga_id',
        'equipo_local_id',
        'equipo_visitante_id',
        'fecha',
        'hora',
        'cancha_id',
        'duracion_cuarto',
        'duracion_descanso',
        'estado',
        'fase',
        'puntos_local',
        'puntos_visitante',
        'observaciones',
        'temporada_id',
        'torneo_id',
        'activo',
        'arbitro_principal_id',
        'arbitro_auxiliar_id',
        'mesa_control_id',
        'cuarto_actual',
        'estado_tiempo', 
        'tiempo_restante',
        'ultimo_cambio_tiempo',
        'en_descanso'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha' => 'date',
        'puntos_local' => 'integer',
        'puntos_visitante' => 'integer',
        'duracion_cuarto' => 'integer',
        'duracion_descanso' => 'integer',
        'en_descanso' => 'boolean'
    ];

    // Relaciones
    public function liga()
    {
        return $this->belongsTo(Liga::class);
    }
    
    public function juegoAlineaciones()
    {
        return $this->hasMany(JuegoAlineacion::class);
    }

    public function equipoLocal()
    {
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }

    public function equipoVisitante()
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }

    public function cancha()
    {
        return $this->belongsTo(Cancha::class);
    }

    public function temporada()
    {
        return $this->belongsTo(Temporada::class);
    }

    public function arbitroPrincipal()
    {
        return $this->belongsTo(Arbitro::class, 'arbitro_principal_id');
    }

    public function arbitroAuxiliar()
    {
        return $this->belongsTo(Arbitro::class, 'arbitro_auxiliar_id');
    }

    public function mesaControl()
    {
        return $this->belongsTo(Arbitro::class, 'mesa_control_id');
    }

    public function torneo()
    {
        return $this->belongsTo(Torneo::class, 'torneo_id');
    }

    // Scopes
    public function scopeEnVivo($query)
    {
        return $query->where('estado', 'En Curso')->where('activo', true);
    }

    public function scopeProgramados($query)
    {
        return $query->where('estado', 'Programado')->where('activo', true);
    }

    public function scopeFinalizados($query)
    {
        return $query->where('estado', 'Finalizado')->where('activo', true);
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha', today());
    }

    public function scopeProximaSemana($query)
    {
        return $query->whereBetween('fecha', [today(), today()->addDays(7)]);
    }

    // Atributos calculados
    public function getFechaHoraInicioAttribute()
    {
        return Carbon::parse($this->fecha->format('Y-m-d') . ' ' . $this->hora);
    }

    public function getTiempoTranscurridoAttribute()
    {
        if ($this->estado !== 'En Curso') {
            return null;
        }

        $fechaHoraInicio = $this->fecha_hora_inicio;
        $tiempoTranscurrido = now()->diffInMinutes($fechaHoraInicio);
        $duracionCuarto = $this->duracion_cuarto ?? 12;
        $duracionDescanso = $this->duracion_descanso ?? 2;

        if ($tiempoTranscurrido <= $duracionCuarto) {
            return "1er Cuarto - {$tiempoTranscurrido}'";
        } elseif ($tiempoTranscurrido <= ($duracionCuarto + $duracionDescanso)) {
            return "Descanso";
        } elseif ($tiempoTranscurrido <= ($duracionCuarto * 2 + $duracionDescanso)) {
            $minutosCuarto = $tiempoTranscurrido - $duracionCuarto - $duracionDescanso;
            return "2do Cuarto - {$minutosCuarto}'";
        } elseif ($tiempoTranscurrido <= ($duracionCuarto * 2 + $duracionDescanso * 2)) {
            return "Medio Tiempo";
        } elseif ($tiempoTranscurrido <= ($duracionCuarto * 3 + $duracionDescanso * 2)) {
            $minutosCuarto = $tiempoTranscurrido - ($duracionCuarto * 2) - ($duracionDescanso * 2);
            return "3er Cuarto - {$minutosCuarto}'";
        } elseif ($tiempoTranscurrido <= ($duracionCuarto * 3 + $duracionDescanso * 3)) {
            return "Descanso";
        } elseif ($tiempoTranscurrido <= ($duracionCuarto * 4 + $duracionDescanso * 3)) {
            $minutosCuarto = $tiempoTranscurrido - ($duracionCuarto * 3) - ($duracionDescanso * 3);
            return "4to Cuarto - {$minutosCuarto}'";
        } else {
            return "Tiempo Extra";
        }
    }

    public function getResultadoAttribute()
    {
        return ($this->puntos_local ?? 0) . ' - ' . ($this->puntos_visitante ?? 0);
    }

    public function getEstadoFormateadoAttribute()
    {
        $estados = [
            'Programado' => 'Programado',
            'En Curso' => 'En Vivo',
            'Finalizado' => 'Finalizado',
            'Cancelado' => 'Cancelado',
            'Suspendido' => 'Suspendido'
        ];

        return $estados[$this->estado] ?? $this->estado;
    }

    public function getFechaFormateadaAttribute()
    {
        return $this->fecha->format('d/m/Y') . ' - ' . $this->hora;
    }

    public function getEstadoProximidadAttribute()
    {
        $fechaHoraInicio = $this->fecha_hora_inicio;
        
        if ($fechaHoraInicio->isToday()) {
            return ['estado' => 'Hoy', 'color' => 'bg-yellow-500/20 text-yellow-100'];
        } elseif ($fechaHoraInicio->isTomorrow()) {
            return ['estado' => 'Mañana', 'color' => 'bg-green-500/20 text-green-100'];
        } elseif ($fechaHoraInicio->diffInDays(now()) <= 7) {
            return ['estado' => $fechaHoraInicio->locale('es')->dayName, 'color' => 'bg-blue-500/20 text-blue-100'];
        } else {
            return ['estado' => 'Programado', 'color' => 'bg-white/20 text-white'];
        }
    }

    public function getTiempoActual()
    {
        if ($this->estado_tiempo === 'corriendo' && $this->ultimo_cambio_tiempo) {
            $tiempoInicio = \Carbon\Carbon::parse($this->ultimo_cambio_tiempo);
            $ahora = now();
            
            // Calcular segundos transcurridos con mayor precisión
            $segundosTranscurridos = $tiempoInicio->diffInSeconds($ahora);
            
            // CORRECCIÓN: Limitar delay máximo a 2 segundos para evitar inconsistencias
            if ($segundosTranscurridos > ($this->tiempo_restante + 2)) {
                $segundosTranscurridos = $this->tiempo_restante;
            }
            
            $tiempoCalculado = $this->tiempo_restante - $segundosTranscurridos;
            
            return max(0, (int)$tiempoCalculado);
        }
        
        return (int)$this->tiempo_restante;
    }
}