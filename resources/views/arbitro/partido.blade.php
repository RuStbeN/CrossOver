@extends('layouts.arbitro')
@section('content')

<!-- Agregar el meta tag del token CSRF -->
@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

<main class="relative z-10 py-8">
    <div class="w-full mx-auto">
        <!-- Header con equipos y temporizador -->
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 mb-8 mx-4 overflow-hidden">
            <div class="p-6">
                <!-- Título Principal -->
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-black dark:text-white mb-2">Mesa de Control</h1>
                    <div class="w-24 h-1 bg-primary-600 mx-auto rounded"></div>
                    
                    <!-- Mostrar estado del juego -->
                    <div class="mt-4">
                        @if($juego->estado === 'Programado')
                            <div class="inline-flex items-center px-4 py-2 bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-300 dark:border-yellow-700 rounded-lg">
                                <span class="text-yellow-800 dark:text-yellow-200 font-medium">Partido Programado</span>
                                <button onclick="iniciarPartidoOficial()" class="ml-4 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium transition-colors">
                                    Iniciar Partido Oficial
                                </button>
                            </div>
                        @elseif($juego->estado === 'En Curso')
                            <div class="inline-flex items-center px-4 py-2 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg">
                                <span class="text-green-800 dark:text-green-200 font-medium">Partido En Curso</span>
                                <button onclick="finalizarPartidoOficial()" class="ml-4 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium transition-colors">
                                    Finalizar Partido
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Equipos y información central -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
                    <!-- Equipo Izquierdo -->
                    <div class="text-center">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-dark-700 border-2 border-gray-300 dark:border-dark-600">
                                @if($juego->equipoLocal->logo_url)
                                    <img src="/storage/{{ $juego->equipoLocal->logo_url }}" 
                                        alt="{{ $juego->equipoLocal->nombre }}" 
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-dark-800 text-gray-500 dark:text-gray-400">
                                        <span class="text-white font-bold text-2xl">{{ substr($juego->equipoLocal->nombre, 0, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $juego->equipoLocal->nombre }}</h2>
                                <p class="text-gray-600 dark:text-gray-300">{{ $juego->puntos_local ?? 0 }}</p>
                                <p class="text-gray-600 dark:text-gray-300">Tiempos: {{ $juego->tiempos_local ?? 3 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Información Central -->
                    <div class="text-center">
                        <!-- Temporizador -->
                        <div class="bg-gray-100 dark:bg-dark-700 rounded-lg p-4 mb-4 inline-block">
                            <div class="text-3xl font-mono font-bold text-gray-800 dark:text-white mb-2" id="temporizador">
                                {{ sprintf('%02d:%02d', floor(($juego->tiempo_restante ?? $juego->duracion_cuarto * 60) / 60), ($juego->tiempo_restante ?? $juego->duracion_cuarto * 60) % 60) }}
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">Tiempo restante</p>
                        </div>
                        
                        <!-- Cuarto -->
                        <div class="mb-4">
                            <p id="cuarto-actual" class="text-lg font-semibold text-primary-800 dark:text-primary-200">
                                @if($juego->en_descanso ?? false)
                                    Descanso (Q{{ $juego->cuarto_actual ?? 1 }})
                                @else
                                    Q{{ $juego->cuarto_actual ?? 1 }}
                                @endif
                            </p>
                        </div>
                        
                        <!-- Controles del temporizador -->
                        @if($juego->estado === 'En Curso')
                        <div class="flex justify-center space-x-3 mb-4">
                            <!-- Botón Iniciar - SIN ONCLICK -->
                            <button id="btn-iniciar" 
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-md font-medium transition-colors flex items-center"
                                    {{ ($juego->estado_tiempo ?? 'pausado') === 'corriendo' ? 'disabled' : '' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                </svg>
                                Iniciar
                            </button>
                            
                            <!-- Botón Pausar - SIN ONCLICK -->
                            <button id="btn-pausar" 
                                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-md font-medium transition-colors flex items-center"
                                    {{ ($juego->estado_tiempo ?? 'pausado') !== 'corriendo' ? 'disabled' : '' }}>
                                <svg xmlns="http://www.w3.000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Pausar
                            </button>
                            
                            <!-- Botón Reiniciar - SIN ONCLICK -->
                            <button id="btn-reiniciar" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-md font-medium transition-colors flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                </svg>
                                Reiniciar
                            </button>
                            
                            <!-- Botón Avanzar Cuarto - SIN ONCLICK -->
                            <button id="btn-avanzar" 
                                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-md font-medium transition-colors flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                                </svg>
                                Avanzar
                            </button>
                        </div>

                        <!-- Indicador de Estado -->
                        <div id="estado-tiempo" class="text-center mb-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                @if(($juego->en_descanso ?? false))
                                    bg-blue-100 text-blue-800
                                @elseif(($juego->estado_tiempo ?? 'pausado') === 'corriendo')
                                    bg-green-100 text-green-800
                                @elseif(($juego->estado_tiempo ?? 'pausado') === 'pausado')
                                    bg-yellow-100 text-yellow-800
                                @else 
                                    bg-blue-100 text-blue-800 
                                @endif">
                                @if($juego->en_descanso ?? false)
                                    En descanso
                                @elseif(($juego->estado_tiempo ?? 'pausado') === 'corriendo')
                                    Tiempo corriendo
                                @elseif(($juego->estado_tiempo ?? 'pausado') === 'pausado')
                                    Tiempo pausado
                                @else
                                    En descanso
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Script para pasar juegoId al JavaScript -->
                    <script>
                        window.juegoId = {{ $juego->id }};
                    </script>

                    <!-- Equipo Derecho -->
                    <div class="text-center">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-dark-700 border-2 border-gray-300 dark:border-dark-600">
                                @if($juego->equipoVisitante->logo_url)
                                    <img src="/storage/{{ $juego->equipoVisitante->logo_url }}" 
                                        alt="{{ $juego->equipoVisitante->nombre }}" 
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-dark-800 text-gray-500 dark:text-gray-400">
                                        <span class="text-white font-bold text-2xl">{{ substr($juego->equipoVisitante->nombre, 0, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            <!-- Equipo Visitante (parte derecha) -->
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $juego->equipoVisitante->nombre }}</h2>
                                <!-- Cambiar de puntos-local a puntos-visitante -->
                                <p class="text-gray-600 dark:text-gray-300 puntos-visitante">{{ $juego->puntos_visitante ?? 0 }}</p>
                                <p class="text-gray-600 dark:text-gray-300">Tiempos: {{ $juego->tiempos_visitante ?? 3 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 mx-4">
            <!-- Card Equipo Local -->
            <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-blue-50 dark:bg-blue-900/20">
                    <h3 class="text-xl font-bold text-blue-800 dark:text-blue-200 text-center">{{ $juego->equipoLocal->nombre }}</h3>
                </div>
                <div class="p-6">
                    @if($juego->estado === 'Programado' || $juego->juegoAlineaciones->where('tipo_equipo', 'Local')->count() > 0)
                    <div class="space-y-4">
                        @if($juego->estado === 'Programado')
                            <!-- PARA JUEGOS PROGRAMADOS: Usar titularesLocalSeleccionados -->
                            @foreach($titularesLocalSeleccionados as $jugador)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-dark-700 rounded-lg" data-jugador-id="{{ $jugador->id }}">
                                <!-- Columna 1: Foto y Datos Básicos -->
                                <div class="flex items-center min-w-[120px]">
                                    @if($jugador->foto_url)
                                    <img src="{{ asset('storage/'.$jugador->foto_url) }}" 
                                        alt="{{ $jugador->nombre }}"
                                        class="w-12 h-12 rounded-full object-cover mr-3">
                                    @else
                                    <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold mr-3">
                                        {{ substr($jugador->nombre, 0, 1) }}
                                    </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white truncate">{{ $jugador->nombre }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">#{{ $jugador->numero_camiseta }}</p>
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Titular</span>
                                    </div>
                                </div>

                                <!-- Columna 2: Estadísticas Básicas (todas en 0 para juegos programados) -->
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-sm mx-4">
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Ast:</span>
                                        <span>0</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Reb:</span>
                                        <span>0</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Rob:</span>
                                        <span>0</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Blq:</span>
                                        <span>0</span>
                                    </div>
                                </div>

                                <!-- Columna 3: Puntos y Botones (deshabilitados para juegos programados) -->
                                <div class="flex flex-col items-end min-w-[180px]">
                                    <p class="font-bold text-lg text-gray-800 dark:text-white mb-2 puntos-jugador">0 pts</p>
                                    <div class="grid grid-cols-3 gap-1">
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-2 py-1 rounded col-span-3" disabled>
                                            Puntos
                                        </button>
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-1 py-1 rounded" disabled>
                                            Ast
                                        </button>
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-1 py-1 rounded" disabled>
                                            Reb
                                        </button>
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-1 py-1 rounded" disabled>
                                            Rob
                                        </button>
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-1 py-1 rounded" disabled>
                                            Blq
                                        </button>
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-1 py-1 rounded" disabled>
                                            Flt
                                        </button>
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-1 py-1 rounded" disabled>
                                            Per
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <!-- PARA JUEGOS EN CURSO: Usar juegoAlineaciones -->
                            @foreach($juego->juegoAlineaciones->where('tipo_equipo', 'Local') as $alineacion)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-dark-700 rounded-lg" data-jugador-id="{{ $alineacion->jugador_id }}">
                                <!-- Columna 1: Foto y Datos Básicos -->
                                <div class="flex items-center min-w-[120px]">
                                    @if($alineacion->jugador->foto_url)
                                    <img src="{{ asset('storage/'.$alineacion->jugador->foto_url) }}" 
                                        alt="{{ $alineacion->jugador->nombre }}"
                                        class="w-12 h-12 rounded-full object-cover mr-3">
                                    @else
                                    <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold mr-3">
                                        {{ substr($alineacion->jugador->nombre, 0, 1) }}
                                    </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white truncate">{{ $alineacion->jugador->nombre }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">#{{ $alineacion->numero_camiseta }}</p>
                                        @if($alineacion->es_titular)
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Titular</span>
                                        @else
                                        <span class="text-xs bg-gray-100 text-gray-800 px-2 py-0.5 rounded">Suplente</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Columna 2: Estadísticas Básicas -->
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs mx-4">
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Ast:</span>
                                        <span>{{ $alineacion->asistencias ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Reb:</span>
                                        <span>{{ ($alineacion->rebotes_defensivos ?? 0) + ($alineacion->rebotes_ofensivos ?? 0) }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Rob:</span>
                                        <span>{{ $alineacion->robos ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Blq:</span>
                                        <span>{{ $alineacion->bloqueos ?? 0 }}</span>
                                    </div>
                                </div>

                                <!-- Columna 3: Puntos y Botones (habilitados para juegos en curso) -->
                                <div class="flex flex-col items-end min-w-[180px]">
                                    <p class="font-bold text-lg text-gray-800 dark:text-white mb-2 puntos-jugador">{{ $alineacion->puntos }} pts</p>
                                    @if($juego->estado === 'En Curso')
                                    <div class="grid grid-cols-3 gap-1">
                                        <!-- CORREGIDO: Pasar jugador_id, tipo_equipo Y alineacion_id para puntos -->
                                        <button onclick="abrirModalPuntos({{ $alineacion->jugador_id }}, 'Local', {{ $alineacion->id }})" 
                                                class="text-xs bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded col-span-3">
                                            Puntos
                                        </button>
                                        
                                        <!-- CORREGIDO: Usar ID de alineación para estadísticas -->
                                        <button onclick="registrarEstadistica({{ $alineacion->id }}, 'Local', 'asistencia')" 
                                                class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-1 py-1 rounded">
                                            Ast
                                        </button>
                                        <button onclick="abrirModalRebotes({{ $alineacion->id }}, 'Local')" 
                                                class="text-xs bg-purple-500 hover:bg-purple-600 text-white px-1 py-1 rounded">
                                            Reb
                                        </button>
                                        <button onclick="registrarEstadistica({{ $alineacion->id }}, 'Local', 'robo')" 
                                                class="text-xs bg-orange-500 hover:bg-orange-600 text-white px-1 py-1 rounded">
                                            Rob
                                        </button>
                                        <button onclick="registrarEstadistica({{ $alineacion->id }}, 'Local', 'bloqueo')" 
                                                class="text-xs bg-indigo-500 hover:bg-indigo-600 text-white px-1 py-1 rounded">
                                            Blq
                                        </button>
                                        <button onclick="abrirModalFaltas({{ $alineacion->id }}, 'Local')" 
                                                class="text-xs bg-red-500 hover:bg-red-600 text-white px-1 py-1 rounded">
                                            Flt
                                        </button>
                                        <button onclick="registrarEstadistica({{ $alineacion->id }}, 'Local', 'perdida')" 
                                                class="text-xs bg-gray-500 hover:bg-gray-600 text-white px-1 py-1 rounded">
                                            Per
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    @else
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        <p>No hay jugadores seleccionados para este equipo</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Card Equipo Visitante -->
            <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-red-50 dark:bg-red-900/20">
                    <h3 class="text-xl font-bold text-red-800 dark:text-red-200 text-center">{{ $juego->equipoVisitante->nombre }}</h3>
                </div>
                <div class="p-6">
                    @if($juego->estado === 'Programado' || $juego->juegoAlineaciones->where('tipo_equipo', 'Visitante')->count() > 0)
                    <div class="space-y-4">
                        @if($juego->estado === 'Programado')
                            @foreach($titularesVisitanteSeleccionados as $jugador)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-dark-700 rounded-lg" data-jugador-id="{{ $jugador->id }}">
                                <!-- Columna 1: Foto y Datos Básicos -->
                                <div class="flex items-center min-w-[120px]">
                                    @if($jugador->foto_url)
                                    <img src="{{ asset('storage/'.$jugador->foto_url) }}" 
                                        alt="{{ $jugador->nombre }}"
                                        class="w-12 h-12 rounded-full object-cover mr-3">
                                    @else
                                    <div class="w-12 h-12 rounded-full bg-red-600 flex items-center justify-center text-white font-bold mr-3">
                                        {{ substr($jugador->nombre, 0, 1) }}
                                    </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white truncate">{{ $jugador->nombre }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">#{{ $jugador->numero_camiseta }}</p>
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Titular</span>
                                    </div>
                                </div>

                                <!-- Columna 2: Estadísticas Básicas -->
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-sm mx-4"> <!-- Cambiado de text-xs a text-sm -->
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Ast:</span>
                                        <span>{{ $jugador->asistencias ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Reb:</span>
                                        <span>{{ ($jugador->rebotes_defensivos ?? 0) + ($jugador->rebotes_ofensivos ?? 0) }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Rob:</span>
                                        <span>{{ $jugador->robos ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Blq:</span>
                                        <span>{{ $jugador->bloqueos ?? 0 }}</span>
                                    </div>
                                </div>

                                <!-- Columna 3: Puntos y Botones -->
                                <div class="flex flex-col items-end min-w-[180px]">
                                    <p class="font-bold text-lg text-gray-800 dark:text-white mb-2 puntos-jugador">0 pts</p>
                                    @if($juego->estado === 'En Curso')
                                    <div class="grid grid-cols-3 gap-1">
                                        <!-- NOTA: Para juegos programados, los botones estarán deshabilitados hasta iniciar el partido -->
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-2 py-1 rounded col-span-3" disabled>
                                            Puntos
                                        </button>
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-1 py-1 rounded" disabled>
                                            Ast
                                        </button>
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-1 py-1 rounded" disabled>
                                            Reb
                                        </button>
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-1 py-1 rounded" disabled>
                                            Rob
                                        </button>
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-1 py-1 rounded" disabled>
                                            Blq
                                        </button>
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-1 py-1 rounded" disabled>
                                            Flt
                                        </button>
                                        <button onclick="alert('Inicia el partido para registrar estadísticas')" 
                                                class="text-xs bg-gray-400 text-white px-1 py-1 rounded" disabled>
                                            Per
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @else
                            <!-- JUEGOS EN CURSO: Usar datos de alineaciones -->
                            @foreach($juego->juegoAlineaciones->where('tipo_equipo', 'Visitante') as $alineacion)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-dark-700 rounded-lg" data-jugador-id="{{ $alineacion->jugador_id }}">
                                <!-- Columna 1: Foto y Datos Básicos -->
                                <div class="flex items-center min-w-[120px]">
                                    @if($alineacion->jugador->foto_url)
                                    <img src="{{ asset('storage/'.$alineacion->jugador->foto_url) }}" 
                                        alt="{{ $alineacion->jugador->nombre }}"
                                        class="w-12 h-12 rounded-full object-cover mr-3">
                                    @else
                                    <div class="w-12 h-12 rounded-full bg-red-600 flex items-center justify-center text-white font-bold mr-3">
                                        {{ substr($alineacion->jugador->nombre, 0, 1) }}
                                    </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white truncate">{{ $alineacion->jugador->nombre }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">#{{ $alineacion->numero_camiseta }}</p>
                                        @if($alineacion->es_titular)
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Titular</span>
                                        @else
                                        <span class="text-xs bg-gray-100 text-gray-800 px-2 py-0.5 rounded">Suplente</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Columna 2: Estadísticas Básicas -->
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs mx-4">
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Ast:</span>
                                        <span>{{ $alineacion->asistencias ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Reb:</span>
                                        <span>{{ ($alineacion->rebotes_defensivos ?? 0) + ($alineacion->rebotes_ofensivos ?? 0) }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Rob:</span>
                                        <span>{{ $alineacion->robos ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-500 dark:text-gray-400 mr-1">Blq:</span>
                                        <span>{{ $alineacion->bloqueos ?? 0 }}</span>
                                    </div>
                                </div>

                                <!-- Columna 3: Puntos y Botones -->
                                <div class="flex flex-col items-end min-w-[180px]">
                                    <p class="font-bold text-lg text-gray-800 dark:text-white mb-2 puntos-jugador">{{ $alineacion->puntos }} pts</p>
                                    @if($juego->estado === 'En Curso')
                                    <div class="grid grid-cols-3 gap-1">
                                        <!-- CORREGIDO: Pasar jugador_id, tipo_equipo Y alineacion_id para puntos -->
                                        <button onclick="abrirModalPuntos({{ $alineacion->jugador_id }}, 'Visitante', {{ $alineacion->id }})" 
                                                class="text-xs bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded col-span-3">
                                            Puntos
                                        </button>
                                        
                                        <!-- CORREGIDO: Usar ID de alineación para estadísticas -->
                                        <button onclick="registrarEstadistica({{ $alineacion->id }}, 'Visitante', 'asistencia')" 
                                                class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-1 py-1 rounded">
                                            Ast
                                        </button>
                                        <button onclick="abrirModalRebotes({{ $alineacion->id }}, 'Visitante')" 
                                                class="text-xs bg-purple-500 hover:bg-purple-600 text-white px-1 py-1 rounded">
                                            Reb
                                        </button>
                                        <button onclick="registrarEstadistica({{ $alineacion->id }}, 'Visitante', 'robo')" 
                                                class="text-xs bg-orange-500 hover:bg-orange-600 text-white px-1 py-1 rounded">
                                            Rob
                                        </button>
                                        <button onclick="registrarEstadistica({{ $alineacion->id }}, 'Visitante', 'bloqueo')" 
                                                class="text-xs bg-indigo-500 hover:bg-indigo-600 text-white px-1 py-1 rounded">
                                            Blq
                                        </button>
                                        <button onclick="abrirModalFaltas({{ $alineacion->id }}, 'Visitante')" 
                                                class="text-xs bg-red-500 hover:bg-red-600 text-white px-1 py-1 rounded">
                                            Flt
                                        </button>
                                        <button onclick="registrarEstadistica({{ $alineacion->id }}, 'Visitante', 'perdida')" 
                                                class="text-xs bg-gray-500 hover:bg-gray-600 text-white px-1 py-1 rounded">
                                            Per
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    @else
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        <p>No hay jugadores seleccionados para este equipo</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para Puntos -->
    <div id="modalPuntos" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-dark-800 rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Registrar Puntos</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <button onclick="registrarTiro('2pts', true)" class="bg-green-500 text-white p-3 rounded">2 Pts Anotado</button>
                        <button onclick="registrarTiro('2pts', false)" class="bg-red-500 text-white p-3 rounded">2 Pts Fallado</button>
                        <button onclick="registrarTiro('3pts', true)" class="bg-green-600 text-white p-3 rounded">3 Pts Anotado</button>
                        <button onclick="registrarTiro('3pts', false)" class="bg-red-600 text-white p-3 rounded">3 Pts Fallado</button>
                        <button onclick="registrarTiro('tiro_libre', true)" class="bg-green-400 text-white p-3 rounded">TL Anotado</button>
                        <button onclick="registrarTiro('tiro_libre', false)" class="bg-red-400 text-white p-3 rounded">TL Fallado</button>
                    </div>
                </div>
                <button onclick="cerrarModal('modalPuntos')" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Rebotes -->
    <div id="modalRebotes" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-dark-800 rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Tipo de Rebote</h3>
                <div class="space-y-3">
                    <button onclick="registrarEstadistica(modalData.jugadorId, modalData.tipoEquipo, 'rebote_defensivo')" 
                            class="w-full bg-blue-500 text-white p-3 rounded">Rebote Defensivo</button>
                    <button onclick="registrarEstadistica(modalData.jugadorId, modalData.tipoEquipo, 'rebote_ofensivo')" 
                            class="w-full bg-orange-500 text-white p-3 rounded">Rebote Ofensivo</button>
                </div>
                <button onclick="cerrarModal('modalRebotes')" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Faltas -->
    <div id="modalFaltas" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-dark-800 rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Tipo de Falta</h3>
                <div class="space-y-3">
                    <button onclick="registrarEstadistica(modalData.jugadorId, modalData.tipoEquipo, 'falta_personal')" 
                            class="w-full bg-yellow-500 text-white p-3 rounded">Falta Personal</button>
                    <button onclick="registrarEstadistica(modalData.jugadorId, modalData.tipoEquipo, 'falta_tecnica')" 
                            class="w-full bg-red-500 text-white p-3 rounded">Falta Técnica</button>
                    <button onclick="registrarEstadistica(modalData.jugadorId, modalData.tipoEquipo, 'falta_descalificante')" 
                            class="w-full bg-red-700 text-white p-3 rounded">Falta Descalificante</button>
                </div>
                <button onclick="cerrarModal('modalFaltas')" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            </div>
        </div>
    </div>
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
                    puntosElement.textContent = data.puntos_jugador + ' pts';
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
        
        // Actualizar cuarto actual
        const cuartoElement = document.getElementById('cuarto-actual');
        if (cuartoElement) {
            if (data.estado_tiempo === 'descanso') {
                cuartoElement.textContent = `Descanso (Q${data.cuarto_actual})`;
            } else {
                cuartoElement.textContent = `Q${data.cuarto_actual}`;
            }
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

    // Función para abrir modal de puntos (también corregida)
    function abrirModalPuntos(jugadorId, tipoEquipo, alineacionId = null) {
        modalData = { 
            jugadorId, 
            tipoEquipo,
            alineacionId: alineacionId || jugadorId // Fallback para compatibilidad
        };
        document.getElementById('modalPuntos').classList.remove('hidden');
    }

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

    // Función para mostrar un modal de corrección (opcional)
    function abrirModalCorreccion(jugadorId, tipoEquipo) {
        // Implementar modal para correcciones manuales
        const campo = prompt("¿Qué campo quieres corregir? (puntos, asistencias, rebotes_defensivos, etc.)");
        if (!campo) return;
        
        const valor = prompt("Nuevo valor:");
        if (valor === null) return;
        
        fetch(`/api/juegos/${juegoId}/corregir-estadistica`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                jugador_id: jugadorId,
                tipo_equipo: tipoEquipo,
                campo: campo,
                valor: parseInt(valor)
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Estadística corregida');
                actualizarEstadisticasCompletas(jugadorId, tipoEquipo);
            } else {
                alert(data.error || 'Error al corregir');
            }
        });
    }
</script>
@endsection