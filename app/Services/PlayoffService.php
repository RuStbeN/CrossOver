<?php

namespace App\Services;

use App\Models\Torneo;
use App\Models\TorneoClasificacion;
use App\Models\Juego;
use App\Models\Equipo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PlayoffService
{
    /**
     * Genera los juegos de playoffs para un torneo
     * 
     * @param int $torneoId ID del torneo
     * @return bool True si se generaron los playoffs correctamente
     */
    public function generarPlayoffs(int $torneoId): bool
    {
        Log::info("Iniciando generación de playoffs", ['torneo_id' => $torneoId]);
        
        $torneo = Torneo::with(['clasificaciones' => function($query) {
            $query->orderBy('posicion');
        }])->find($torneoId);

        if (!$torneo) {
            Log::error("Torneo no encontrado al generar playoffs", ['torneo_id' => $torneoId]);
            return false;
        }

        // Verificar que el torneo usa playoffs y tiene equipos configurados
        if (!$torneo->usa_playoffs || !$torneo->equipos_playoffs) {
            Log::error("Torneo no configurado para playoffs", ['torneo_id' => $torneoId]);
            return false;
        }

        try {
            DB::transaction(function () use ($torneo) {
                // Obtener los equipos clasificados (ya vienen ordenados por posición)
                $equiposClasificados = $torneo->clasificaciones
                    ->take($torneo->equipos_playoffs)
                    ->load('equipo');

                if ($equiposClasificados->count() < 2) {
                    throw new \Exception("No hay suficientes equipos clasificados para playoffs");
                }

                // Generar los emparejamientos iniciales
                $this->generarEmparejamientosIniciales($torneo, $equiposClasificados);

                // Actualizar estado del torneo
                $torneo->update(['estado' => 'En Curso']);
            });

            Log::info("Playoffs generados exitosamente", ['torneo_id' => $torneoId]);
            return true;
            
        } catch (\Exception $e) {
            Log::error("Error al generar playoffs", [
                'torneo_id' => $torneoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Genera los emparejamientos iniciales de playoffs
     */
    protected function generarEmparejamientosIniciales(Torneo $torneo, $equiposClasificados): void
    {
        $numEquipos = $equiposClasificados->count();
        $numPartidos = $numEquipos / 2;
        $fechaInicioPlayoffs = now()->addDays(2); // 2 días después de hoy

        Log::debug("Generando emparejamientos iniciales", [
            'num_equipos' => $numEquipos,
            'num_partidos' => $numPartidos
        ]);

        // Crear los partidos usando el formato tradicional de playoffs
        for ($i = 0; $i < $numPartidos; $i++) {
            $local = $equiposClasificados[$i]->equipo;
            $visitante = $equiposClasificados[$numEquipos - 1 - $i]->equipo;

            $this->crearPartidoPlayoff(
                $torneo,
                $local,
                $visitante,
                $fechaInicioPlayoffs->addHours($i * 2), // Espaciar los partidos
                $this->determinarFaseInicial($numEquipos)
            );
        }
    }

    /**
     * Determina la fase inicial basada en el número de equipos
     */
    protected function determinarFaseInicial(int $numEquipos): string
    {
        return match(true) {
            $numEquipos >= 8 => 'Cuartos de Final',
            $numEquipos >= 4 => 'Semifinal',
            default => 'Final'
        };
    }

    /**
     * Crea un partido de playoff
     */
    protected function crearPartidoPlayoff(Torneo $torneo, Equipo $equipoLocal, Equipo $equipoVisitante, Carbon $fechaHora, string $fase): void
    {
        Log::debug("Creando partido de playoff", [
            'torneo_id' => $torneo->id,
            'equipo_local' => $equipoLocal->id,
            'equipo_visitante' => $equipoVisitante->id,
            'fase' => $fase
        ]);

        Juego::create([
            'torneo_id' => $torneo->id,
            'liga_id' => $torneo->liga_id,
            'equipo_local_id' => $equipoLocal->id,
            'equipo_visitante_id' => $equipoVisitante->id,
            'fecha' => $fechaHora->format('Y-m-d'),
            'hora' => $fechaHora->format('H:i:s'),
            'cancha_id' => $torneo->cancha_id,
            'duracion_cuarto' => $torneo->duracion_cuarto_minutos,
            'duracion_descanso' => 10, // 10 minutos de descanso
            'estado' => 'Programado',
            'fase' => $fase,
            'temporada_id' => $torneo->temporada_id,
            'activo' => true
        ]);
    }

    /**
     * Avanza a la siguiente ronda de playoffs
     */
    public function avanzarRondaPlayoffs(int $torneoId, string $faseActual): bool
    {
        Log::info("Avanzando ronda de playoffs", [
            'torneo_id' => $torneoId,
            'fase_actual' => $faseActual
        ]);

        $torneo = Torneo::find($torneoId);
        if (!$torneo) {
            Log::error("Torneo no encontrado al avanzar ronda de playoffs", ['torneo_id' => $torneoId]);
            return false;
        }

        try {
            DB::transaction(function () use ($torneo, $faseActual) {
                // Obtener los ganadores de la fase actual
                $ganadores = $this->obtenerGanadoresFase($torneo->id, $faseActual);
                
                // Determinar la siguiente fase
                $siguienteFase = $this->determinarSiguienteFase($faseActual, count($ganadores));
                
                // Si hay más de un ganador, crear los nuevos emparejamientos
                if (count($ganadores) > 1) {
                    $this->generarSiguienteRonda($torneo, $ganadores, $siguienteFase);
                } else if (count($ganadores) === 1) {
                    // Tenemos un campeón
                    Log::info("Torneo finalizado con campeón", [
                        'torneo_id' => $torneo->id,
                        'campeon_id' => $ganadores[0]->id
                    ]);
                    $torneo->update(['estado' => 'Finalizado']);
                }
            });

            return true;
            
        } catch (\Exception $e) {
            Log::error("Error al avanzar ronda de playoffs", [
                'torneo_id' => $torneoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Obtiene los ganadores de una fase específica
     */
    protected function obtenerGanadoresFase(int $torneoId, string $fase): array
    {
        $juegos = Juego::with(['equipoLocal', 'equipoVisitante'])
            ->where('torneo_id', $torneoId)
            ->where('fase', $fase)
            ->where('estado', 'Finalizado')
            ->get();

        $ganadores = [];
        
        foreach ($juegos as $juego) {
            $ganadores[] = ($juego->puntos_local > $juego->puntos_visitante) 
                ? $juego->equipoLocal 
                : $juego->equipoVisitante;
        }

        return $ganadores;
    }

    /**
     * Determina la siguiente fase basada en la fase actual y número de equipos
     */
    protected function determinarSiguienteFase(string $faseActual, int $numEquipos): string
    {
        return match($faseActual) {
            'Cuartos de Final' => 'Semifinal',
            'Semifinal' => 'Final',
            'Final' => 'Tercer Puesto',
            default => 'Final'
        };
    }

    /**
     * Genera los partidos para la siguiente ronda
     */
    protected function generarSiguienteRonda(Torneo $torneo, array $ganadores, string $siguienteFase): void
    {
        $numPartidos = count($ganadores) / 2;
        $fechaHora = now()->addDays(3); // 3 días después para la siguiente ronda

        for ($i = 0; $i < $numPartidos; $i++) {
            $local = $ganadores[$i * 2];
            $visitante = $ganadores[$i * 2 + 1];

            $this->crearPartidoPlayoff(
                $torneo,
                $local,
                $visitante,
                $fechaHora->addHours($i * 2),
                $siguienteFase
            );
        }
    }
}