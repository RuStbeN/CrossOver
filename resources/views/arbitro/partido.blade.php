@extends('layouts.arbitro')
@section('content')

<!-- Agregar el meta tag del token CSRF -->
@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

<main class="relative z-10 py-4">
    <div class="w-full mx-auto">
        <!-- Contenedor horizontal mejorado -->
        <div class="bg-gradient-to-r from-white to-gray-50 dark:from-dark-800 dark:to-dark-900 rounded-xl shadow-lg border border-gray-200 dark:border-dark-600 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                
                <!-- Columna 1: Controles del temporizador -->
                @if($juego->estado === 'En Curso')
                <div class="flex items-center space-x-2">
                    <!-- Botón Iniciar -->
                    <button id="btn-iniciar" 
                            class="group relative p-2.5 bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 
                                   disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed 
                                   text-white rounded-lg transition-all duration-300 transform hover:scale-105 
                                   shadow-md hover:shadow-lg border border-green-400/30 disabled:border-gray-400/30"
                            {{ ($juego->estado_tiempo ?? 'pausado') === 'corriendo' ? 'disabled' : '' }}
                            title="Iniciar tiempo">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    
                    <!-- Botón Pausar -->
                    <button id="btn-pausar" 
                            class="group relative p-2.5 bg-gradient-to-br from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 
                                   disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed 
                                   text-white rounded-lg transition-all duration-300 transform hover:scale-105 
                                   shadow-md hover:shadow-lg border border-yellow-400/30 disabled:border-gray-400/30"
                            {{ ($juego->estado_tiempo ?? 'pausado') !== 'corriendo' ? 'disabled' : '' }}
                            title="Pausar tiempo">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    
                    <!-- Botón Reiniciar -->
                    <button id="btn-reiniciar" 
                            class="group relative p-2.5 bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 
                                   text-white rounded-lg transition-all duration-300 transform hover:scale-105 
                                   shadow-md hover:shadow-lg border border-blue-400/30"
                            title="Reiniciar cuarto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    
                    <!-- Botón Avanzar -->
                    <button id="btn-avanzar" 
                            class="group relative p-2.5 bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 
                                   text-white rounded-lg transition-all duration-300 transform hover:scale-105 
                                   shadow-md hover:shadow-lg border border-purple-400/30"
                            title="Avanzar cuarto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                @endif
                
                <!-- Columna 2: Temporizador, cuarto y estado del tiempo centrados -->
                <div class="flex flex-col items-center space-y-2">
                    <!-- Temporizador estilo deportivo mejorado -->
                    <div class="relative">
                        <div class="bg-black dark:bg-gray-900 rounded-lg shadow-2xl border-4 border-gray-800 dark:border-gray-700 px-6 py-2">
                            <!-- Display principal del tiempo -->
                            <div class="text-5xl font-mono font-black text-green-400 dark:text-green-300 tracking-wider leading-none drop-shadow-lg" id="temporizador" style="text-shadow: 0 0 20px rgba(34, 197, 94, 0.5);">
                                {{ sprintf('%02d:%02d', floor(($juego->tiempo_restante ?? $juego->duracion_cuarto * 60) / 60), ($juego->tiempo_restante ?? $juego->duracion_cuarto * 60) % 60) }}
                            </div>
                            <!-- Línea decorativa inferior -->
                            <div class="h-1 bg-gradient-to-r from-green-400 to-green-500 dark:from-green-300 dark:to-green-400 rounded-full mt-1"></div>
                        </div>
                    </div>
                    
                    <!-- Información del partido estilo tablero deportivo -->
                    <div class="flex items-center space-x-4">
                        <!-- Cuarto actual estilo tablero -->
                        <div id="cuarto-actual" class="flex items-center">
                            @if($juego->en_descanso ?? false)
                                <div class="bg-orange-500 dark:bg-orange-600 text-white px-3 py-1.5 rounded-lg text-sm font-bold tracking-wide shadow-lg border-2 border-orange-600 dark:border-orange-700">
                                    <div class="flex items-center space-x-1">
                                        <span class="text-sm">Cuarto</span>
                                        <span class="text-lg font-black">{{ $juego->cuarto_actual ?? 1 }}</span>
                                        <span class="text-xs bg-orange-600 dark:bg-orange-700 px-1.5 py-0.5 rounded text-white">DESCANSO</span>
                                    </div>
                                </div>
                            @else
                                <div class="bg-blue-600 dark:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-sm font-bold tracking-wide shadow-lg border-2 border-blue-700 dark:border-blue-800">
                                    <div class="flex items-center space-x-1">
                                        <span class="text-sm">Cuarto</span>
                                        <span class="text-lg font-black">{{ $juego->cuarto_actual ?? 1 }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Estado del tiempo estilo LED -->
                        @if($juego->estado === 'En Curso')
                        <div id="estado-tiempo" class="flex items-center">
                            <div class="
                                @if(($juego->en_descanso ?? false))
                                    bg-orange-100 dark:bg-orange-900/50 border-orange-300 dark:border-orange-700 text-orange-800 dark:text-orange-200
                                @elseif(($juego->estado_tiempo ?? 'pausado') === 'corriendo')
                                    bg-green-100 dark:bg-green-900/50 border-green-300 dark:border-green-700 text-green-800 dark:text-green-200
                                @elseif(($juego->estado_tiempo ?? 'pausado') === 'pausado')
                                    bg-red-100 dark:bg-red-900/50 border-red-300 dark:border-red-700 text-red-800 dark:text-red-200
                                @endif
                                px-3 py-1 rounded-md text-xs font-bold tracking-wider border-2 shadow-lg">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 rounded-full mr-2
                                        @if($juego->en_descanso ?? false)
                                            bg-orange-500 dark:bg-orange-400
                                        @elseif(($juego->estado_tiempo ?? 'pausado') === 'corriendo')
                                            bg-green-500 dark:bg-green-400 animate-pulse
                                        @elseif(($juego->estado_tiempo ?? 'pausado') === 'pausado')
                                            bg-red-500 dark:bg-red-400
                                        @endif">
                                    </div>
                                    @if($juego->en_descanso ?? false)
                                        DESCANSO
                                    @elseif(($juego->estado_tiempo ?? 'pausado') === 'corriendo')
                                        CORRIENDO
                                    @elseif(($juego->estado_tiempo ?? 'pausado') === 'pausado')
                                        PAUSADO
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Columna 3: Estado del partido y botón de acción -->
                <div class="flex flex-col items-center space-y-2">
                    <!-- Estado del partido mejorado -->
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold shadow-md border transition-all duration-300
                        @if($juego->estado === 'Programado')
                            bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border-yellow-300
                            dark:from-yellow-900/40 dark:to-yellow-800/40 dark:text-yellow-200 dark:border-yellow-600/50
                        @else
                            bg-gradient-to-r from-green-100 to-green-200 text-green-800 border-green-300
                            dark:from-green-900/40 dark:to-green-800/40 dark:text-green-200 dark:border-green-600/50
                        @endif">
                        <div class="w-2 h-2 rounded-full mr-2 animate-pulse
                            @if($juego->estado === 'Programado')
                                bg-yellow-500 dark:bg-yellow-400
                            @else
                                bg-green-500 dark:bg-green-400
                            @endif">
                        </div>
                        @if($juego->estado === 'Programado')
                            Partido Programado
                        @else
                            Partido En Curso
                        @endif
                    </span>
                    
                    <!-- Botón de acción principal debajo del estado -->
                    @if($juego->estado === 'Programado')
                        <button onclick="iniciarPartidoOficial()" 
                                class="group relative px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 
                                       text-white rounded-lg text-sm font-semibold transition-all duration-300 transform hover:scale-105 
                                       shadow-md hover:shadow-lg border border-green-500/30">
                            <span class="relative z-10 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                </svg>
                                Iniciar
                            </span>
                        </button>
                    @else
                        <button onclick="finalizarPartidoOficial()" 
                                class="group relative px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 
                                       text-white rounded-lg text-sm font-semibold transition-all duration-300 transform hover:scale-105 
                                       shadow-md hover:shadow-lg border border-red-500/30">
                            <span class="relative z-10 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                                </svg>
                                Finalizar
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- Jugadores en dos columnas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mx-2 mt-6">
        <!-- Equipo Local -->
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow border border-dark-100 dark:border-dark-700 overflow-hidden">
            <div class="px-4 py-2 border-b border-gray-200 dark:border-dark-700 bg-blue-50 dark:bg-blue-900/20">
                <h3 class="font-bold text-center">{{ $juego->equipoLocal->nombre }}</h3>
            </div>
            <div class="p-2 space-y-2">
                @if($juego->estado === 'Programado' || $juego->juegoAlineaciones->where('tipo_equipo', 'Local')->count() > 0)
                    @if($juego->estado === 'Programado')
                        @foreach($titularesLocalSeleccionados as $jugador)
                            @include('arbitro.partials.jugador-card', [
                                'jugador' => $jugador,
                                'estado' => 'Programado',
                                'tipoEquipo' => 'Local'
                            ])
                        @endforeach
                    @else
                        @foreach($juego->juegoAlineaciones->where('tipo_equipo', 'Local') as $alineacion)
                            @include('arbitro.partials.jugador-card', [
                                'alineacion' => $alineacion,
                                'estado' => 'En Curso',
                                'tipoEquipo' => 'Local'
                            ])
                        @endforeach
                    @endif
                @else
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        <p>No hay jugadores seleccionados</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Equipo Visitante -->
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow border border-dark-100 dark:border-dark-700 overflow-hidden">
            <div class="px-4 py-2 border-b border-gray-200 dark:border-dark-700 bg-red-50 dark:bg-red-900/20">
                <h3 class="font-bold text-center">{{ $juego->equipoVisitante->nombre }}</h3>
            </div>
            <div class="p-2 space-y-2">
                @if($juego->estado === 'Programado' || $juego->juegoAlineaciones->where('tipo_equipo', 'Visitante')->count() > 0)
                    @if($juego->estado === 'Programado')
                        @foreach($titularesVisitanteSeleccionados as $jugador)
                            @include('arbitro.partials.jugador-card', [
                                'jugador' => $jugador,
                                'estado' => 'Programado',
                                'tipoEquipo' => 'Visitante'
                            ])
                        @endforeach
                    @else
                        @foreach($juego->juegoAlineaciones->where('tipo_equipo', 'Visitante') as $alineacion)
                            @include('arbitro.partials.jugador-card', [
                                'alineacion' => $alineacion,
                                'estado' => 'En Curso',
                                'tipoEquipo' => 'Visitante'
                            ])
                        @endforeach
                    @endif
                @else
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        <p>No hay jugadores seleccionados</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('arbitro.partials.modal-rebotes')
    @include('arbitro.partials.modal-faltas')


    <!-- Script para pasar juegoId al JavaScript -->
    <script>
        window.juegoId = {{ $juego->id }};
    </script>
</main>

<script>
    // Variables globales
    const juegoId = @json($juego->id);
    const duracionCuarto = @json($juego->duracion_cuarto ?? 10); // En minutos
    let tiempoRestante = duracionCuarto * 60; // Convertir a segundos
    let temporizadorInterval = null;
    let juegoEnCurso = @json($juego->estado === 'En Curso');
    let modalData = {};
    let requestEnProceso = false;

    // Inicializar temporizador al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        actualizarTemporizador();
    });

    // Función simplificada para iniciar el partido (solo cambiar estado y empezar cronómetro)
    function iniciarPartidoOficial() {
        // Obtener IDs de jugadores seleccionados
        const titularesLocalIds = @json($titularesLocalSeleccionados->pluck('id')->toArray());
        const titularesVisitanteIds = @json($titularesVisitanteSeleccionados->pluck('id')->toArray());

        if (confirm('¿Estás seguro de que quieres iniciar el partido oficialmente?')) {
            fetch(`/arbitro/partidos/${juegoId}/iniciar-oficial`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    titulares_local: titularesLocalIds,
                    titulares_visitante: titularesVisitanteIds
                })
            })
            .then(async response => {
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error al iniciar partido');
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    location.reload(); // Recargar para ver los cambios
                } else {
                    alert(data.message || 'Error al iniciar el partido');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Error al iniciar el partido');
            });
        }
    }

    // Nueva función unificada para registrar acciones (puntos y faltas)
    function registrarAccion(jugadorId, equipoId, tipoEquipo, tipoAccion, valor) {
        fetch(`/api/juegos/${juegoId}/registrar-accion`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                jugador_id: jugadorId,
                equipo_id: equipoId,
                tipo_equipo: tipoEquipo,
                tipo_accion: tipoAccion, // 'punto' o 'falta'
                valor: valor
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Actualizar la UI sin recargar toda la página
                actualizarPuntajesEnTiempoReal(data);
            } else {
                alert(data.error || 'Error al registrar la acción');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al registrar la acción');
        });
    }

    // Función para actualizar puntajes en tiempo real
    function actualizarPuntajesEnTiempoReal(data) {
        if (data.puntos_jugador !== undefined) {
            // Buscar y actualizar el puntaje del jugador específico
            const jugadorElements = document.querySelectorAll('[data-jugador-id="' + data.jugador_id + '"]');
            jugadorElements.forEach(element => {
                const puntosElement = element.querySelector('.puntos-jugador');
                if (puntosElement) {
                    puntosElement.textContent = data.puntos_jugador ;
                }
            });
        }
        
        if (data.puntos_equipo !== undefined) {
            // Actualizar puntaje del equipo en el header
            if (data.tipo_equipo === 'Local') {
                const puntosLocalElement = document.querySelector('.puntos-local');
                if (puntosLocalElement) {
                    puntosLocalElement.textContent = data.puntos_equipo;
                }
            } else {
                const puntosVisitanteElement = document.querySelector('.puntos-visitante');
                if (puntosVisitanteElement) {
                    puntosVisitanteElement.textContent = data.puntos_equipo;
                }
            }
        }
    }

    // Funciones del temporizador
    function iniciarTemporizador() {
        if (temporizadorInterval) return; // Ya está corriendo
        
        temporizadorInterval = setInterval(function() {
            if (tiempoRestante > 0) {
                tiempoRestante--;
                actualizarTemporizador();
            } else {
                pausarTemporizador();
                alert('¡Tiempo terminado!');
            }
        }, 1000);
    }

    function pausarTemporizador() {
        if (temporizadorInterval) {
            clearInterval(temporizadorInterval);
            temporizadorInterval = null;
        }
    }

    function reiniciarTemporizador() {
        pausarTemporizador();
        tiempoRestante = duracionCuarto * 60;
        actualizarTemporizador();
    }

    function actualizarTemporizador() {
        const minutos = Math.floor(tiempoRestante / 60);
        const segundos = tiempoRestante % 60;
        const tiempoFormateado = `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
        
        const temporizadorElement = document.getElementById('temporizador');
        if (temporizadorElement) {
            temporizadorElement.textContent = tiempoFormateado;
        }
    }

    // CAMBIO 1: Modificar la función controlarTiempo para eliminar la lógica de finalización automática
    async function controlarTiempo(accion) {
        // Prevenir múltiples requests simultáneos
        if (requestEnProceso) {
            console.log('Request ya en proceso, ignorando...');
            return;
        }
        
        requestEnProceso = true;
        console.log(`Intentando ${accion}...`);
        
        // Deshabilitar botones temporalmente
        deshabilitarBotones(true);
        
        try {
            const url = `/api/juegos/${juegoId}/tiempo`;
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ accion })
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                let errorMessage = `HTTP error! status: ${response.status}`;
                try {
                    const errorData = await response.json();
                    if (errorData.error) {
                        errorMessage = errorData.error;
                    }
                    console.log('Error data:', errorData);
                } catch (jsonError) {
                    console.log('Could not parse error as JSON');
                }
                throw new Error(errorMessage);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Respuesta no es JSON:', text);
                throw new Error('El servidor no devolvió una respuesta JSON válida');
            }
            
            const data = await response.json();
            console.log('Success response:', data);
            
            if (data.success) {
                // Sincronizar tiempo con servidor
                actualizarUI(data);
                actualizarEstadoBotones(data.estado_tiempo);
                
                // CAMBIO: Eliminar esta sección que finalizaba automáticamente
                // if (data.estado_partido === 'Finalizado') {
                //     alert('¡Partido finalizado!');
                //     location.reload();
                //     return;
                // }
                
                // AGREGAR: Mostrar botón de finalizar cuando el partido esté listo para finalizar
                if (data.puede_finalizar) {
                    mostrarBotonFinalizar();
                }
                
                // Manejar temporizador local correctamente
                if (data.estado_tiempo === 'corriendo') {
                    console.log('Iniciando temporizador local con tiempo:', data.tiempo_restante);
                    detenerTemporizadorLocal();
                    iniciarTemporizadorLocal(data.tiempo_restante);
                } else {
                    console.log('Deteniendo temporizador local');
                    detenerTemporizadorLocal();
                    // Actualizar display con tiempo exacto del servidor
                    const temporizador = document.getElementById('temporizador');
                    if (temporizador) {
                        temporizador.textContent = formatearTiempo(data.tiempo_restante);
                    }
                }
            } else {
                throw new Error(data.error || 'Error al controlar el tiempo');
            }
        } catch (error) {
            console.error('Error completo:', error);
            alert(`Error: ${error.message}`);
        } finally {
            requestEnProceso = false;
            // Re-habilitar botones después del request
            setTimeout(() => {
                deshabilitarBotones(false);
            }, 100);
        }
    }

    // CAMBIO 2: Agregar función para mostrar el botón de finalizar
    function mostrarBotonFinalizar() {
        // Verificar si el botón ya existe
        let btnFinalizar = document.getElementById('btn-finalizar');
        
        if (!btnFinalizar) {
            // Crear el botón de finalizar
            btnFinalizar = document.createElement('button');
            btnFinalizar.id = 'btn-finalizar';
            btnFinalizar.className = 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors';
            btnFinalizar.textContent = 'Finalizar Partido';
            btnFinalizar.onclick = finalizarPartidoOficial;
            
            // Agregar el botón al contenedor de controles de tiempo
            const controlesContainer = document.querySelector('.controles-tiempo') || 
                                    document.querySelector('.flex.gap-2') ||
                                    document.getElementById('btn-avanzar').parentElement;
            
            if (controlesContainer) {
                controlesContainer.appendChild(btnFinalizar);
            }
        }
        
        // Mostrar el botón
        btnFinalizar.style.display = 'block';
    }

    // CAMBIO 3: Agregar función para finalizar el partido usando tu función existente
    function finalizarPartidoOficial() {
        if (confirm('¿Estás seguro de que quieres finalizar el partido? Esta acción no se puede deshacer.')) {
            // Crear un formulario para enviar la petición POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/arbitro/partidos/${juegoId}/finalizar`;
            
            // Agregar token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Agregar al DOM y enviar
            document.body.appendChild(form);
            form.submit();
        }
    }

    // FUNCIÓN CORREGIDA para deshabilitar/habilitar botones
    function deshabilitarBotones(deshabilitar) {
        const botones = ['btn-iniciar', 'btn-pausar', 'btn-reiniciar', 'btn-avanzar'];
        botones.forEach(btnId => {
            const btn = document.getElementById(btnId);
            if (btn) {
                if (deshabilitar) {
                    btn.disabled = true;
                    btn.style.opacity = '0.6';
                    btn.style.pointerEvents = 'none';
                } else {
                    btn.style.opacity = '1';
                    btn.style.pointerEvents = 'auto';
                    // El estado disabled se actualizará en actualizarEstadoBotones
                }
            }
        });
    }

    // Función de sincronización - MEJORADA
    async function sincronizarEstadoTiempo() {
        // No sincronizar si hay un request en proceso
        if (requestEnProceso) {
            return;
        }
        
        try {
            const url = `/api/juegos/${juegoId}/estado-tiempo`;
            
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                }
            });
            
            if (!response.ok) {
                console.error(`HTTP error! status: ${response.status}`);
                return;
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                console.error('Respuesta no es JSON en sincronización');
                return;
            }
            
            const data = await response.json();
            console.log('Estado sincronizado:', data);
            
            if (data.success) {
                actualizarUI(data);
                actualizarEstadoBotones(data.estado_tiempo);
                
                // CORRECCIÓN: Sincronizar temporizador solo si no está corriendo localmente
                if (data.estado_tiempo === 'corriendo' && !temporizadorInterval) {
                    console.log('Servidor dice que está corriendo, iniciando temporizador local');
                    iniciarTemporizadorLocal(data.tiempo_restante);
                } else if (data.estado_tiempo !== 'corriendo' && temporizadorInterval) {
                    console.log('Servidor dice que no está corriendo, deteniendo temporizador local');
                    detenerTemporizadorLocal();
                    // Actualizar con tiempo exacto del servidor
                    const temporizador = document.getElementById('temporizador');
                    if (temporizador) {
                        temporizador.textContent = formatearTiempo(data.tiempo_restante);
                    }
                }
            }
        } catch (error) {
            console.error('Error al sincronizar estado del tiempo:', error);
        }
    }

    // Función para actualizar la UI
    function actualizarUI(data) {
        // Actualizar temporizador
        const temporizadorElement = document.getElementById('temporizador');
        if (temporizadorElement) {
            temporizadorElement.textContent = formatearTiempo(data.tiempo_restante);
        }
        
        // Actualizar cuarto actual manteniendo la estructura HTML original
        const cuartoElement = document.getElementById('cuarto-actual');
        if (cuartoElement) {
            if (data.en_descanso) {
                cuartoElement.innerHTML = `
                    <div class="bg-orange-500 dark:bg-orange-600 text-white px-3 py-1.5 rounded-lg text-sm font-bold tracking-wide shadow-lg border-2 border-orange-600 dark:border-orange-700">
                        <div class="flex items-center space-x-1">
                            <span class="text-sm">Cuarto</span>
                            <span class="text-lg font-black">${data.cuarto_actual}</span>
                            <span class="text-xs bg-orange-600 dark:bg-orange-700 px-1.5 py-0.5 rounded text-white">DESCANSO</span>
                        </div>
                    </div>
                `;
            } else {
                cuartoElement.innerHTML = `
                    <div class="bg-blue-600 dark:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-sm font-bold tracking-wide shadow-lg border-2 border-blue-700 dark:border-blue-800">
                        <div class="flex items-center space-x-1">
                            <span class="text-sm">Cuarto</span>
                            <span class="text-lg font-black">${data.cuarto_actual}</span>
                        </div>
                    </div>
                `;
            }
        }

        // Actualizar estado del tiempo (CORRECCIÓN IMPORTANTE)
        const estadoTiempoElement = document.getElementById('estado-tiempo');
        if (estadoTiempoElement) {
            let estadoHtml = '';
            if (data.en_descanso) {
                estadoHtml = `
                    <div class="bg-orange-100 dark:bg-orange-900/50 border-orange-300 dark:border-orange-700 text-orange-800 dark:text-orange-200 px-3 py-1 rounded-md text-xs font-bold tracking-wider border-2 shadow-lg">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full mr-2 bg-orange-500 dark:bg-orange-400"></div>
                            DESCANSO
                        </div>
                    </div>
                `;
            } else if (data.estado_tiempo === 'corriendo') {
                estadoHtml = `
                    <div class="bg-green-100 dark:bg-green-900/50 border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 px-3 py-1 rounded-md text-xs font-bold tracking-wider border-2 shadow-lg">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full mr-2 bg-green-500 dark:bg-green-400 animate-pulse"></div>
                            CORRIENDO
                        </div>
                    </div>
                `;
            } else {
                estadoHtml = `
                    <div class="bg-red-100 dark:bg-red-900/50 border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 px-3 py-1 rounded-md text-xs font-bold tracking-wider border-2 shadow-lg">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full mr-2 bg-red-500 dark:bg-red-400"></div>
                            PAUSADO
                        </div>
                    </div>
                `;
            }
            estadoTiempoElement.innerHTML = estadoHtml;
        }
    }

    // Función para formatear el tiempo
    function formatearTiempo(segundos) {
        const minutos = Math.floor(segundos / 60);
        const segs = segundos % 60;
        return `${minutos.toString().padStart(2, '0')}:${segs.toString().padStart(2, '0')}`;
    }

    // FUNCIÓN CORREGIDA para actualizar el estado de los botones
    function actualizarEstadoBotones(estadoTiempo) {
        const btnIniciar = document.getElementById('btn-iniciar');
        const btnPausar = document.getElementById('btn-pausar');
        const btnReiniciar = document.getElementById('btn-reiniciar');
        const btnAvanzar = document.getElementById('btn-avanzar');
        const estadoElement = document.getElementById('estado-tiempo');
        
        if (btnIniciar && btnPausar && btnReiniciar && btnAvanzar) {
            // CORRECCIÓN: Lógica de botones mejorada
            btnIniciar.disabled = estadoTiempo === 'corriendo';
            btnPausar.disabled = estadoTiempo !== 'corriendo';
            btnReiniciar.disabled = false; // Siempre habilitado
            btnAvanzar.disabled = estadoTiempo === 'corriendo'; // Solo deshabilitar si está corriendo
        }
        
        if (estadoElement) {
            const span = estadoElement.querySelector('span');
            if (span) {
                // Limpiar clases existentes
                span.className = 'px-3 py-1 rounded-full text-sm font-medium ';
                
                if (estadoTiempo === 'corriendo') {
                    span.classList.add('bg-green-100', 'text-green-800');
                    span.textContent = 'Tiempo corriendo';
                } else if (estadoTiempo === 'pausado') {
                    span.classList.add('bg-yellow-100', 'text-yellow-800');
                    span.textContent = 'Tiempo pausado';
                } else if (estadoTiempo === 'descanso') {
                    span.classList.add('bg-blue-100', 'text-blue-800');
                    span.textContent = 'En descanso';
                }
            }
        }
    }

    // FUNCIÓN CORREGIDA del temporizador local
    function iniciarTemporizadorLocal(tiempoInicial = null) {
        detenerTemporizadorLocal();
        
        console.log('Iniciando temporizador local con tiempo inicial:', tiempoInicial);
        
        // Usar tiempo inicial del servidor
        let tiempoActual = tiempoInicial !== null ? Math.round(tiempoInicial) : null;
        
        // Si se proporciona tiempo inicial, actualizar el display
        if (tiempoActual !== null) {
            const temporizador = document.getElementById('temporizador');
            if (temporizador) {
                temporizador.textContent = formatearTiempo(tiempoActual);
            }
        }
        
        temporizadorInterval = setInterval(async () => {
            const temporizador = document.getElementById('temporizador');
            if (temporizador) {
                if (tiempoActual !== null) {
                    tiempoActual--;
                } else {
                    // Parsear del display
                    const [minutos, segundos] = temporizador.textContent.split(':').map(Number);
                    tiempoActual = minutos * 60 + segundos - 1;
                }
                
                if (tiempoActual <= 0) {
                    tiempoActual = 0;
                    temporizador.textContent = formatearTiempo(tiempoActual);
                    detenerTemporizadorLocal();
                    
                    // Mostrar alerta
                    alert('¡Tiempo terminado!');
                    
                    // Pausar automáticamente el tiempo en el servidor
                    await controlarTiempo('pausar');
                    return;
                }
                
                temporizador.textContent = formatearTiempo(tiempoActual);
            }
        }, 1000);
    }

    function detenerTemporizadorLocal() {
        if (temporizadorInterval) {
            clearInterval(temporizadorInterval);
            temporizadorInterval = null;
        }
    }

    // Al cargar la página - MEJORADO
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof juegoId === 'undefined') {
            console.error('juegoId no está definido');
            return;
        }
        
        console.log('Inicializando control de tiempo para juego:', juegoId);
        
        // Configurar botones CON PROTECCIÓN ANTI-DOBLE-CLICK
        const btnIniciar = document.getElementById('btn-iniciar');
        const btnPausar = document.getElementById('btn-pausar');
        const btnReiniciar = document.getElementById('btn-reiniciar');
        const btnAvanzar = document.getElementById('btn-avanzar');
        
        if (btnIniciar) {
            btnIniciar.addEventListener('click', function(e) {
                e.preventDefault();
                if (!requestEnProceso && !this.disabled) controlarTiempo('iniciar');
            });
        }
        if (btnPausar) {
            btnPausar.addEventListener('click', function(e) {
                e.preventDefault();
                if (!requestEnProceso && !this.disabled) controlarTiempo('pausar');
            });
        }
        if (btnReiniciar) {
            btnReiniciar.addEventListener('click', function(e) {
                e.preventDefault();
                if (!requestEnProceso && !this.disabled) controlarTiempo('reiniciar');
            });
        }
        if (btnAvanzar) {
            btnAvanzar.addEventListener('click', function(e) {
                e.preventDefault();
                if (!requestEnProceso && !this.disabled) controlarTiempo('avanzar_cuarto');
            });
        }
        
        // Sincronizar estado inicial
        setTimeout(() => {
            sincronizarEstadoTiempo();
        }, 500);
        
        // Sincronizar cada 2 minutos (menos agresivo)
        setInterval(sincronizarEstadoTiempo, 120000);
    });

    // Limpiar al salir de la página
    window.addEventListener('beforeunload', function() {
        detenerTemporizadorLocal();
    });

    // NUEVA: Función global para uso directo en onclick (si es necesario)
    window.controlarTiempo = controlarTiempo;

    function abrirModalRebotes(jugadorId, tipoEquipo) {
        modalData = { jugadorId, tipoEquipo };
        document.getElementById('modalRebotes').classList.remove('hidden');
    }

    function abrirModalFaltas(jugadorId, tipoEquipo) {
        modalData = { jugadorId, tipoEquipo };
        document.getElementById('modalFaltas').classList.remove('hidden');
    }

    function cerrarModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        modalData = {};
    }

    // Función para registrar tiro (actualizada para usar alineación)
    function registrarTiro(tipoPunto, anotado) {
        console.log('Registrando tiro:', {
            alineacion_id: modalData.alineacionId,
            tipo_equipo: modalData.tipoEquipo,
            tipo_punto: tipoPunto,
            anotado: anotado
        });
        
        fetch(`/api/juegos/${juegoId}/estadisticas`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                alineacion_id: modalData.alineacionId,  // Usar ID de alineación
                tipo_equipo: modalData.tipoEquipo,
                tipo_punto: tipoPunto,
                anotado: anotado
            })
        })
        .then(async response => {
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error response:', errorText);
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Success response:', data);
            if(data.success) {
                actualizarPuntajesEnTiempoReal(data);
                cerrarModal('modalPuntos');
            } else {
                alert(data.error || 'Error al registrar tiro');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            alert(`Error al registrar tiro: ${error.message}`);
        });
    }

    // Función corregida para registrar estadística simple
    function registrarEstadistica(alineacionId, tipoEquipo, tipoEstadistica) {
        // Debug: mostrar datos que se van a enviar
        console.log('Enviando datos:', {
            alineacion_id: alineacionId,
            tipo_equipo: tipoEquipo,
            tipo_estadistica: tipoEstadistica
        });
        
        fetch(`/api/juegos/${juegoId}/estadistica-simple`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                alineacion_id: alineacionId,  // Cambiado: ahora enviamos el ID de la alineación
                tipo_equipo: tipoEquipo,
                tipo_estadistica: tipoEstadistica
            })
        })
        .then(async response => {
            console.log('Response status:', response.status);
            
            const data = await response.json().catch(() => ({
                error: 'Error al procesar respuesta del servidor'
            }));
            
            console.log('Response data:', data);
            
            if (!response.ok) {
                throw new Error(data.error || `Error ${response.status}: ${response.statusText}`);
            }
            
            return data;
        })
        .then(data => {
            if(data.success) {
                console.log('Estadística registrada exitosamente');
                
                // Actualizar UI si es necesario
                if (tipoEstadistica.includes('rebote')) {
                    cerrarModal('modalRebotes');
                } else if (tipoEstadistica.includes('falta')) {
                    cerrarModal('modalFaltas');
                }
                
                // Actualizar estadísticas en tiempo real
                actualizarEstadisticasCompletas(data.jugador_id, tipoEquipo);
                
                // Mostrar mensaje de éxito
                showNotification('Estadística registrada correctamente', 'success');
            } else {
                throw new Error(data.error || 'Error desconocido al registrar estadística');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            alert(`Error al registrar estadística: ${error.message}`);
        });
    }

    // Función auxiliar para mostrar notificaciones (opcional)
    function showNotification(message, type = 'info') {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remover después de 3 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }

    // Función mejorada para debugging - verificar estado antes de enviar
    function debugearEstadistica(jugadorId, tipoEquipo, tipoEstadistica) {
        console.log('=== DEBUG ESTADÍSTICA ===');
        console.log('Juego ID:', juegoId);
        console.log('Jugador ID:', jugadorId);
        console.log('Tipo Equipo:', tipoEquipo);
        console.log('Tipo Estadística:', tipoEstadistica);
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);
        console.log('URL completa:', `/api/juegos/${juegoId}/estadistica-simple`);
        console.log('========================');
    }

    // Función auxiliar para verificar conexión con el servidor
    function verificarConexion() {
        fetch(`/api/juegos/${juegoId}/estadisticas-tiempo-real`)
        .then(response => {
            if (response.ok) {
                console.log('Conexión con servidor OK');
            } else {
                console.error('Problema de conexión:', response.status);
            }
        })
        .catch(error => {
            console.error('Error de conexión:', error);
        });
    }
    // Función para actualizar estadísticas en tiempo real (mejorada)
    function actualizarEstadisticasCompletas(jugadorId, tipoEquipo) {
        fetch(`/api/juegos/${juegoId}/estadisticas-tiempo-real`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Actualizar puntos del equipo
                document.querySelector('.puntos-local').textContent = data.juego.puntos_local;
                document.querySelector('.puntos-visitante').textContent = data.juego.puntos_visitante;
                
                // Actualizar estadísticas individuales
                const estadisticas = tipoEquipo === 'Local' ? data.estadisticas_local : data.estadisticas_visitante;
                
                estadisticas.forEach(jugador => {
                    const jugadorElement = document.querySelector(`[data-jugador-id="${jugador.jugador_id}"]`);
                    if (jugadorElement) {
                        // Actualizar puntos
                        const puntosElement = jugadorElement.querySelector('.puntos-jugador');
                        if (puntosElement) {
                            puntosElement.textContent = jugador.puntos + ' pts';
                        }
                        
                        // Actualizar otras estadísticas si existen elementos para ellas
                        const statsElement = jugadorElement.querySelector('.estadisticas-extras');
                        if (statsElement) {
                            statsElement.innerHTML = `
                                <div class="flex justify-between text-xs">
                                    <span>Ast: ${jugador.asistencias}</span>
                                    <span>Reb: ${jugador.rebotes_totales}</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span>Rob: ${jugador.robos}</span>
                                    <span>Blq: ${jugador.bloqueos}</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span>2P: ${jugador.tiros_2pts}</span>
                                    <span>3P: ${jugador.tiros_3pts}</span>
                                </div>
                            `;
                        }
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error al actualizar estadísticas:', error);
        });
    }

    // Función para registrar tiros directamente sin modal - VERSIÓN CORREGIDA
    function registrarTiroDirecto(alineacionId, tipoEquipo, tipoPunto, anotado) {
        console.log('Registrando tiro directo:', {
            alineacion_id: alineacionId,
            tipo_equipo: tipoEquipo,
            tipo_punto: tipoPunto,
            anotado: anotado
        });
        
        // Mostrar feedback visual inmediato
        const button = event.target;
        const originalText = button.textContent;
        button.style.opacity = '0.7';
        button.disabled = true;
        
        fetch(`/api/juegos/${juegoId}/estadisticas`, {  // Comillas corregidas
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                alineacion_id: alineacionId,
                tipo_equipo: tipoEquipo,
                tipo_punto: tipoPunto,
                anotado: anotado
            })
        })
        .then(async response => {
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error response:', errorText);
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Success response:', data);
            if(data.success) {
                // Actualizar UI
                actualizarPuntajesEnTiempoReal(data);
                
                // Feedback visual de éxito
                button.style.backgroundColor = anotado ? '#10b981' : '#ef4444';
                setTimeout(() => {
                    button.style.backgroundColor = '';
                    button.style.opacity = '1';
                    button.disabled = false;
                }, 500);
                
                // Mostrar notificación rápida
                showQuickNotification(
                    anotado ? 
                    `+${tipoPunto === 'tiro_libre' ? '1' : tipoPunto === '2pts' ? '2' : '3'} pts` : 
                    `${tipoPunto === 'tiro_libre' ? 'TL' : tipoPunto === '2pts' ? '2P' : '3P'} fallado`,
                    anotado ? 'success' : 'miss'
                );
            } else {
                throw new Error(data.error || 'Error al registrar tiro');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            
            // Restaurar botón en caso de error
            button.style.opacity = '1';
            button.disabled = false;
            
            // Mostrar error
            showQuickNotification(`Error: ${error.message}`, 'error');
        });
    }

    // Función para mostrar notificaciones rápidas y discretas
    function showQuickNotification(message, type = 'info') {
        // Remover notificación anterior si existe
        const existingNotification = document.getElementById('quick-notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        const notification = document.createElement('div');
        notification.id = 'quick-notification';
        notification.className = `fixed top-20 right-4 px-4 py-2 rounded-lg z-50 font-medium text-sm transform translate-x-full transition-transform duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'miss' ? 'bg-orange-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Animar salida y remover
        setTimeout(() => {
            notification.style.transform = 'translateX(full)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 2000);
    }
</script>
@endsection