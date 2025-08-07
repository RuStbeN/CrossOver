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
     * Configuración completa del torneo por puntos
     */
    public function configurarTorneo(Torneo $torneo)
    {
        Log::info('Configurando torneo por puntos (todos contra todos)', [
            'torneo_id' => $torneo->id,
            'tipo' => $torneo->tipo
        ]);
        
        // 1. Obtener equipos elegibles
        $equipos = $this->obtenerEquiposElegibles($torneo);
        
        // 2. Validar cantidad mínima
        if ($equipos->count() < 2) {
            throw new \Exception('Se necesitan al menos 2 equipos para crear un torneo');
        }
        
        // 3. Asignar equipos al torneo (sin grupos)
        $this->asignarEquiposAlTorneo($torneo, $equipos);

        // 4. Registrar clasificación inicial para cada equipo
        $this->registrarClasificacionEquipos($torneo->id, $equipos);
        
        // 5. Crear juegos todos contra todos con programación inteligente
        $this->crearJuegosTodosContraTodos($torneo, $equipos);
        
        Log::info('Configuración de torneo por puntos completada', [
            'torneo_id' => $torneo->id,
            'equipos_asignados' => $equipos->count(),
            'tipo' => $torneo->tipo
        ]);
    }

    /**
     * Obtiene equipos elegibles para el torneo
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

        // Fallback: buscar solo por liga
        if ($equipos->isEmpty()) {
            Log::warning('No se encontraron equipos con liga y categoría, buscando solo por liga');
            
            $equipos = Equipo::where('liga_id', $torneo->liga_id)
                            ->where('activo', true)
                            ->get();
        }

        Log::info('Equipos encontrados para torneo por puntos', [
            'total' => $equipos->count(),
            'equipos' => $equipos->pluck('nombre')->toArray()
        ]);

        return $equipos;
    }

    /**
     * Asigna equipos al torneo en la tabla pivote (máximo 2 equipos por grupo)
     */
    private function asignarEquiposAlTorneo(Torneo $torneo, $equipos)
    {
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
        
        DB::table('torneo_equipo')->insert($equiposData);
        
        Log::info('Equipos asignados al torneo por puntos (sin grupos)', [
            'torneo_id' => $torneo->id,
            'equipos_asignados' => count($equiposData)
        ]);
    }

    /**
     * Registra la clasificación inicial de los equipos
     */
    public function registrarClasificacionEquipos($torneoId, $equipos)
    {
        foreach ($equipos as $equipo) {
            TorneoClasificacion::create([
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
            ]);
        }
    }

    /**
     * Verifica que hay suficiente capacidad en los días hábiles para todos los juegos
     */
    private function verificarCapacidadTorneo(Torneo $torneo, int $numJuegosNecesarios): void
    {
        $temporada = Temporada::find($torneo->temporada_id);
        $diasDisponibles = $this->obtenerDiasHabilesEnRango($torneo, $temporada);
        
        if (empty($diasDisponibles)) {
            throw new \Exception('No hay días hábiles disponibles en el rango del torneo');
        }
        
        $capacidadTotal = 0;
        foreach ($diasDisponibles as $dia) {
            $horario = $this->obtenerHorarioDelDia($dia['fecha'], $dia['diasHabiles'], $temporada);
            $duracionJornada = $horario['inicio']->diffInMinutes($horario['fin']);
            $duracionJuego = $this->calcularDuracionTotalJuego($torneo) + $torneo->tiempo_entre_partidos_minutos;
            $capacidadDia = floor($duracionJornada / $duracionJuego);
            $capacidadTotal += $capacidadDia;
        }
        
        if ($numJuegosNecesarios > $capacidadTotal) {
            throw new \Exception(sprintf(
                'No hay suficiente capacidad. Se necesitan %d slots de juego pero solo hay %d disponibles',
                $numJuegosNecesarios,
                $capacidadTotal
            ));
        }
        
        Log::info('Capacidad del torneo verificada', [
            'juegos_necesarios' => $numJuegosNecesarios,
            'capacidad_total' => $capacidadTotal,
            'dias_disponibles' => count($diasDisponibles)
        ]);
    }

    /**
     * Obtiene todos los días hábiles dentro del rango del torneo
     */
    private function obtenerDiasHabilesEnRango(Torneo $torneo, Temporada $temporada): array
    {
        $diasHabiles = $this->obtenerDiasHabiles($temporada->id);
        $fechaInicio = Carbon::parse($torneo->fecha_inicio);
        $fechaFin = Carbon::parse($torneo->fecha_fin ?? $temporada->fecha_fin); // Usar fecha fin de temporada si no hay en torneo

        // Encontrar el primer día hábil a partir de la fecha de inicio
        $primerDiaHabil = $this->encontrarSiguienteDiaHabil($fechaInicio, $diasHabiles);

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

            // Avanzar al siguiente día
            $fechaActual->addDay();

            // Saltar días no hábiles
            while ($fechaActual->lte($fechaFin) && !$diasHabiles->has($fechaActual->dayOfWeekIso)) {
                $fechaActual->addDay();
            }
        }

        Log::info('Días hábiles encontrados en rango', [
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
            'primer_dia_habil' => $primerDiaHabil->format('Y-m-d'),
            'total_dias_disponibles' => count($diasDisponibles),
            'dias' => array_map(fn($d) => $d['fecha']->format('Y-m-d (D)'), $diasDisponibles)
        ]);

        return $diasDisponibles;
    }


    /**
     * Genera emparejamientos todos contra todos con orden aleatorio
     */
    private function generarEmparejamientos($equipos): array
    {
        $equiposArray = $equipos->toArray();
        $emparejamientos = [];
        
        for ($i = 0; $i < count($equiposArray); $i++) {
            for ($j = $i + 1; $j < count($equiposArray); $j++) {
                $emparejamientos[] = [
                    'local' => $equiposArray[$i]['id'],
                    'visitante' => $equiposArray[$j]['id'],
                    'equipo_local_nombre' => $equiposArray[$i]['nombre'],
                    'equipo_visitante_nombre' => $equiposArray[$j]['nombre']
                ];
            }
        }
        
        // Mezclar para mejor distribución
        shuffle($emparejamientos);
        
        return $emparejamientos;
    }

    /**
     * Crea juegos en formato todos contra todos con programación inteligente
     */
    private function crearJuegosTodosContraTodos(Torneo $torneo, $equipos)
    {
        Log::info('Creando juegos todos contra todos (versión mejorada)', [
            'torneo_id' => $torneo->id,
            'total_equipos' => $equipos->count()
        ]);

        // 1. Generar todos los emparejamientos necesarios
        $emparejamientos = $this->generarEmparejamientos($equipos);
        $totalJuegos = count($emparejamientos);
        
        // 2. Verificar capacidad antes de empezar
        $this->verificarCapacidadTorneo($torneo, $totalJuegos);
        
        // 3. Obtener días hábiles dentro del rango del torneo
        $temporada = Temporada::find($torneo->temporada_id);
        $diasDisponibles = $this->obtenerDiasHabilesEnRango($torneo, $temporada);
        
        // 4. Calcular juegos por día (distribución equitativa)
        $juegosPorDia = ceil($totalJuegos / count($diasDisponibles));
        
        Log::debug('Distribución de juegos calculada', [
            'total_juegos' => $totalJuegos,
            'dias_disponibles' => count($diasDisponibles),
            'juegos_por_dia' => $juegosPorDia
        ]);
        
        // 5. Programar los juegos
        $juegosCreados = 0;
        foreach ($diasDisponibles as $dia) {
            $juegosEsteDia = 0;
            $horario = $this->obtenerHorarioDelDia($dia['fecha'], $dia['diasHabiles'], $temporada);
            $horaActual = $horario['inicio']->copy();
            
            Log::debug('Programando juegos para día', [
                'fecha' => $dia['fecha']->format('Y-m-d'),
                'dia_semana' => $dia['dia_semana'],
                'horario_inicio' => $horario['inicio']->format('H:i'),
                'horario_fin' => $horario['fin']->format('H:i')
            ]);
            
            while ($juegosEsteDia < $juegosPorDia && !empty($emparejamientos)) {
                $emparejamiento = array_shift($emparejamientos);
                
                // Calcular si el juego cabe en el horario actual
                $duracionTotal = $this->calcularDuracionTotalJuego($torneo);
                $horaFinJuego = $horaActual->copy()->addMinutes($duracionTotal);
                
                if ($horaFinJuego->lte($horario['fin'])) {
                    // Crear el juego
                    $juego = $this->crearJuego(
                        $torneo,
                        $emparejamiento['local'],
                        $emparejamiento['visitante'],
                        $dia['fecha'],
                        $horaActual,
                        'Fase Regular'
                    );
                    
                    Log::info('Juego programado', [
                        'juego_id' => $juego->id,
                        'fecha' => $dia['fecha']->format('Y-m-d'),
                        'hora' => $horaActual->format('H:i'),
                        'equipo_local' => $emparejamiento['equipo_local_nombre'],
                        'equipo_visitante' => $emparejamiento['equipo_visitante_nombre']
                    ]);
                    
                    // Avanzar el horario
                    $horaActual->addMinutes($duracionTotal + $torneo->tiempo_entre_partidos_minutos);
                    $juegosEsteDia++;
                    $juegosCreados++;
                } else {
                    // No cabe más en este día, devolver el emparejamiento al array
                    array_unshift($emparejamientos, $emparejamiento);
                    break;
                }
            }
            
            Log::info('Juegos programados para día', [
                'fecha' => $dia['fecha']->format('Y-m-d'),
                'juegos_programados' => $juegosEsteDia,
                'juegos_restantes' => count($emparejamientos)
            ]);
        }
        
        // 6. Manejar emparejamientos restantes (si los hay)
        if (!empty($emparejamientos)) {
            Log::warning('Quedan emparejamientos por programar', [
                'emparejamientos_restantes' => count($emparejamientos)
            ]);
            $this->distribuirJuegosRestantes($torneo, $emparejamientos, $temporada);
        }
        
        Log::info('Juegos todos contra todos creados', [
            'torneo_id' => $torneo->id,
            'total_juegos' => $juegosCreados,
            'juegos_por_equipo' => $equipos->count() - 1
        ]);
        
        return $juegosCreados;
    }

    /**
     * Distribuye juegos que no cupieron en la distribución inicial
     */
    private function distribuirJuegosRestantes(Torneo $torneo, array $emparejamientos, Temporada $temporada): void
    {
        Log::info('Intentando distribuir juegos restantes', [
            'juegos_restantes' => count($emparejamientos)
        ]);
        
        $diasDisponibles = $this->obtenerDiasHabilesEnRango($torneo, $temporada);
        $juegosCreados = 0;
        
        foreach ($diasDisponibles as $dia) {
            if (empty($emparejamientos)) break;
            
            $horario = $this->obtenerHorarioDelDia($dia['fecha'], $dia['diasHabiles'], $temporada);
            $horaActual = $horario['inicio']->copy();
            
            while (!empty($emparejamientos)) {
                $emparejamiento = $emparejamientos[0];
                $duracionTotal = $this->calcularDuracionTotalJuego($torneo);
                $horaFinJuego = $horaActual->copy()->addMinutes($duracionTotal);
                
                if ($horaFinJuego->lte($horario['fin'])) {
                    // Crear el juego
                    $juego = $this->crearJuego(
                        $torneo,
                        $emparejamiento['local'],
                        $emparejamiento['visitante'],
                        $dia['fecha'],
                        $horaActual,
                        'Fase Regular'
                    );
                    
                    Log::info('Juego restante programado', [
                        'juego_id' => $juego->id,
                        'fecha' => $dia['fecha']->format('Y-m-d'),
                        'hora' => $horaActual->format('H:i'),
                        'equipo_local' => $emparejamiento['equipo_local_nombre'],
                        'equipo_visitante' => $emparejamiento['equipo_visitante_nombre']
                    ]);
                    
                    // Avanzar el horario y quitar el emparejamiento
                    $horaActual->addMinutes($duracionTotal + $torneo->tiempo_entre_partidos_minutos);
                    array_shift($emparejamientos);
                    $juegosCreados++;
                } else {
                    break; // Pasar al siguiente día
                }
            }
        }
        
        if (!empty($emparejamientos)) {
            Log::error('No se pudieron programar todos los juegos', [
                'juegos_sin_programar' => count($emparejamientos)
            ]);
            throw new \Exception('No se pudieron programar todos los juegos dentro del rango de fechas');
        }
        
        Log::info('Juegos restantes distribuidos', [
            'juegos_creados' => $juegosCreados
        ]);
    }

    /**
     * Inicializa la programación de juegos
     */
    private function inicializarProgramacion(Torneo $torneo, Temporada $temporada)
    {
        // Obtener días hábiles de la temporada
        $diasHabiles = $this->obtenerDiasHabiles($temporada->id);
        
        // Encontrar el primer día hábil válido desde la fecha de inicio
        $fechaInicio = Carbon::parse($torneo->fecha_inicio);
        $primerDiaHabil = $this->encontrarSiguienteDiaHabil($fechaInicio, $diasHabiles);
        
        // Obtener horario para el primer día hábil
        $horarioDelDia = $this->obtenerHorarioDelDia($primerDiaHabil, $diasHabiles, $temporada);
        
        return [
            'fecha_actual' => $primerDiaHabil,
            'hora_actual' => $horarioDelDia['inicio'],
            'horario_inicio_default' => Carbon::parse($temporada->horario_inicio),
            'horario_fin_default' => Carbon::parse($temporada->horario_fin),
            'fecha_fin_torneo' => Carbon::parse($torneo->fecha_fin),
            'dias_habiles' => $diasHabiles
        ];
    }

    /**
     * Obtiene la siguiente fecha y hora disponible para un juego
     */
    private function obtenerSiguienteFechaHora(&$programacion, Torneo $torneo, Temporada $temporada)
    {
        // Calcular duración total de un juego (en minutos)
        $duracionJuego = $this->calcularDuracionTotalJuego($torneo);
        
        // Obtener horario del día actual
        $horarioDelDia = $this->obtenerHorarioDelDia(
            $programacion['fecha_actual'], 
            $programacion['dias_habiles'], 
            $temporada
        );
        
        // Verificar si el juego cabe en el horario del día actual
        $horaFinJuego = $programacion['hora_actual']->copy()->addMinutes($duracionJuego);
        
        // Si el juego se pasa del horario permitido, mover al siguiente día hábil
        if ($horaFinJuego->greaterThan($horarioDelDia['fin'])) {
            Log::info('Cambiando al siguiente día hábil por tiempo excedido', [
                'fecha_actual' => $programacion['fecha_actual']->format('Y-m-d'),
                'hora_actual' => $programacion['hora_actual']->format('H:i'),
                'hora_fin_juego' => $horaFinJuego->format('H:i'),
                'horario_limite' => $horarioDelDia['fin']->format('H:i')
            ]);
            
            $programacion['fecha_actual'] = $this->encontrarSiguienteDiaHabil(
                $programacion['fecha_actual']->copy()->addDay(), 
                $programacion['dias_habiles']
            );
            
            // Actualizar horario para el nuevo día
            $nuevoHorarioDelDia = $this->obtenerHorarioDelDia(
                $programacion['fecha_actual'], 
                $programacion['dias_habiles'], 
                $temporada
            );
            $programacion['hora_actual'] = $nuevoHorarioDelDia['inicio']->copy();
            
            Log::info('Cambiado a nuevo día hábil', [
                'nueva_fecha' => $programacion['fecha_actual']->format('Y-m-d'),
                'nueva_hora' => $programacion['hora_actual']->format('H:i')
            ]);
            
            // Verificar que no nos pasemos de la fecha fin del torneo
            if ($programacion['fecha_actual']->greaterThan($programacion['fecha_fin_torneo'])) {
                Log::warning('La programación de juegos excede la fecha fin del torneo', [
                    'fecha_calculada' => $programacion['fecha_actual']->format('Y-m-d'),
                    'fecha_fin_torneo' => $programacion['fecha_fin_torneo']->format('Y-m-d')
                ]);
            }
        }
        
        return [
            'fecha' => $programacion['fecha_actual']->copy(),
            'hora' => $programacion['hora_actual']->copy()
        ];
    }

    /**
     * Actualiza la programación después de crear un juego
     */
    private function actualizarProgramacion(&$programacion, Torneo $torneo, Temporada $temporada)
    {
        // Calcular duración total de un juego + tiempo entre partidos
        $duracionTotal = $this->calcularDuracionTotalJuego($torneo) + $torneo->tiempo_entre_partidos_minutos;
        
        // Avanzar la hora actual
        $programacion['hora_actual']->addMinutes($duracionTotal);
        
        Log::debug('Programación actualizada', [
            'nueva_hora' => $programacion['hora_actual']->format('H:i'),
            'duracion_utilizada' => $duracionTotal
        ]);
    }

    /**
     * Calcula la duración total de un juego en minutos
     */
    private function calcularDuracionTotalJuego(Torneo $torneo)
    {
        // Asumiendo 4 cuartos + 3 descansos de 5 minutos cada uno
        $cuartos = 4;
        $descansos = 3;
        $duracionDescanso = 5; // minutos
        
        $duracionTotal = ($cuartos * $torneo->duracion_cuarto_minutos) + ($descansos * $duracionDescanso);
        
        Log::debug('Duración calculada del juego', [
            'cuartos' => $cuartos,
            'duracion_cuarto' => $torneo->duracion_cuarto_minutos,
            'descansos' => $descansos,
            'duracion_descanso' => $duracionDescanso,
            'duracion_total' => $duracionTotal
        ]);
        
        return $duracionTotal;
    }

    /**
     * Obtiene los días hábiles configurados para la temporada
     */
    private function obtenerDiasHabiles($temporadaId)
    {
        $diasHabiles = DB::table('temporada_dias_habiles')
            ->where('temporada_id', $temporadaId)
            ->where('activo', true)
            ->orderBy('dia_semana')
            ->get()
            ->keyBy('dia_semana');

        if ($diasHabiles->isEmpty()) {
            Log::warning('No se encontraron días hábiles para la temporada, usando lunes a viernes por defecto', [
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
        }

        Log::info('Días hábiles obtenidos', [
            'temporada_id' => $temporadaId,
            'dias_disponibles' => $diasHabiles->keys()->toArray()
        ]);

        return $diasHabiles;
    }

    /**
     * Encuentra el siguiente día hábil desde una fecha dada
     */
    private function encontrarSiguienteDiaHabil(Carbon $fecha, $diasHabiles)
    {
        $maxIntentos = 14; // Para evitar bucles infinitos
        $intentos = 0;
        
        while ($intentos < $maxIntentos) {
            $diaSemana = $fecha->dayOfWeekIso; // 1=Lunes, 2=Martes, ..., 7=Domingo
            
            if ($diasHabiles->has($diaSemana)) {
                Log::debug('Día hábil encontrado', [
                    'fecha' => $fecha->format('Y-m-d'),
                    'dia_semana' => $diaSemana,
                    'nombre_dia' => $fecha->locale('es')->dayName
                ]);
                return $fecha;
            }
            
            $fecha->addDay();
            $intentos++;
        }
        
        throw new \Exception('No se pudo encontrar un día hábil válido en las próximas 2 semanas');
    }

    /**
     * Obtiene el horario específico para un día, considerando horarios personalizados
     */
    private function obtenerHorarioDelDia(Carbon $fecha, $diasHabiles, Temporada $temporada): array
    {
        $diaSemana = $fecha->dayOfWeekIso;
        $diaHabil = $diasHabiles->get($diaSemana);
        
        if (!$diaHabil) {
            throw new \Exception("El día {$fecha->format('Y-m-d')} no es un día hábil válido");
        }
        
        // Horarios por defecto de la temporada
        $horarioInicioDefault = Carbon::parse($temporada->horario_inicio);
        $horarioFinDefault = Carbon::parse($temporada->horario_fin);
        
        // Determinar horarios para este día
        $horarioInicio = $diaHabil->horario_inicio 
            ? Carbon::parse($diaHabil->horario_inicio)
            : $horarioInicioDefault->copy();
            
        $horarioFin = $diaHabil->horario_fin
            ? Carbon::parse($diaHabil->horario_fin)
            : $horarioFinDefault->copy();
        
        // Validar coherencia de horarios
        if ($horarioInicio->gte($horarioFin)) {
            Log::warning('Horario inválido para día, usando horario por defecto', [
                'fecha' => $fecha->format('Y-m-d'),
                'horario_inicio' => $horarioInicio->format('H:i'),
                'horario_fin' => $horarioFin->format('H:i')
            ]);
            $horarioInicio = $horarioInicioDefault->copy();
            $horarioFin = $horarioFinDefault->copy();
        }
        
        // Asegurar que el horario no exceda el horario por defecto
        if ($horarioInicio->lt($horarioInicioDefault)) {
            $horarioInicio = $horarioInicioDefault->copy();
        }
        
        if ($horarioFin->gt($horarioFinDefault)) {
            $horarioFin = $horarioFinDefault->copy();
        }
        
        Log::debug('Horario determinado para día', [
            'fecha' => $fecha->format('Y-m-d'),
            'dia_semana' => $diaSemana,
            'horario_inicio' => $horarioInicio->format('H:i'),
            'horario_fin' => $horarioFin->format('H:i'),
            'es_personalizado' => $diaHabil->horario_inicio !== null
        ]);
        
        return [
            'inicio' => $horarioInicio,
            'fin' => $horarioFin
        ];
    }

    /**
     * Crea un juego individual
     */
    private function crearJuego(Torneo $torneo, $equipoLocalId, $equipoVisitanteId, $fecha, $hora, $fase = 'Fase Regular')
    {
        return Juego::create([
            'liga_id' => $torneo->liga_id,
            'temporada_id' => $torneo->temporada_id,
            'torneo_id' => $torneo->id,
            'equipo_local_id' => $equipoLocalId,
            'equipo_visitante_id' => $equipoVisitanteId,
            'cancha_id' => $torneo->cancha_id,
            'fecha' => $fecha->format('Y-m-d'),
            'hora' => $hora->format('H:i:s'),
            'duracion_cuarto' => $torneo->duracion_cuarto_minutos,
            'duracion_descanso' => 5, // Valor por defecto
            'estado' => 'Programado',
            'fase' => $fase,
            'activo' => true,
            'observaciones' => "Generado automáticamente - Torneo por puntos"
        ]);
    }


    /**
     * Crea un juego BYE (pase automático)
     */
    private function crearJuegoBye(Torneo $torneo, $equipoId)
    {
        return Juego::create([
            'liga_id' => $torneo->liga_id,
            'temporada_id' => $torneo->temporada_id,
            'torneo_id' => $torneo->id,
            'equipo_local_id' => $equipoId,
            'equipo_visitante_id' => null,
            'cancha_id' => $torneo->cancha_id,
            'fecha' => $torneo->fecha_inicio,
            'hora' => '10:00',
            'duracion_cuarto' => $torneo->duracion_cuarto_minutos,
            'estado' => 'Programado',
            'fase' => 'Fase Regular', // <-- Añadido para consistencia
            'activo' => true,
            'puntos_local' => 0,
            'puntos_visitante' => 0,
            'observaciones' => "Generado automáticamente - Torneo por puntos - BYE (Pase automático)"
        ]);
    }


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