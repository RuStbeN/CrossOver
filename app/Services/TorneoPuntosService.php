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

class TorneoPuntosService
{
    /**
     * Configuración completa del torneo por puntos con validaciones exhaustivas
     */
    public function configurarTorneo(Torneo $torneo)
    {
        Log::info('=== INICIANDO CONFIGURACIÓN DE TORNEO POR PUNTOS ===', [
            'torneo_id' => $torneo->id,
            'torneo_nombre' => $torneo->nombre,
            'tipo' => $torneo->tipo,
            'fecha_inicio' => $torneo->fecha_inicio,
            'fecha_fin' => $torneo->fecha_fin,
            'liga_id' => $torneo->liga_id,
            'categoria_id' => $torneo->categoria_id,
            'temporada_id' => $torneo->temporada_id,
            'cancha_id' => $torneo->cancha_id,
            'duracion_cuarto_minutos' => $torneo->duracion_cuarto_minutos,
            'tiempo_entre_partidos_minutos' => $torneo->tiempo_entre_partidos_minutos
        ]);
        
        try {
            // 1. Validaciones iniciales críticas
            $this->validarConfiguracionTorneo($torneo);
            
            // 2. Obtener y validar temporada
            $temporada = $this->obtenerYValidarTemporada($torneo);
            
            // 3. Obtener equipos elegibles
            $equipos = $this->obtenerEquiposElegibles($torneo);
            
            // 4. Validar cantidad mínima de equipos
            if ($equipos->count() < 2) {
                throw new \Exception('Se necesitan al menos 2 equipos para crear un torneo');
            }
            
            // 5. Calcular juegos necesarios y validar capacidad
            $totalJuegosNecesarios = $this->calcularTotalJuegos($equipos->count());
            Log::info('Juegos necesarios calculados', [
                'total_equipos' => $equipos->count(),
                'total_juegos_necesarios' => $totalJuegosNecesarios
            ]);
            
            // 6. Validar disponibilidad de días hábiles
            $diasDisponibles = $this->obtenerYValidarDiasHabiles($torneo, $temporada);
            
            // 7. Validar capacidad total del torneo
            $this->validarCapacidadCompleta($torneo, $temporada, $totalJuegosNecesarios, $diasDisponibles);
            
            // 8. Asignar equipos al torneo
            $this->asignarEquiposAlTorneo($torneo, $equipos);

            // 9. Registrar clasificación inicial
            $this->registrarClasificacionEquipos($torneo->id, $equipos);
            
            // 10. Crear juegos todos contra todos
            $juegosCreados = $this->crearJuegosTodosContraTodos($torneo, $equipos, $temporada, $diasDisponibles);
            
            Log::info('=== CONFIGURACIÓN DE TORNEO COMPLETADA EXITOSAMENTE ===', [
                'torneo_id' => $torneo->id,
                'equipos_asignados' => $equipos->count(),
                'juegos_creados' => $juegosCreados,
                'dias_utilizados' => count($diasDisponibles)
            ]);
            
            return [
                'success' => true,
                'equipos_asignados' => $equipos->count(),
                'juegos_creados' => $juegosCreados,
                'dias_utilizados' => count($diasDisponibles)
            ];
            
        } catch (\Exception $e) {
            Log::error('=== ERROR EN CONFIGURACIÓN DE TORNEO ===', [
                'torneo_id' => $torneo->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Validaciones iniciales del torneo
     */
    private function validarConfiguracionTorneo(Torneo $torneo): void
    {
        Log::info('Validando configuración inicial del torneo');
        
        $errores = [];
        
        // Validar que tenga temporada
        if (!$torneo->temporada_id) {
            $errores[] = 'El torneo debe tener una temporada asignada';
        }
        
        // Validar que tenga liga
        if (!$torneo->liga_id) {
            $errores[] = 'El torneo debe tener una liga asignada';
        }
        
        // Agregar esto en el método validarConfiguracionTorneo()
        $canchasAsignadas = DB::table('torneo_cancha')->where('torneo_id', $torneo->id)->count();
        if ($canchasAsignadas === 0) {
            $errores[] = 'El torneo debe tener al menos una cancha asignada';
        }
                
        // Validar duración del cuarto
        if (!$torneo->duracion_cuarto_minutos || $torneo->duracion_cuarto_minutos <= 0) {
            $errores[] = 'La duración del cuarto debe ser mayor a 0 minutos';
        }
        
        // Validar tiempo entre partidos
        if (!isset($torneo->tiempo_entre_partidos_minutos) || $torneo->tiempo_entre_partidos_minutos < 0) {
            $errores[] = 'El tiempo entre partidos debe ser 0 o mayor';
        }
        
        // Validar fecha de inicio
        if (!$torneo->fecha_inicio) {
            $errores[] = 'El torneo debe tener una fecha de inicio';
        }
        
        if (!empty($errores)) {
            Log::error('Errores de validación en configuración del torneo', [
                'errores' => $errores
            ]);
            throw new \Exception('Errores de configuración: ' . implode(', ', $errores));
        }
        
        Log::info('Configuración inicial del torneo validada correctamente');
    }

    /**
     * Obtiene y valida la temporada del torneo
     */
    private function obtenerYValidarTemporada(Torneo $torneo): Temporada
    {
        Log::info('Obteniendo y validando temporada', [
            'temporada_id' => $torneo->temporada_id
        ]);
        
        $temporada = Temporada::find($torneo->temporada_id);
        
        if (!$temporada) {
            throw new \Exception("No se encontró la temporada con ID: {$torneo->temporada_id}");
        }
        
        if (!$temporada->activo) {
            throw new \Exception("La temporada '{$temporada->nombre}' no está activa");
        }
        
        // Validar fechas de la temporada
        $fechaInicioTemporada = Carbon::parse($temporada->fecha_inicio);
        $fechaFinTemporada = Carbon::parse($temporada->fecha_fin);
        $fechaInicioTorneo = Carbon::parse($torneo->fecha_inicio);
        
        if ($fechaInicioTorneo->lt($fechaInicioTemporada)) {
            throw new \Exception("La fecha de inicio del torneo ({$fechaInicioTorneo->format('Y-m-d')}) no puede ser anterior al inicio de la temporada ({$fechaInicioTemporada->format('Y-m-d')})");
        }
        
        // Si el torneo tiene fecha fin, validarla
        if ($torneo->fecha_fin) {
            $fechaFinTorneo = Carbon::parse($torneo->fecha_fin);
            if ($fechaFinTorneo->gt($fechaFinTemporada)) {
                throw new \Exception("La fecha de fin del torneo ({$fechaFinTorneo->format('Y-m-d')}) no puede ser posterior al fin de la temporada ({$fechaFinTemporada->format('Y-m-d')})");
            }
        }
        
        // Validar horarios de la temporada
        if (!$temporada->horario_inicio || !$temporada->horario_fin) {
            throw new \Exception("La temporada debe tener horarios de inicio y fin configurados");
        }
        
        $horarioInicio = Carbon::parse($temporada->horario_inicio);
        $horarioFin = Carbon::parse($temporada->horario_fin);
        
        if ($horarioInicio->gte($horarioFin)) {
            throw new \Exception("El horario de inicio de la temporada debe ser anterior al horario de fin");
        }
        
        Log::info('Temporada validada correctamente', [
            'temporada_id' => $temporada->id,
            'temporada_nombre' => $temporada->nombre,
            'fecha_inicio' => $temporada->fecha_inicio,
            'fecha_fin' => $temporada->fecha_fin,
            'horario_inicio' => $temporada->horario_inicio,
            'horario_fin' => $temporada->horario_fin
        ]);
        
        return $temporada;
    }

    /**
     * Obtiene equipos elegibles para el torneo con validaciones
     */
    private function obtenerEquiposElegibles(Torneo $torneo)
    {
        Log::info('Obteniendo equipos elegibles para torneo por puntos', [
            'liga_id' => $torneo->liga_id,
            'categoria_id' => $torneo->categoria_id
        ]);

        // Buscar por liga y categoría
        $equipos = Equipo::where('liga_id', $torneo->liga_id)
                    ->where('categoria_id', $torneo->categoria_id)
                    ->where('activo', true)
                    ->get();

        // Fallback: buscar solo por liga si no hay categoría específica
        if ($equipos->isEmpty() && $torneo->categoria_id) {
            Log::warning('No se encontraron equipos con liga y categoría específica, buscando solo por liga');
            
            $equipos = Equipo::where('liga_id', $torneo->liga_id)
                            ->where('activo', true)
                            ->get();
        }
        
        // Validar que encontramos equipos
        if ($equipos->isEmpty()) {
            throw new \Exception("No se encontraron equipos activos para la liga ID: {$torneo->liga_id}" . 
                ($torneo->categoria_id ? " y categoría ID: {$torneo->categoria_id}" : ""));
        }

        Log::info('Equipos encontrados para torneo por puntos', [
            'total' => $equipos->count(),
            'equipos' => $equipos->pluck('nombre', 'id')->toArray()
        ]);

        return $equipos;
    }

    /**
     * Calcula el total de juegos necesarios para un torneo todos contra todos
     */
    private function calcularTotalJuegos(int $numEquipos): int
    {
        // Fórmula para todos contra todos: n * (n-1) / 2
        $totalJuegos = ($numEquipos * ($numEquipos - 1)) / 2;
        
        Log::info('Total de juegos calculado', [
            'num_equipos' => $numEquipos,
            'total_juegos' => $totalJuegos,
            'formula' => 'n * (n-1) / 2'
        ]);
        
        return (int) $totalJuegos;
    }

    /**
     * Obtiene y valida los días hábiles del torneo
     */
    private function obtenerYValidarDiasHabiles(Torneo $torneo, Temporada $temporada): array
    {
        Log::info('Obteniendo días hábiles para el torneo');
        
        // Obtener días hábiles de la temporada
        $diasHabiles = $this->obtenerDiasHabiles($temporada->id);
        
        if ($diasHabiles->isEmpty()) {
            throw new \Exception("No se encontraron días hábiles configurados para la temporada '{$temporada->nombre}'");
        }
        
        // Obtener días disponibles en el rango del torneo
        $diasDisponibles = $this->obtenerDiasHabilesEnRango($torneo, $temporada);
        
        if (empty($diasDisponibles)) {
            $fechaInicio = Carbon::parse($torneo->fecha_inicio)->format('Y-m-d');
            $fechaFin = Carbon::parse($torneo->fecha_fin ?? $temporada->fecha_fin)->format('Y-m-d');
            throw new \Exception("No hay días hábiles disponibles en el rango del torneo ({$fechaInicio} a {$fechaFin})");
        }
        
        // Validar horarios de cada día hábil
        foreach ($diasDisponibles as $dia) {
            $this->validarHorarioDia($dia, $temporada);
        }
        
        Log::info('Días hábiles validados correctamente', [
            'total_dias_disponibles' => count($diasDisponibles),
            'primer_dia' => $diasDisponibles[0]['fecha']->format('Y-m-d'),
            'ultimo_dia' => end($diasDisponibles)['fecha']->format('Y-m-d')
        ]);
        
        return $diasDisponibles;
    }

    /**
     * Valida el horario de un día específico
     */
    private function validarHorarioDia(array $dia, Temporada $temporada): void
    {
        $horario = $this->obtenerHorarioDelDia($dia['fecha'], $dia['diasHabiles'], $temporada);
        
        // Validar que el horario tenga sentido
        if ($horario['inicio']->gte($horario['fin'])) {
            throw new \Exception("Horario inválido para el día {$dia['fecha']->format('Y-m-d')}: inicio ({$horario['inicio']->format('H:i')}) >= fin ({$horario['fin']->format('H:i')})");
        }
        
        // Validar duración mínima del día
        $duracionDia = $horario['inicio']->diffInMinutes($horario['fin']);
        if ($duracionDia < 60) { // Mínimo 1 hora
            throw new \Exception("El día {$dia['fecha']->format('Y-m-d')} tiene muy poco tiempo disponible: {$duracionDia} minutos");
        }
    }

    /**
     * Validación completa de capacidad del torneo
     */
    private function validarCapacidadCompleta(Torneo $torneo, Temporada $temporada, int $juegosNecesarios, array $diasDisponibles): void
    {
        Log::info('Validando capacidad completa del torneo', [
            'juegos_necesarios' => $juegosNecesarios,
            'dias_disponibles' => count($diasDisponibles)
        ]);
        
        $capacidadTotal = 0;
        $detalleCapacidad = [];
        $duracionJuegoCompleto = $this->calcularDuracionTotalJuego($torneo) + $torneo->tiempo_entre_partidos_minutos;
        
        foreach ($diasDisponibles as $dia) {
            $horario = $this->obtenerHorarioDelDia($dia['fecha'], $dia['diasHabiles'], $temporada);
            $duracionDia = $horario['inicio']->diffInMinutes($horario['fin']);
            $capacidadDia = floor($duracionDia / $duracionJuegoCompleto);
            
            $detalleCapacidad[] = [
                'fecha' => $dia['fecha']->format('Y-m-d (D)'),
                'horario_inicio' => $horario['inicio']->format('H:i'),
                'horario_fin' => $horario['fin']->format('H:i'),
                'duracion_disponible' => $duracionDia,
                'capacidad_juegos' => $capacidadDia
            ];
            
            $capacidadTotal += $capacidadDia;
        }
        
        Log::info('Detalle de capacidad por día', [
            'detalle' => $detalleCapacidad,
            'capacidad_total' => $capacidadTotal,
            'duracion_juego_completo' => $duracionJuegoCompleto
        ]);
        
        if ($juegosNecesarios > $capacidadTotal) {
            throw new \Exception(sprintf(
                'Capacidad insuficiente para el torneo. Se necesitan %d juegos pero solo hay capacidad para %d juegos en %d días disponibles. Considera: 1) Extender fechas del torneo, 2) Reducir duración de juegos, 3) Reducir tiempo entre partidos, 4) Agregar más días hábiles.',
                $juegosNecesarios,
                $capacidadTotal,
                count($diasDisponibles)
            ));
        }
        
        // Advertencia si la capacidad es muy justa
        $porcentajeUso = ($juegosNecesarios / $capacidadTotal) * 100;
        if ($porcentajeUso > 90) {
            Log::warning('Capacidad muy ajustada para el torneo', [
                'porcentaje_uso' => round($porcentajeUso, 2),
                'recomendacion' => 'Considera agregar más días o extender horarios para mayor flexibilidad'
            ]);
        }
        
        Log::info('Capacidad del torneo validada exitosamente', [
            'juegos_necesarios' => $juegosNecesarios,
            'capacidad_total' => $capacidadTotal,
            'porcentaje_uso' => round($porcentajeUso, 2)
        ]);
    }

    /**
     * Asigna equipos al torneo en la tabla pivote
     */
    private function asignarEquiposAlTorneo(Torneo $torneo, $equipos)
    {
        Log::info('Asignando equipos al torneo');
        
        $equiposData = [];
        
        foreach ($equipos as $equipo) {
            $equiposData[] = [
                'torneo_id' => $torneo->id,
                'equipo_id' => $equipo->id,
                'grupo' => null, // No usamos grupos en torneo por puntos
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        // Eliminar asignaciones previas si existen
        DB::table('torneo_equipo')->where('torneo_id', $torneo->id)->delete();
        
        // Insertar nuevas asignaciones
        DB::table('torneo_equipo')->insert($equiposData);
        
        Log::info('Equipos asignados al torneo exitosamente', [
            'torneo_id' => $torneo->id,
            'equipos_asignados' => count($equiposData),
            'equipos_ids' => $equipos->pluck('id')->toArray()
        ]);
    }

    /**
     * Registra la clasificación inicial de los equipos
     */
    public function registrarClasificacionEquipos($torneoId, $equipos)
    {
        Log::info('Registrando clasificación inicial de equipos', [
            'torneo_id' => $torneoId,
            'total_equipos' => $equipos->count()
        ]);
        
        // Eliminar clasificaciones previas si existen
        TorneoClasificacion::where('torneo_id', $torneoId)->delete();
        
        $clasificacionesData = [];
        foreach ($equipos as $equipo) {
            $clasificacionesData[] = [
                'torneo_id' => $torneoId,
                'equipo_id' => $equipo->id,
                'partidos_jugados' => 0,
                'partidos_ganados' => 0,
                'partidos_empatados' => 0,
                'partidos_perdidos' => 0,
                'puntos_totales' => 0,
                'puntos_favor' => 0,
                'puntos_contra' => 0,
                'diferencia_puntos' => 0,
                'posicion' => null,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        TorneoClasificacion::insert($clasificacionesData);
        
        Log::info('Clasificaciones iniciales registradas exitosamente', [
            'total_registros' => count($clasificacionesData)
        ]);
    }

    /**
     * Obtiene todos los días hábiles dentro del rango del torneo
     */
    private function obtenerDiasHabilesEnRango(Torneo $torneo, Temporada $temporada): array
    {
        Log::info('Obteniendo días hábiles en rango del torneo');
        
        $diasHabiles = $this->obtenerDiasHabiles($temporada->id);
        $fechaInicio = Carbon::parse($torneo->fecha_inicio);
        $fechaFin = Carbon::parse($torneo->fecha_fin ?? $temporada->fecha_fin);

        Log::info('Rango de fechas para búsqueda', [
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
            'dias_habiles_configurados' => $diasHabiles->keys()->toArray()
        ]);

        // Encontrar el primer día hábil a partir de la fecha de inicio
        $primerDiaHabil = $this->encontrarSiguienteDiaHabil($fechaInicio->copy(), $diasHabiles);
        if (!$primerDiaHabil) {
            throw new \Exception("No se pudo encontrar un primer día hábil válido desde {$fechaInicio->format('Y-m-d')}");
        }

        $diasDisponibles = [];
        $fechaActual = $primerDiaHabil->copy();

        while ($fechaActual->lte($fechaFin)) {
            $diaSemana = $fechaActual->dayOfWeekIso;

            if ($diasHabiles->has($diaSemana)) {
                $diasDisponibles[] = [
                    'fecha' => $fechaActual->copy(),
                    'dia_semana' => $diaSemana,
                    'diasHabiles' => $diasHabiles
                ];
            }

            $fechaActual->addDay();
        }

        Log::info('Días hábiles encontrados en rango', [
            'fecha_inicio_busqueda' => $fechaInicio->format('Y-m-d'),
            'fecha_fin_busqueda' => $fechaFin->format('Y-m-d'),
            'primer_dia_habil' => $primerDiaHabil->format('Y-m-d'),
            'total_dias_disponibles' => count($diasDisponibles),
            'detalle_dias' => array_map(function($d) {
                return [
                    'fecha' => $d['fecha']->format('Y-m-d'),
                    'dia_nombre' => $d['fecha']->locale('es')->dayName,
                    'dia_semana' => $d['dia_semana']
                ];
            }, $diasDisponibles)
        ]);

        return $diasDisponibles;
    }

    /**
     * Genera emparejamientos todos contra todos con orden aleatorio
     */
    private function generarEmparejamientos($equipos): array
    {
        Log::info('Generando emparejamientos todos contra todos');
        
        $equiposArray = $equipos->toArray();
        $emparejamientos = [];
        
        // Generar todos los posibles emparejamientos
        for ($i = 0; $i < count($equiposArray); $i++) {
            for ($j = $i + 1; $j < count($equiposArray); $j++) {
                $emparejamientos[] = [
                    'local' => $equiposArray[$i]['id'],
                    'visitante' => $equiposArray[$j]['id'],
                    'equipo_local_nombre' => $equiposArray[$i]['nombre'],
                    'equipo_visitante_nombre' => $equiposArray[$j]['nombre'],
                    'equipo_local' => $equiposArray[$i],
                    'equipo_visitante' => $equiposArray[$j]
                ];
            }
        }
        
        Log::info('Emparejamientos generados', [
            'total_emparejamientos' => count($emparejamientos),
            'primer_emparejamiento' => $emparejamientos[0] ?? null
        ]);
        
        // Balancear emparejamientos
        $emparejamientos = $this->balancearEmparejamientos($emparejamientos, $equiposArray);
        
        return $emparejamientos;
    }

    /**
     * Balancea los emparejamientos usando algoritmo Round Robin modificado
     */
    private function balancearEmparejamientos(array $emparejamientos, array $equipos): array
    {
        Log::info('Balanceando emparejamientos');
        
        // Implementación de algoritmo Round Robin modificado
        $numEquipos = count($equipos);
        $equiposIds = array_column($equipos, 'id');
        $equiposIndex = array_flip($equiposIds);
        
        // 1. Ordenar emparejamientos para alternar local/visitante
        usort($emparejamientos, function($a, $b) use ($equiposIndex) {
            // Priorizar equipos que han sido menos locales
            $balanceA = $equiposIndex[$a['local']] - $equiposIndex[$a['visitante']];
            $balanceB = $equiposIndex[$b['local']] - $equiposIndex[$b['visitante']];
            return $balanceA - $balanceB;
        });
        
        // 2. Mezclar para evitar secuencias repetidas
        $grupos = array_chunk($emparejamientos, ceil(count($emparejamientos)/$numEquipos));
        shuffle($grupos);
        $emparejamientos = array_merge(...$grupos);
        
        // 3. Asegurar que ningún equipo juegue consecutivamente
        $ultimosPartidos = array_fill_keys($equiposIds, -2);
        $emparejamientosBalanceados = [];
        
        $intentos = 0;
        $maxIntentos = count($emparejamientos) * 2;
        
        while (!empty($emparejamientos) && $intentos < $maxIntentos) {
            $encontrado = false;
            
            foreach ($emparejamientos as $key => $emparejamiento) {
                $localId = $emparejamiento['local'];
                $visitanteId = $emparejamiento['visitante'];
                
                // Verificar que no jueguen consecutivamente
                if ($ultimosPartidos[$localId] < $intentos - 1 && 
                    $ultimosPartidos[$visitanteId] < $intentos - 1) {
                    
                    $emparejamientosBalanceados[] = $emparejamiento;
                    $ultimosPartidos[$localId] = $intentos;
                    $ultimosPartidos[$visitanteId] = $intentos;
                    unset($emparejamientos[$key]);
                    $encontrado = true;
                    break;
                }
            }
            
            if (!$encontrado) {
                // Forzar movimiento si no se encuentra emparejamiento ideal
                $emparejamientosBalanceados[] = array_shift($emparejamientos);
            }
            
            $intentos++;
        }
        
        // Si quedan emparejamientos, agregarlos al final
        if (!empty($emparejamientos)) {
            $emparejamientosBalanceados = array_merge($emparejamientosBalanceados, $emparejamientos);
        }
        
        Log::info('Emparejamientos balanceados', [
            'total_balanceados' => count($emparejamientosBalanceados),
            'equipos' => $equiposIds
        ]);
        
        return $emparejamientosBalanceados;
    }

    /**
     * Crea juegos en formato todos contra todos con programación inteligente mejorada
     */
    private function crearJuegosTodosContraTodos(Torneo $torneo, $equipos, Temporada $temporada, array $diasDisponibles): int
    {
        Log::info('=== INICIANDO CREACIÓN DE JUEGOS TODOS CONTRA TODOS ===', [
            'torneo_id' => $torneo->id,
            'total_equipos' => $equipos->count(),
            'dias_disponibles' => count($diasDisponibles)
        ]);

        // 1. Generar todos los emparejamientos necesarios
        $emparejamientos = $this->generarEmparejamientos($equipos);
        $totalJuegos = count($emparejamientos);
        
        Log::info('Emparejamientos preparados', [
            'total_juegos_a_programar' => $totalJuegos
        ]);
        
        // 2. Programar los juegos día por día
        $juegosCreados = 0;
        $duracionJuegoCompleto = $this->calcularDuracionTotalJuego($torneo) + $torneo->tiempo_entre_partidos_minutos;
        
        // Estadísticas para balancear
        $partidosPorEquipo = array_fill_keys($equipos->pluck('id')->toArray(), 0);
        $ultimoPartidoEquipo = [];
        
        foreach ($diasDisponibles as $indiceDia => $dia) {
            if (empty($emparejamientos)) {
                Log::info('Todos los emparejamientos han sido programados');
                break;
            }
            
            Log::info("--- Programando juegos para día #{$indiceDia} ---", [
                'fecha' => $dia['fecha']->format('Y-m-d (D)'),
                'emparejamientos_restantes' => count($emparejamientos)
            ]);
            
            $horario = $this->obtenerHorarioDelDia($dia['fecha'], $dia['diasHabiles'], $temporada);
            $horaActual = $horario['inicio']->copy();
            $juegosEsteDia = 0;
            
            // Ordenar emparejamientos para priorizar equipos con menos partidos
            usort($emparejamientos, function($a, $b) use ($partidosPorEquipo, $ultimoPartidoEquipo) {
                $prioridadA = $partidosPorEquipo[$a['local']] + $partidosPorEquipo[$a['visitante']];
                $prioridadB = $partidosPorEquipo[$b['local']] + $partidosPorEquipo[$b['visitante']];
                
                // Considerar también cuándo jugaron por última vez
                if (isset($ultimoPartidoEquipo[$a['local']])) {
                    $prioridadA -= $ultimoPartidoEquipo[$a['local']];
                }
                if (isset($ultimoPartidoEquipo[$a['visitante']])) {
                    $prioridadA -= $ultimoPartidoEquipo[$a['visitante']];
                }
                
                if (isset($ultimoPartidoEquipo[$b['local']])) {
                    $prioridadB -= $ultimoPartidoEquipo[$b['local']];
                }
                if (isset($ultimoPartidoEquipo[$b['visitante']])) {
                    $prioridadB -= $ultimoPartidoEquipo[$b['visitante']];
                }
                
                return $prioridadA - $prioridadB;
            });
            
            while (!empty($emparejamientos) && $horaActual->copy()->addMinutes($duracionJuegoCompleto)->lte($horario['fin'])) {
                $emparejamiento = array_shift($emparejamientos);
                
                // Crear el juego
                $juego = $this->crearJuego(
                    $torneo,
                    $emparejamiento['local'],
                    $emparejamiento['visitante'],
                    $dia['fecha'],
                    $horaActual,
                    'Fase Regular'
                );
                
                // Actualizar estadísticas
                $partidosPorEquipo[$emparejamiento['local']]++;
                $partidosPorEquipo[$emparejamiento['visitante']]++;
                $ultimoPartidoEquipo[$emparejamiento['local']] = $indiceDia;
                $ultimoPartidoEquipo[$emparejamiento['visitante']] = $indiceDia;
                
                Log::info('✓ Juego programado exitosamente', [
                    'juego_id' => $juego->id,
                    'fecha' => $dia['fecha']->format('Y-m-d'),
                    'hora' => $horaActual->format('H:i'),
                    'equipo_local' => $emparejamiento['equipo_local_nombre'],
                    'equipo_visitante' => $emparejamiento['equipo_visitante_nombre'],
                    'duracion_estimada' => $duracionJuegoCompleto . ' minutos'
                ]);
                
                // Avanzar el horario para el próximo juego
                $horaActual = $horaActual->copy()->addMinutes($duracionJuegoCompleto);
                $juegosEsteDia++;
                $juegosCreados++;
            }
            
            Log::info("--- Día completado ---", [
                'fecha' => $dia['fecha']->format('Y-m-d'),
                'juegos_programados_este_dia' => $juegosEsteDia,
                'juegos_totales_hasta_ahora' => $juegosCreados,
                'emparejamientos_restantes' => count($emparejamientos)
            ]);
        }
        
        // 3. Validar que todos los juegos fueron programados
        if (!empty($emparejamientos)) {
            Log::error('=== ERROR: NO SE PUDIERON PROGRAMAR TODOS LOS JUEGOS ===', [
                'juegos_sin_programar' => count($emparejamientos),
                'juegos_programados' => $juegosCreados,
                'total_esperado' => $totalJuegos,
                'emparejamientos_faltantes' => array_slice($emparejamientos, 0, 5)
            ]);
            throw new \Exception("No se pudieron programar todos los juegos. Faltan " . count($emparejamientos) . " juegos por programar.");
        }
        
        Log::info('=== JUEGOS TODOS CONTRA TODOS CREADOS EXITOSAMENTE ===', [
            'torneo_id' => $torneo->id,
            'total_juegos_creados' => $juegosCreados,
            'total_esperado' => $totalJuegos,
            'dias_utilizados' => count($diasDisponibles),
            'equipos_participantes' => $equipos->count(),
            'juegos_por_equipo' => $equipos->count() - 1,
            'distribucion_partidos' => $partidosPorEquipo
        ]);
        
        return $juegosCreados;
    }

    /**
     * Calcula la duración total de un juego en minutos
     */
    private function calcularDuracionTotalJuego(Torneo $torneo): int
    {
        // Configuración estándar de básquetbol
        $cuartos = 4;
        $descansos = 3; // Entre cuartos
        $duracionDescanso = 5; // minutos por descanso
        
        $duracionTotal = ($cuartos * $torneo->duracion_cuarto_minutos) + ($descansos * $duracionDescanso);
        
        Log::debug('Duración calculada del juego', [
            'cuartos' => $cuartos,
            'duracion_por_cuarto' => $torneo->duracion_cuarto_minutos . ' minutos',
            'descansos' => $descansos,
            'duracion_por_descanso' => $duracionDescanso . ' minutos',
            'duracion_total_juego' => $duracionTotal . ' minutos'
        ]);
        
        return $duracionTotal;
    }

    /**
     * Obtiene los días hábiles configurados para la temporada
     */
    private function obtenerDiasHabiles($temporadaId)
    {
        Log::info('Obteniendo días hábiles de la temporada', [
            'temporada_id' => $temporadaId
        ]);
        
        $diasHabiles = DB::table('temporada_dias_habiles')
            ->where('temporada_id', $temporadaId)
            ->where('activo', true)
            ->orderBy('dia_semana')
            ->get()
            ->keyBy('dia_semana');

        Log::info('Días hábiles obtenidos de BD', [
            'temporada_id' => $temporadaId,
            'total_dias_configurados' => $diasHabiles->count(),
            'dias_configurados' => $diasHabiles->map(function($dia) {
                return [
                    'dia_semana' => $dia->dia_semana,
                    'horario_inicio' => $dia->horario_inicio,
                    'horario_fin' => $dia->horario_fin,
                    'activo' => $dia->activo
                ];
            })->toArray()
        ]);

        if ($diasHabiles->isEmpty()) {
            Log::warning('No se encontraron días hábiles configurados, usando días por defecto (lunes a viernes)', [
                'temporada_id' => $temporadaId
            ]);
            
            // Crear días hábiles por defecto (lunes a viernes)
            $diasHabiles = collect();
            for ($dia = 1; $dia <= 5; $dia++) {
                $diasHabiles->put($dia, (object) [
                    'dia_semana' => $dia,
                    'horario_inicio' => null,
                    'horario_fin' => null,
                    'activo' => true
                ]);
            }
            
            Log::info('Días hábiles por defecto creados', [
                'dias_creados' => $diasHabiles->keys()->toArray()
            ]);
        }

        return $diasHabiles;
    }

    /**
     * Encuentra el siguiente día hábil desde una fecha dada
     */
    private function encontrarSiguienteDiaHabil(Carbon $fecha, $diasHabiles): ?Carbon
    {
        Log::debug('Buscando siguiente día hábil', [
            'fecha_inicio' => $fecha->format('Y-m-d (D)'),
            'dias_habiles_disponibles' => $diasHabiles->keys()->toArray()
        ]);
        
        $maxIntentos = 14; // Para evitar bucles infinitos (2 semanas)
        $intentos = 0;
        $fechaOriginal = $fecha->copy();
        
        while ($intentos < $maxIntentos) {
            $diaSemana = $fecha->dayOfWeekIso; // 1=Lunes, 2=Martes, ..., 7=Domingo
            
            Log::debug("Intento #{$intentos}: evaluando fecha", [
                'fecha' => $fecha->format('Y-m-d'),
                'dia_semana' => $diaSemana,
                'nombre_dia' => $fecha->locale('es')->dayName,
                'es_dia_habil' => $diasHabiles->has($diaSemana) ? 'SÍ' : 'NO'
            ]);
            
            if ($diasHabiles->has($diaSemana)) {
                Log::info('✓ Día hábil encontrado', [
                    'fecha_original' => $fechaOriginal->format('Y-m-d'),
                    'fecha_encontrada' => $fecha->format('Y-m-d (D)'),
                    'dias_avanzados' => $intentos,
                    'dia_semana' => $diaSemana
                ]);
                return $fecha;
            }
            
            $fecha->addDay();
            $intentos++;
        }
        
        Log::error('No se pudo encontrar día hábil válido', [
            'fecha_inicio' => $fechaOriginal->format('Y-m-d'),
            'intentos_realizados' => $intentos,
            'dias_habiles_configurados' => $diasHabiles->keys()->toArray()
        ]);
        
        return null;
    }

    /**
     * Obtiene el horario específico para un día, considerando horarios personalizados
     */
    private function obtenerHorarioDelDia(Carbon $fecha, $diasHabiles, Temporada $temporada): array
    {
        $diaSemana = $fecha->dayOfWeekIso;
        $diaHabil = $diasHabiles->get($diaSemana);
        
        Log::debug('Obteniendo horario específico del día', [
            'fecha' => $fecha->format('Y-m-d (D)'),
            'dia_semana' => $diaSemana,
            'dia_habil_encontrado' => $diaHabil ? 'SÍ' : 'NO'
        ]);
        
        if (!$diaHabil) {
            throw new \Exception("El día {$fecha->format('Y-m-d')} (día de semana: {$diaSemana}) no es un día hábil válido");
        }
        
        // Horarios por defecto de la temporada
        $horarioInicioDefault = Carbon::parse($temporada->horario_inicio);
        $horarioFinDefault = Carbon::parse($temporada->horario_fin);
        
        Log::debug('Horarios por defecto de temporada', [
            'horario_inicio_default' => $horarioInicioDefault->format('H:i'),
            'horario_fin_default' => $horarioFinDefault->format('H:i')
        ]);
        
        // Determinar horarios para este día específico
        $horarioInicio = $diaHabil->horario_inicio 
            ? Carbon::parse($diaHabil->horario_inicio)
            : $horarioInicioDefault->copy();
            
        $horarioFin = $diaHabil->horario_fin
            ? Carbon::parse($diaHabil->horario_fin)
            : $horarioFinDefault->copy();
        
        Log::debug('Horarios iniciales determinados', [
            'horario_inicio_calculado' => $horarioInicio->format('H:i'),
            'horario_fin_calculado' => $horarioFin->format('H:i'),
            'tiene_horario_personalizado' => ($diaHabil->horario_inicio !== null || $diaHabil->horario_fin !== null)
        ]);
        
        // Validar coherencia de horarios
        if ($horarioInicio->gte($horarioFin)) {
            Log::warning('Horario inválido detectado, usando horario por defecto', [
                'fecha' => $fecha->format('Y-m-d'),
                'horario_inicio_invalido' => $horarioInicio->format('H:i'),
                'horario_fin_invalido' => $horarioFin->format('H:i'),
                'horario_inicio_corregido' => $horarioInicioDefault->format('H:i'),
                'horario_fin_corregido' => $horarioFinDefault->format('H:i')
            ]);
            $horarioInicio = $horarioInicioDefault->copy();
            $horarioFin = $horarioFinDefault->copy();
        }
        
        // Asegurar que el horario no exceda el horario por defecto de la temporada
        if ($horarioInicio->lt($horarioInicioDefault)) {
            Log::debug('Ajustando horario de inicio para no exceder límites de temporada', [
                'horario_original' => $horarioInicio->format('H:i'),
                'horario_ajustado' => $horarioInicioDefault->format('H:i')
            ]);
            $horarioInicio = $horarioInicioDefault->copy();
        }
        
        if ($horarioFin->gt($horarioFinDefault)) {
            Log::debug('Ajustando horario de fin para no exceder límites de temporada', [
                'horario_original' => $horarioFin->format('H:i'),
                'horario_ajustado' => $horarioFinDefault->format('H:i')
            ]);
            $horarioFin = $horarioFinDefault->copy();
        }
        
        $duracionDisponible = $horarioInicio->diffInMinutes($horarioFin);
        
        Log::info('✓ Horario del día determinado', [
            'fecha' => $fecha->format('Y-m-d (D)'),
            'dia_semana' => $diaSemana,
            'horario_inicio_final' => $horarioInicio->format('H:i'),
            'horario_fin_final' => $horarioFin->format('H:i'),
            'duracion_disponible' => $duracionDisponible . ' minutos',
            'es_personalizado' => ($diaHabil->horario_inicio !== null || $diaHabil->horario_fin !== null)
        ]);
        
        return [
            'inicio' => $horarioInicio,
            'fin' => $horarioFin,
            'duracion' => $duracionDisponible
        ];
    }

    /**
     * Crea un juego individual con asignación aleatoria de cancha
     */
    private function crearJuego(Torneo $torneo, $equipoLocalId, $equipoVisitanteId, $fecha, $hora, $fase = 'Fase Regular')
    {
        Log::debug('Creando juego individual con cancha aleatoria', [
            'torneo_id' => $torneo->id,
            'equipo_local_id' => $equipoLocalId,
            'equipo_visitante_id' => $equipoVisitanteId,
            'fecha' => $fecha->format('Y-m-d'),
            'hora' => $hora->format('H:i:s'),
            'fase' => $fase
        ]);
        
        // Validaciones básicas
        if ($equipoLocalId === $equipoVisitanteId) {
            throw new \Exception("Un equipo no puede jugar contra sí mismo");
        }
        
        // Verificar juego duplicado
        $juegoExistente = Juego::where('torneo_id', $torneo->id)
            ->where(function($query) use ($equipoLocalId, $equipoVisitanteId) {
                $query->where(function($q) use ($equipoLocalId, $equipoVisitanteId) {
                    $q->where('equipo_local_id', $equipoLocalId)
                    ->where('equipo_visitante_id', $equipoVisitanteId);
                })->orWhere(function($q) use ($equipoLocalId, $equipoVisitanteId) {
                    $q->where('equipo_local_id', $equipoVisitanteId)
                    ->where('equipo_visitante_id', $equipoLocalId);
                });
            })
            ->first();
            
        if ($juegoExistente) {
            throw new \Exception("Ya existe un juego entre estos equipos en el torneo");
        }
        
        // Obtener todas las canchas asignadas al torneo
        $canchasTorneo = DB::table('torneo_cancha')
            ->where('torneo_id', $torneo->id)
            ->get();
            
        if ($canchasTorneo->isEmpty()) {
            throw new \Exception("El torneo no tiene canchas asignadas");
        }
        
        Log::debug('Canchas disponibles para el torneo', [
            'total_canchas' => $canchasTorneo->count(),
            'canchas_ids' => $canchasTorneo->pluck('cancha_id')->toArray()
        ]);
        
        // Calcular duración total del juego
        $duracionTotal = $this->calcularDuracionTotalJuego($torneo);
        
        // Convertir a array y barajar aleatoriamente las canchas
        $canchasArray = $canchasTorneo->toArray();
        shuffle($canchasArray);
        
        Log::debug('Canchas barajadas aleatoriamente', [
            'orden_aleatorio' => array_column($canchasArray, 'cancha_id')
        ]);
        
        // Buscar la primera cancha disponible del array aleatorio
        $canchaSeleccionada = null;
        foreach ($canchasArray as $cancha) {
            if ($this->verificarDisponibilidadCancha(
                $cancha->cancha_id,
                $fecha,
                $hora,
                $duracionTotal
            )) {
                $canchaSeleccionada = $cancha;
                break;
            }
        }
        
        // Si no hay cancha disponible, seleccionar una aleatoriamente sin verificar disponibilidad
        // (esto puede ser necesario dependiendo de tu lógica de negocio)
        if (!$canchaSeleccionada) {
            Log::warning('No se encontró cancha disponible, seleccionando aleatoriamente', [
                'fecha' => $fecha->format('Y-m-d'),
                'hora' => $hora->format('H:i'),
                'canchas_evaluadas' => array_column($canchasArray, 'cancha_id')
            ]);
            
            // Seleccionar una cancha aleatoria
            $canchaSeleccionada = $canchasArray[array_rand($canchasArray)];
        }
        
        Log::info('Cancha seleccionada para el juego', [
            'cancha_id' => $canchaSeleccionada->cancha_id,
            'es_principal' => $canchaSeleccionada->es_principal ?? false,
            'orden_prioridad' => $canchaSeleccionada->orden_prioridad ?? null,
            'metodo_seleccion' => $canchaSeleccionada ? 'disponible' : 'aleatoria'
        ]);
        
        try {
            $juego = Juego::create([
                'liga_id' => $torneo->liga_id,
                'temporada_id' => $torneo->temporada_id,
                'torneo_id' => $torneo->id,
                'equipo_local_id' => $equipoLocalId,
                'equipo_visitante_id' => $equipoVisitanteId,
                'cancha_id' => $canchaSeleccionada->cancha_id,
                'fecha' => $fecha->format('Y-m-d'),
                'hora' => $hora->format('H:i:s'),
                'duracion_cuarto' => $torneo->duracion_cuarto_minutos,
                'duracion_descanso' => 5,
                'estado' => 'Programado',
                'fase' => $fase,
                'activo' => true,
                'observaciones' => "Generado automáticamente - Torneo por puntos - Cancha asignada aleatoriamente"
            ]);
            
            Log::info('✅ Juego creado exitosamente con cancha aleatoria', [
                'juego_id' => $juego->id,
                'cancha_asignada' => $canchaSeleccionada->cancha_id,
                'fecha' => $fecha->format('Y-m-d'),
                'hora' => $hora->format('H:i')
            ]);
            
            return $juego;
            
        } catch (\Exception $e) {
            Log::error('Error al crear juego con cancha aleatoria', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    
    private function verificarDisponibilidadCancha($canchaId, $fecha, $hora, $duracion): bool
    {
        $horaInicio = Carbon::parse($hora);
        $horaFin = $horaInicio->copy()->addMinutes($duracion);
        
        $juegosExistentes = Juego::where('cancha_id', $canchaId)
            ->where('fecha', $fecha->format('Y-m-d'))
            ->where(function($query) use ($horaInicio, $horaFin) {
                $query->where(function($q) use ($horaInicio, $horaFin) {
                    $q->where('hora', '>=', $horaInicio->format('H:i:s'))
                    ->where('hora', '<', $horaFin->format('H:i:s'));
                })->orWhere(function($q) use ($horaInicio) {
                    $q->where('hora', '<=', $horaInicio->format('H:i:s'))
                    ->whereRaw('ADDTIME(hora, SEC_TO_TIME(duracion_cuarto*4*60 + duracion_descanso*3*60)) > ?', 
                    [$horaInicio->format('H:i:s')]);
                });
            })
            ->count();
        
        return $juegosExistentes === 0;
    }

    /**
     * Método de diagnóstico para validar la configuración completa
     */
    public function diagnosticarTorneo(Torneo $torneo): array
    {
        Log::info('=== INICIANDO DIAGNÓSTICO COMPLETO DEL TORNEO ===', [
            'torneo_id' => $torneo->id,
            'torneo_nombre' => $torneo->nombre
        ]);
        
        $diagnostico = [
            'torneo' => [],
            'temporada' => [],
            'dias_habiles' => [],
            'equipos' => [],
            'capacidad' => [],
            'recomendaciones' => []
        ];
        
        try {
            // 1. Diagnóstico del torneo
            $diagnostico['torneo'] = [
                'id' => $torneo->id,
                'nombre' => $torneo->nombre,
                'tipo' => $torneo->tipo,
                'fecha_inicio' => $torneo->fecha_inicio,
                'fecha_fin' => $torneo->fecha_fin,
                'duracion_cuarto_minutos' => $torneo->duracion_cuarto_minutos,
                'tiempo_entre_partidos_minutos' => $torneo->tiempo_entre_partidos_minutos,
                'validaciones' => []
            ];
            
            // Validaciones del torneo
            if (!$torneo->temporada_id) {
                $diagnostico['torneo']['validaciones'][] = 'ERROR: Falta temporada_id';
            }
            if (!$torneo->liga_id) {
                $diagnostico['torneo']['validaciones'][] = 'ERROR: Falta liga_id';
            }
            // Agregar validación de canchas asignadas
            $canchasAsignadas = DB::table('torneo_cancha')->where('torneo_id', $torneo->id)->count();
            if ($canchasAsignadas === 0) {
                $diagnostico['torneo']['validaciones'][] = 'ERROR: El torneo no tiene canchas asignadas';
            } else {
                $diagnostico['torneo']['canchas_asignadas'] = $canchasAsignadas;
            }
            if (!$torneo->duracion_cuarto_minutos || $torneo->duracion_cuarto_minutos <= 0) {
                $diagnostico['torneo']['validaciones'][] = 'ERROR: Duración del cuarto inválida';
            }
            
            // 2. Diagnóstico de la temporada
            if ($torneo->temporada_id) {
                $temporada = Temporada::find($torneo->temporada_id);
                if ($temporada) {
                    $diagnostico['temporada'] = [
                        'id' => $temporada->id,
                        'nombre' => $temporada->nombre,
                        'fecha_inicio' => $temporada->fecha_inicio,
                        'fecha_fin' => $temporada->fecha_fin,
                        'horario_inicio' => $temporada->horario_inicio,
                        'horario_fin' => $temporada->horario_fin,
                        'activo' => $temporada->activo,
                        'validaciones' => []
                    ];
                    
                    if (!$temporada->activo) {
                        $diagnostico['temporada']['validaciones'][] = 'WARNING: Temporada no está activa';
                    }
                    
                    // 3. Diagnóstico de días hábiles
                    $diasHabiles = $this->obtenerDiasHabiles($temporada->id);
                    $diagnostico['dias_habiles'] = [
                        'total_configurados' => $diasHabiles->count(),
                        'detalle' => [],
                        'validaciones' => []
                    ];
                    
                    foreach ($diasHabiles as $dia) {
                        $diagnostico['dias_habiles']['detalle'][] = [
                            'dia_semana' => $dia->dia_semana,
                            'nombre_dia' => Carbon::now()->dayOfWeekIso($dia->dia_semana)->locale('es')->dayName,
                            'horario_inicio' => $dia->horario_inicio,
                            'horario_fin' => $dia->horario_fin,
                            'activo' => $dia->activo
                        ];
                    }
                    
                    if ($diasHabiles->isEmpty()) {
                        $diagnostico['dias_habiles']['validaciones'][] = 'WARNING: No hay días hábiles configurados';
                    }
                    
                    // 4. Diagnóstico de días disponibles en rango
                    try {
                        $diasDisponibles = $this->obtenerDiasHabilesEnRango($torneo, $temporada);
                        $diagnostico['dias_habiles']['dias_en_rango'] = count($diasDisponibles);
                        $diagnostico['dias_habiles']['primer_dia_disponible'] = !empty($diasDisponibles) ? $diasDisponibles[0]['fecha']->format('Y-m-d') : null;
                        $diagnostico['dias_habiles']['ultimo_dia_disponible'] = !empty($diasDisponibles) ? end($diasDisponibles)['fecha']->format('Y-m-d') : null;
                    } catch (\Exception $e) {
                        $diagnostico['dias_habiles']['validaciones'][] = 'ERROR: ' . $e->getMessage();
                    }
                }
            }
            
            // 4. Diagnóstico de equipos
            try {
                $equipos = $this->obtenerEquiposElegibles($torneo);
                $diagnostico['equipos'] = [
                    'total_encontrados' => $equipos->count(),
                    'detalle' => $equipos->pluck('nombre', 'id')->toArray(),
                    'validaciones' => []
                ];
                
                if ($equipos->count() < 2) {
                    $diagnostico['equipos']['validaciones'][] = 'ERROR: Se necesitan al menos 2 equipos';
                }
                
                // 5. Diagnóstico de capacidad
                if ($equipos->count() >= 2) {
                    $totalJuegos = $this->calcularTotalJuegos($equipos->count());
                    $diagnostico['capacidad']['juegos_necesarios'] = $totalJuegos;
                    
                    try {
                        if (isset($diasDisponibles) && !empty($diasDisponibles)) {
                            $this->validarCapacidadCompleta($torneo, $temporada, $totalJuegos, $diasDisponibles);
                            $diagnostico['capacidad']['validaciones'][] = 'OK: Capacidad suficiente';
                        }
                    } catch (\Exception $e) {
                        $diagnostico['capacidad']['validaciones'][] = 'ERROR: ' . $e->getMessage();
                    }
                }
            } catch (\Exception $e) {
                $diagnostico['equipos']['validaciones'][] = 'ERROR: ' . $e->getMessage();
            }
            
            // 6. Generar recomendaciones
            $diagnostico['recomendaciones'] = $this->generarRecomendaciones($diagnostico);
            
        } catch (\Exception $e) {
            Log::error('Error en diagnóstico', ['error' => $e->getMessage()]);
            $diagnostico['error_general'] = $e->getMessage();
        }
        
        Log::info('=== DIAGNÓSTICO COMPLETADO ===', [
            'total_errores' => $this->contarErrores($diagnostico),
            'total_warnings' => $this->contarWarnings($diagnostico)
        ]);
        
        return $diagnostico;
    }
    
    /**
     * Genera recomendaciones basadas en el diagnóstico
     */
    private function generarRecomendaciones(array $diagnostico): array
    {
        $recomendaciones = [];
        
        // Analizar errores y generar recomendaciones específicas
        foreach ($diagnostico as $seccion => $datos) {
            if (isset($datos['validaciones'])) {
                foreach ($datos['validaciones'] as $validacion) {
                    if (strpos($validacion, 'ERROR:') !== false) {
                        $recomendaciones[] = $this->obtenerRecomendacionPorError($validacion);
                    }
                }
            }
        }
        
        // Recomendaciones generales
        if (isset($diagnostico['equipos']['total_encontrados'])) {
            $totalEquipos = $diagnostico['equipos']['total_encontrados'];
            if ($totalEquipos > 10) {
                $recomendaciones[] = "Con {$totalEquipos} equipos, considera dividir en grupos o usar eliminación directa";
            }
        }
        
        return array_unique($recomendaciones);
    }
    
    /**
     * Obtiene recomendación específica por tipo de error
     */
    private function obtenerRecomendacionPorError(string $error): string
    {
        if (strpos($error, 'temporada_id') !== false) {
            return 'Asigna una temporada válida al torneo';
        }
        if (strpos($error, 'liga_id') !== false) {
            return 'Asigna una liga válida al torneo';
        }
        if (strpos($error, 'cancha_id') !== false) {
            return 'Asigna una cancha válida al torneo';
        }
        if (strpos($error, 'duración del cuarto') !== false) {
            return 'Configura una duración válida para los cuartos (ej: 10-12 minutos)';
        }
        if (strpos($error, 'días hábiles') !== false) {
            return 'Configura al menos un día hábil para la temporada';
        }
        if (strpos($error, 'equipos') !== false) {
            return 'Asegúrate de tener equipos activos en la liga y categoría del torneo';
        }
        if (strpos($error, 'Capacidad') !== false) {
            return 'Extiende las fechas del torneo, agrega más días hábiles, o reduce la duración de los juegos';
        }
        
        return 'Revisa la configuración del torneo';
    }
    
    /**
     * Cuenta errores en el diagnóstico
     */
    private function contarErrores(array $diagnostico): int
    {
        $errores = 0;
        foreach ($diagnostico as $seccion => $datos) {
            if (isset($datos['validaciones'])) {
                foreach ($datos['validaciones'] as $validacion) {
                    if (strpos($validacion, 'ERROR:') !== false) {
                        $errores++;
                    }
                }
            }
        }
        return $errores;
    }
    
    /**
     * Cuenta warnings en el diagnóstico
     */
    private function contarWarnings(array $diagnostico): int
    {
        $warnings = 0;
        foreach ($diagnostico as $seccion => $datos) {
            if (isset($datos['validaciones'])) {
                foreach ($datos['validaciones'] as $validacion) {
                    if (strpos($validacion, 'WARNING:') !== false) {
                        $warnings++;
                    }
                }
            }
        }
        return $warnings;
    }
}