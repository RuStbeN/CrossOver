<?php

namespace App\Services;

use App\Models\Juego;
use App\Models\Torneo;
use App\Models\Equipo;
use App\Models\Temporada;
use Carbon\Carbon; 
use App\Models\TorneoClasificacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\PlayoffService;

class ResultadosPorPuntos
{


/**
     * Actualiza toda la clasificación después de un juego
     */
    public function procesarResultadoJuego(Juego $juego): void
    {
        Log::info("Iniciando procesamiento de resultado para juego ID: {$juego->id}");
    
        // Verificar que el juego pertenezca a un torneo
        if (!$juego->torneo_id) {
            Log::warning("El juego no pertenece a ningún torneo, no se procesará", [
                'juego_id' => $juego->id
            ]);
            return;
        }

        // Cargar la relación torneo si no está cargada
        if (!$juego->relationLoaded('torneo')) {
            $juego->load('torneo');
        }

        // Verificar que el torneo es por puntos
        if ($juego->torneo->tipo !== 'por_puntos') {
            Log::warning("El torneo no es por puntos, no se procesará", [
                'juego_id' => $juego->id,
                'torneo_id' => $juego->torneo_id,
                'tipo_torneo' => $juego->torneo->tipo
            ]);
            return;
        }

        Log::debug("Configuración de puntos del torneo", [
            'puntos_victoria' => $juego->torneo->puntos_por_victoria,
            'puntos_empate' => $juego->torneo->puntos_por_empate,
            'puntos_derrota' => $juego->torneo->puntos_por_derrota
        ]);

        // Determinar resultado del juego
        $resultadoLocal = $this->determinarResultado(
            $juego->puntos_local,
            $juego->puntos_visitante,
            $juego->torneo->puntos_por_victoria,
            $juego->torneo->puntos_por_empate,
            $juego->torneo->puntos_por_derrota
        );

        $resultadoVisitante = $this->determinarResultado(
            $juego->puntos_visitante,
            $juego->puntos_local,
            $juego->torneo->puntos_por_victoria,
            $juego->torneo->puntos_por_empate,
            $juego->torneo->puntos_por_derrota
        );

        Log::debug("Resultados determinados", [
            'local' => $resultadoLocal,
            'visitante' => $resultadoVisitante
        ]);

        // Actualizar ambos equipos
        try {
            DB::transaction(function () use ($juego, $resultadoLocal, $resultadoVisitante) {
                Log::info("Actualizando estadísticas para equipo local", [
                    'equipo_id' => $juego->equipo_local_id
                ]);
                
                $this->actualizarEquipo(
                    $juego->torneo_id,
                    $juego->equipo_local_id,
                    $juego->puntos_local,
                    $juego->puntos_visitante,
                    $resultadoLocal['puntos'],
                    $resultadoLocal['es_victoria'],
                    $resultadoLocal['es_empate']
                );

                Log::info("Actualizando estadísticas para equipo visitante", [
                    'equipo_id' => $juego->equipo_visitante_id
                ]);
                
                $this->actualizarEquipo(
                    $juego->torneo_id,
                    $juego->equipo_visitante_id,
                    $juego->puntos_visitante,
                    $juego->puntos_local,
                    $resultadoVisitante['puntos'],
                    $resultadoVisitante['es_victoria'],
                    $resultadoVisitante['es_empate']
                );

                Log::info("Recalculando posiciones para torneo", [
                    'torneo_id' => $juego->torneo_id
                ]);
                
                $this->recalcularPosiciones($juego->torneo_id);
            });

            Log::info("Procesamiento de juego completado exitosamente", [
                'juego_id' => $juego->id
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error al procesar el juego", [
                'juego_id' => $juego->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Determina el resultado para un equipo
     */
    private function determinarResultado(
        int $puntosEquipo,
        int $puntosRival,
        int $puntosVictoria,
        int $puntosEmpate,
        int $puntosDerrota
    ): array {
        $esVictoria = $puntosEquipo > $puntosRival;
        $esEmpate = $puntosEquipo == $puntosRival;

        $resultado = [
            'es_victoria' => $esVictoria,
            'es_empate' => $esEmpate,
            'puntos' => $esVictoria ? $puntosVictoria : ($esEmpate ? $puntosEmpate : $puntosDerrota)
        ];

        Log::debug("Resultado determinado", [
            'puntos_equipo' => $puntosEquipo,
            'puntos_rival' => $puntosRival,
            'resultado' => $resultado
        ]);

        return $resultado;
    }

    /**
     * Actualiza las estadísticas de un equipo en la clasificación
     */
    private function actualizarEquipo(
        int $torneoId,
        int $equipoId,
        int $puntosFavor,
        int $puntosContra,
        int $puntosObtenidos,
        bool $esVictoria,
        bool $esEmpate
    ): void {
        Log::debug("Actualizando equipo en clasificación", [
            'torneo_id' => $torneoId,
            'equipo_id' => $equipoId,
            'puntos_favor' => $puntosFavor,
            'puntos_contra' => $puntosContra,
            'puntos_obtenidos' => $puntosObtenidos
        ]);

        TorneoClasificacion::updateOrCreate(
            ['torneo_id' => $torneoId, 'equipo_id' => $equipoId],
            [
                'partidos_jugados' => DB::raw('partidos_jugados + 1'),
                'partidos_ganados' => DB::raw($esVictoria ? 'partidos_ganados + 1' : 'partidos_ganados'),
                'partidos_empatados' => DB::raw($esEmpate ? 'partidos_empatados + 1' : 'partidos_empatados'),
                'partidos_perdidos' => DB::raw((!$esVictoria && !$esEmpate) ? 'partidos_perdidos + 1' : 'partidos_perdidos'),
                'puntos_totales' => DB::raw("puntos_totales + $puntosObtenidos"),
                'puntos_favor' => DB::raw("puntos_favor + $puntosFavor"),
                'puntos_contra' => DB::raw("puntos_contra + $puntosContra"),
                'diferencia_puntos' => DB::raw("puntos_favor - puntos_contra")
            ]
        );

        Log::info("Estadísticas actualizadas para equipo", [
            'torneo_id' => $torneoId,
            'equipo_id' => $equipoId
        ]);
    }

    /**
     * Recalcula todas las posiciones en el torneo
     */
    public function recalcularPosiciones(int $torneoId): void
    {
        Log::info("Recalculando posiciones para torneo", ['torneo_id' => $torneoId]);
        
        $clasificaciones = TorneoClasificacion::where('torneo_id', $torneoId)
            ->orderBy('puntos_totales', 'DESC')
            ->orderBy('diferencia_puntos', 'DESC')
            ->orderBy('puntos_favor', 'DESC')
            ->get();

        Log::debug("Clasificaciones obtenidas para recalculo", [
            'count' => $clasificaciones->count()
        ]);

        try {
            DB::transaction(function () use ($clasificaciones, $torneoId) {
                // 1. Recalcular posiciones
                $posicion = 1;
                foreach ($clasificaciones as $clasificacion) {
                    $clasificacion->update(['posicion' => $posicion++]);
                }
                
                // 2. Validación básica de playoffs
                $torneo = Torneo::find($torneoId);
                if ($torneo && $torneo->usa_playoffs) {
                    // Solo llamar a la función de validación si el torneo usa playoffs
                    $this->validarTorneoParaPlayoffs($torneoId);
                }
            });
            
            Log::info("Posiciones recalculadas exitosamente", [
                'torneo_id' => $torneoId,
                'equipos_actualizados' => $clasificaciones->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error al recalcular posiciones", [
                'torneo_id' => $torneoId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Valida si el torneo está listo para generar playoffs o finalizar
     * 
     * @param int $torneoId ID del torneo a validar
     */
    public function validarTorneoParaPlayoffs(int $torneoId): void
    {
        Log::info("Iniciando validación para playoffs", ['torneo_id' => $torneoId]);
        
        // 1. Obtener información básica del torneo
        $torneo = Torneo::find($torneoId);
        if (!$torneo) {
            Log::error("Torneo no encontrado al validar playoffs", ['torneo_id' => $torneoId]);
            return;
        }

        // 2. Determinar el estado actual del torneo
        $estadoTorneo = $this->determinarEstadoTorneo($torneoId);
        
        switch ($estadoTorneo) {
            case 'fase_regular_completa':
                $this->procesarFaseRegularCompleta($torneoId);
                break;
                
            case 'playoffs_completos':
                $this->finalizarTorneo($torneoId);
                break;
                
            case 'en_progreso':
                Log::info("Torneo aún en progreso", ['torneo_id' => $torneoId]);
                break;
                
            default:
                Log::warning("Estado de torneo desconocido", [
                    'torneo_id' => $torneoId,
                    'estado' => $estadoTorneo
                ]);
        }
    }

    /**
     * Determina en qué estado se encuentra el torneo
     */
    private function determinarEstadoTorneo(int $torneoId): string
    {
        // Verificar si hay juegos pendientes
        $juegosPendientes = Juego::where('torneo_id', $torneoId)
            ->where('estado', '!=', 'Finalizado')
            ->count();
        
        if ($juegosPendientes > 0) {
            return 'en_progreso';
        }
        
        // Verificar si ya existen playoffs
        $hayPlayoffs = Juego::where('torneo_id', $torneoId)
            ->where('fase', '!=', 'Fase Regular')
            ->exists();
        
        if ($hayPlayoffs) {
            // Si hay playoffs, verificar si están completos
            $playoffsPendientes = Juego::where('torneo_id', $torneoId)
                ->where('fase', '!=', 'Fase Regular')
                ->where('estado', '!=', 'Finalizado')
                ->count();
                
            return $playoffsPendientes === 0 ? 'playoffs_completos' : 'en_progreso';
        }
        
        // No hay playoffs, verificar si la fase regular está completa
        return $this->validarFaseRegularCompleta($torneoId) ? 'fase_regular_completa' : 'en_progreso';
    }

    /**
     * Valida si la fase regular está completa
     */
    private function validarFaseRegularCompleta(int $torneoId): bool
    {
        // Solo validar partidos de fase regular
        $distribucionJuegos = TorneoClasificacion::where('torneo_id', $torneoId)
            ->selectRaw('partidos_jugados, COUNT(*) as equipos')
            ->groupBy('partidos_jugados')
            ->get();
        
        // Verificar que todos los equipos hayan jugado el mismo número de partidos EN FASE REGULAR
        if ($distribucionJuegos->count() > 1) {
            // Obtener el número esperado de partidos en fase regular
            $partidosEsperados = $this->calcularPartidosEsperadosFaseRegular($torneoId);
            
            // Verificar si algún equipo tiene exactamente los partidos esperados
            $equiposConPartidosCorrectos = TorneoClasificacion::where('torneo_id', $torneoId)
                ->where('partidos_jugados', $partidosEsperados)
                ->count();
                
            $totalEquipos = TorneoClasificacion::where('torneo_id', $torneoId)->count();
            
            // Si todos los equipos tienen los partidos esperados de fase regular, está completa
            return $equiposConPartidosCorrectos === $totalEquipos;
        }
        
        // Si todos tienen el mismo número, verificar si corresponde a la fase regular completa
        $partidosJugados = $distribucionJuegos->first()->partidos_jugados;
        $partidosEsperados = $this->calcularPartidosEsperadosFaseRegular($torneoId);
        
        return $partidosJugados >= $partidosEsperados;
    }

    /**
     * Calcula cuántos partidos debe jugar cada equipo en fase regular
     */
    private function calcularPartidosEsperadosFaseRegular(int $torneoId): int
    {
        $torneo = Torneo::find($torneoId);
        $totalEquipos = TorneoClasificacion::where('torneo_id', $torneoId)->count();
        
        // Para round-robin (todos contra todos)
        if ($torneo->tipo_torneo === 'round_robin' || $torneo->tipo_torneo === 'puntos') {
            return $totalEquipos - 1; // Cada equipo juega contra todos los demás una vez
        }
        
        // Para otros tipos de torneo, puedes ajustar según tus reglas
        return $totalEquipos - 1; // Default
    }

    /**
     * Procesa cuando la fase regular está completa
     */
    private function procesarFaseRegularCompleta(int $torneoId): void
    {
        $torneo = Torneo::find($torneoId);
        
        // Validar que hay suficientes equipos para playoffs
        $equiposDisponibles = TorneoClasificacion::where('torneo_id', $torneoId)->count();
        $equiposRequeridos = $torneo->equipos_playoffs ?? 0;
        
        if ($equiposRequeridos <= 0 || $equiposDisponibles < $equiposRequeridos) {
            Log::info("No se requieren playoffs o no hay suficientes equipos", [
                'torneo_id' => $torneoId,
                'equipos_disponibles' => $equiposDisponibles,
                'equipos_requeridos' => $equiposRequeridos
            ]);
            
            // Finalizar torneo directamente
            $this->finalizarTorneo($torneoId);
            return;
        }

        // Generar playoffs
        try {
            Log::info("Fase regular completa, generando playoffs", ['torneo_id' => $torneoId]);
            
            $playoffService = app(PlayoffService::class);
            $success = $playoffService->generarPlayoffs($torneoId);
            
            if (!$success) {
                throw new \Exception("No se pudo generar los playoffs");
            }
            
            Log::info("Playoffs generados exitosamente", ['torneo_id' => $torneoId]);
            
        } catch (\Exception $e) {
            Log::error("Error al generar playoffs", [
                'torneo_id' => $torneoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Finaliza el torneo cuando todo está completo
     */
    private function finalizarTorneo(int $torneoId): void
    {
        try {
            $torneo = Torneo::find($torneoId);
            $torneo->estado = 'Finalizado';
            $torneo->save();
            
            Log::info("Torneo finalizado exitosamente", ['torneo_id' => $torneoId]);
            
            // Aquí puedes agregar cualquier lógica adicional para el cierre del torneo
            // como enviar notificaciones, generar reportes finales, etc.
            
        } catch (\Exception $e) {
            Log::error("Error al finalizar torneo", [
                'torneo_id' => $torneoId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Método auxiliar para debugging - muestra el estado actual del torneo
     */
    private function logEstadoTorneo(int $torneoId): void
    {
        $juegosFaseRegular = Juego::where('torneo_id', $torneoId)
            ->where('fase', 'Fase Regular')
            ->count();
            
        $juegosPlayoffs = Juego::where('torneo_id', $torneoId)
            ->where('fase', '!=', 'Fase Regular')
            ->count();
            
        $juegosPendientes = Juego::where('torneo_id', $torneoId)
            ->where('estado', '!=', 'Finalizado')
            ->count();
            
        $equipos = TorneoClasificacion::where('torneo_id', $torneoId)
            ->selectRaw('equipo_id, partidos_jugados')
            ->get();
        
        Log::info("Estado actual del torneo", [
            'torneo_id' => $torneoId,
            'juegos_fase_regular' => $juegosFaseRegular,
            'juegos_playoffs' => $juegosPlayoffs,
            'juegos_pendientes' => $juegosPendientes,
            'equipos_partidos' => $equipos->toArray()
        ]);
    }
    
    /**
     * Obtiene la clasificación actual del torneo
     */
    public function obtenerClasificacion(int $torneoId, bool $incluirValidacion = false)
    {
        Log::debug("Obteniendo clasificación para torneo", ['torneo_id' => $torneoId]);
        
        // Obtener la clasificación normal
        $clasificacion = TorneoClasificacion::with('equipo')
            ->where('torneo_id', $torneoId)
            ->orderBy('posicion')
            ->get()
            ->map(function ($item) {
                return [
                    'posicion' => $item->posicion,
                    'equipo' => $item->equipo->nombre,
                    'logo' => $item->equipo->logo_url,
                    'pj' => $item->partidos_jugados,
                    'pg' => $item->partidos_ganados,
                    'pe' => $item->partidos_empatados,
                    'pp' => $item->partidos_perdidos,
                    'gf' => $item->puntos_favor,
                    'gc' => $item->puntos_contra,
                    'dg' => $item->diferencia_puntos,
                    'pts' => $item->puntos_totales
                ];
            });

        Log::info("Clasificación obtenida", [
            'torneo_id' => $torneoId,
            'equipos_en_clasificacion' => $clasificacion->count()
        ]);

        // Si se solicita incluir la validación
        if ($incluirValidacion) {
            $validacion = $this->validarEstadoTorneo($torneoId);
            
            return [
                'clasificacion' => $clasificacion,
                'validacion' => $validacion,
                'torneo_id' => $torneoId
            ];
        }

        return $clasificacion;
    }

}