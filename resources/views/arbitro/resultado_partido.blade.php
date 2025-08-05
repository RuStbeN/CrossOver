@extends('layouts.arbitro')

@section('title', 'Resultados Finales - ' . $juego->equipoLocal->nombre . ' vs ' . $juego->equipoVisitante->nombre)

@section('content')
<main class="relative z-10 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado del partido -->
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gradient-to-r from-primary-500 to-primary-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Resultados Finales</h1>
                        <p class="text-primary-100 mt-1">
                            {{ \Carbon\Carbon::parse($juego->fecha)->format('d M Y') }} - {{ $juego->cancha->nombre }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-primary-100">{{ $juego->torneo->nombre ?? 'Partido Regular' }}</p>
                        <p class="text-sm text-primary-100">{{ $juego->fase }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Marcador final -->
            <div class="p-8">
                <div class="grid grid-cols-3 gap-8 items-center">
                    <!-- Equipo Local -->
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">L</span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">{{ $juego->equipoLocal->nombre }}</h2>
                        <div class="text-6xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $juego->puntos_local }}
                        </div>
                    </div>
                    
                    <!-- VS -->
                    <div class="text-center">
                        <div class="text-4xl font-bold text-gray-400 dark:text-gray-500 mb-4">VS</div>
                        <div class="text-lg font-semibold text-gray-600 dark:text-gray-400">
                            @if($juego->puntos_local > $juego->puntos_visitante)
                                Victoria: {{ $juego->equipoLocal->nombre }}
                            @elseif($juego->puntos_visitante > $juego->puntos_local)
                                Victoria: {{ $juego->equipoVisitante->nombre }}
                            @else
                                Empate
                            @endif
                        </div>
                    </div>
                    
                    <!-- Equipo Visitante -->
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-red-600 dark:text-red-400">V</span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">{{ $juego->equipoVisitante->nombre }}</h2>
                        <div class="text-6xl font-bold text-red-600 dark:text-red-400">
                            {{ $juego->puntos_visitante }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas detalladas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-data="estadisticas">
            <!-- Equipo Local -->
            <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-blue-50 dark:bg-blue-900/20">
                    <h3 class="text-xl font-bold text-blue-800 dark:text-blue-300 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z"></path>
                        </svg>
                        {{ $juego->equipoLocal->nombre }}
                    </h3>
                </div>
                
                <!-- Tabs Local -->
                <div class="border-b border-gray-200 dark:border-dark-700">
                    <div class="flex">
                        <button @click="activeTabLocal = 'resumen-local'" 
                                :class="activeTabLocal === 'resumen-local' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="px-4 py-2 border-b-2 font-medium text-sm hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            Resumen
                        </button>
                        <button @click="activeTabLocal = 'anotacion-local'" 
                                :class="activeTabLocal === 'anotacion-local' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="px-4 py-2 border-b-2 font-medium text-sm hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            Anotación
                        </button>
                        <button @click="activeTabLocal = 'defensa-local'" 
                                :class="activeTabLocal === 'defensa-local' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="px-4 py-2 border-b-2 font-medium text-sm hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            Defensa
                        </button>
                        <button @click="activeTabLocal = 'faltas-local'" 
                                :class="activeTabLocal === 'faltas-local' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="px-4 py-2 border-b-2 font-medium text-sm hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            Faltas
                        </button>
                    </div>
                </div>

                <!-- Contenido de tabs -->
                <div class="p-6">
                    <!-- Resumen Local -->
                    <div x-show="activeTabLocal === 'resumen-local'" class="space-y-4">
                        @foreach($estadisticasLocal as $estadistica)
                        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ $estadistica->numero_camiseta }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 dark:text-white">{{ $estadistica->jugador->nombre }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $estadistica->posicion_jugada }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $estadistica->puntos }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $estadistica->minutos_jugados }}min</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-2 text-xs text-gray-600 dark:text-gray-400">
                                <div class="text-center">
                                    <p class="font-medium">REB</p>
                                    <p>{{ $estadistica->rebotes_totales }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-medium">AST</p>
                                    <p>{{ $estadistica->asistencias }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-medium">ROB</p>
                                    <p>{{ $estadistica->robos }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-medium">BLQ</p>
                                    <p>{{ $estadistica->bloqueos }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- Totales Local -->
                        <div class="bg-blue-100 dark:bg-blue-900/30 rounded-lg p-4 border-2 border-blue-200 dark:border-blue-700">
                            <h4 class="font-bold text-blue-800 dark:text-blue-300 mb-3">Totales del Equipo</h4>
                            <div class="grid grid-cols-4 gap-4 text-sm">
                                <div class="text-center">
                                    <p class="font-bold text-blue-600 dark:text-blue-400 text-lg">{{ $totalesLocal['puntos'] }}</p>
                                    <p class="text-blue-700 dark:text-blue-300">Puntos</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-bold text-blue-600 dark:text-blue-400 text-lg">{{ $totalesLocal['rebotes_totales'] }}</p>
                                    <p class="text-blue-700 dark:text-blue-300">Rebotes</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-bold text-blue-600 dark:text-blue-400 text-lg">{{ $totalesLocal['asistencias'] }}</p>
                                    <p class="text-blue-700 dark:text-blue-300">Asistencias</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-bold text-blue-600 dark:text-blue-400 text-lg">{{ $totalesLocal['robos'] }}</p>
                                    <p class="text-blue-700 dark:text-blue-300">Robos</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Anotación Local -->
                    <div x-show="activeTabLocal === 'anotacion-local'" class="space-y-4">
                        @foreach($estadisticasLocal as $estadistica)
                        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ $estadistica->numero_camiseta }}
                                    </div>
                                    <p class="font-bold text-gray-800 dark:text-white">{{ $estadistica->jugador->nombre }}</p>
                                </div>
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $estadistica->puntos }} pts</p>
                            </div>
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div class="text-center">
                                    <p class="text-gray-600 dark:text-gray-400">Tiros Libres</p>
                                    <p class="font-bold">{{ $estadistica->tiros_libres_anotados }}/{{ $estadistica->tiros_libres_intentados }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $estadistica->tiros_libres_intentados > 0 ? round(($estadistica->tiros_libres_anotados / $estadistica->tiros_libres_intentados) * 100, 1) : 0 }}%
                                    </p>
                                </div>
                                <div class="text-center">
                                    <p class="text-gray-600 dark:text-gray-400">Tiros 2pts</p>
                                    <p class="font-bold">{{ $estadistica->tiros_2pts_anotados }}/{{ $estadistica->tiros_2pts_intentados }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $estadistica->tiros_2pts_intentados > 0 ? round(($estadistica->tiros_2pts_anotados / $estadistica->tiros_2pts_intentados) * 100, 1) : 0 }}%
                                    </p>
                                </div>
                                <div class="text-center">
                                    <p class="text-gray-600 dark:text-gray-400">Tiros 3pts</p>
                                    <p class="font-bold">{{ $estadistica->tiros_3pts_anotados }}/{{ $estadistica->tiros_3pts_intentados }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $estadistica->tiros_3pts_intentados > 0 ? round(($estadistica->tiros_3pts_anotados / $estadistica->tiros_3pts_intentados) * 100, 1) : 0 }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Defensa Local -->
                    <div x-show="activeTabLocal === 'defensa-local'" class="space-y-4">
                        @foreach($estadisticasLocal as $estadistica)
                        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ $estadistica->numero_camiseta }}
                                    </div>
                                    <p class="font-bold text-gray-800 dark:text-white">{{ $estadistica->jugador->nombre }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-4 text-sm text-center">
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Rebotes Def.</p>
                                    <p class="font-bold text-lg">{{ $estadistica->rebotes_defensivos }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Rebotes Of.</p>
                                    <p class="font-bold text-lg">{{ $estadistica->rebotes_ofensivos }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Robos</p>
                                    <p class="font-bold text-lg">{{ $estadistica->robos }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Bloqueos</p>
                                    <p class="font-bold text-lg">{{ $estadistica->bloqueos }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Faltas Local -->
                    <div x-show="activeTabLocal === 'faltas-local'" class="space-y-4">
                        @foreach($estadisticasLocal as $estadistica)
                        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ $estadistica->numero_camiseta }}
                                    </div>
                                    <p class="font-bold text-gray-800 dark:text-white">{{ $estadistica->jugador->nombre }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-4 text-sm text-center">
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Personales</p>
                                    <p class="font-bold text-lg">{{ $estadistica->faltas_personales }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Técnicas</p>
                                    <p class="font-bold text-lg">{{ $estadistica->faltas_tecnicas }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Descalif.</p>
                                    <p class="font-bold text-lg">{{ $estadistica->faltas_descalificantes }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Pérdidas</p>
                                    <p class="font-bold text-lg">{{ $estadistica->perdidas }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Equipo Visitante (Misma estructura pero con colores rojos) -->
            <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-red-50 dark:bg-red-900/20">
                    <h3 class="text-xl font-bold text-red-800 dark:text-red-300 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z"></path>
                        </svg>
                        {{ $juego->equipoVisitante->nombre }}
                    </h3>
                </div>
                
                <!-- Tabs Visitante -->
                <div class="border-b border-gray-200 dark:border-dark-700">
                    <div class="flex">
                        <button @click="activeTabVisitante = 'resumen-visitante'" 
                                :class="activeTabVisitante === 'resumen-visitante' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="px-4 py-2 border-b-2 font-medium text-sm hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            Resumen
                        </button>
                        <button @click="activeTabVisitante = 'anotacion-visitante'" 
                                :class="activeTabVisitante === 'anotacion-visitante' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="px-4 py-2 border-b-2 font-medium text-sm hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            Anotación
                        </button>
                        <button @click="activeTabVisitante = 'defensa-visitante'" 
                                :class="activeTabVisitante === 'defensa-visitante' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="px-4 py-2 border-b-2 font-medium text-sm hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            Defensa
                        </button>
                        <button @click="activeTabVisitante = 'faltas-visitante'" 
                                :class="activeTabVisitante === 'faltas-visitante' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="px-4 py-2 border-b-2 font-medium text-sm hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            Faltas
                        </button>
                    </div>
                </div>

                <!-- Contenido de tabs Visitante -->
                <div class="p-6">
                    <!-- Resumen Visitante -->
                    <div x-show="activeTabVisitante === 'resumen-visitante'" class="space-y-4">
                        @foreach($estadisticasVisitante as $estadistica)
                        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-dark-600 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ $estadistica->numero_camiseta }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 dark:text-white">{{ $estadistica->jugador->nombre }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $estadistica->posicion_jugada }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $estadistica->puntos }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $estadistica->minutos_jugados }}min</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-2 text-xs text-gray-600 dark:text-gray-400">
                                <div class="text-center">
                                    <p class="font-medium">REB</p>
                                    <p>{{ $estadistica->rebotes_totales }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-medium">AST</p>
                                    <p>{{ $estadistica->asistencias }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-medium">ROB</p>
                                    <p>{{ $estadistica->robos }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-medium">BLQ</p>
                                    <p>{{ $estadistica->bloqueos }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- Totales Visitante -->
                        <div class="bg-red-100 dark:bg-red-900/30 rounded-lg p-4 border-2 border-red-200 dark:border-red-700">
                            <h4 class="font-bold text-red-800 dark:text-red-300 mb-3">Totales del Equipo</h4>
                            <div class="grid grid-cols-4 gap-4 text-sm">
                                <div class="text-center">
                                    <p class="font-bold text-red-600 dark:text-red-400 text-lg">{{ $totalesVisitante['puntos'] }}</p>
                                    <p class="text-red-700 dark:text-red-300">Puntos</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-bold text-red-600 dark:text-red-400 text-lg">{{ $totalesVisitante['rebotes_totales'] }}</p>
                                    <p class="text-red-700 dark:text-red-300">Rebotes</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-bold text-red-600 dark:text-red-400 text-lg">{{ $totalesVisitante['asistencias'] }}</p>
                                    <p class="text-red-700 dark:text-red-300">Asistencias</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-bold text-red-600 dark:text-red-400 text-lg">{{ $totalesVisitante['robos'] }}</p>
                                    <p class="text-red-700 dark:text-red-300">Robos</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Anotación Visitante -->
                    <div x-show="activeTabVisitante === 'anotacion-visitante'" class="space-y-4">
                        @foreach($estadisticasVisitante as $estadistica)
                        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ $estadistica->numero_camiseta }}
                                    </div>
                                    <p class="font-bold text-gray-800 dark:text-white">{{ $estadistica->jugador->nombre }}</p>
                                </div>
                                <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ $estadistica->puntos }} pts</p>
                            </div>
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div class="text-center">
                                    <p class="text-gray-600 dark:text-gray-400">Tiros Libres</p>
                                    <p class="font-bold">{{ $estadistica->tiros_libres_anotados }}/{{ $estadistica->tiros_libres_intentados }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $estadistica->tiros_libres_intentados > 0 ? round(($estadistica->tiros_libres_anotados / $estadistica->tiros_libres_intentados) * 100, 1) : 0 }}%
                                    </p>
                                </div>
                                <div class="text-center">
                                    <p class="text-gray-600 dark:text-gray-400">Tiros 2pts</p>
                                    <p class="font-bold">{{ $estadistica->tiros_2pts_anotados }}/{{ $estadistica->tiros_2pts_intentados }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $estadistica->tiros_2pts_intentados > 0 ? round(($estadistica->tiros_2pts_anotados / $estadistica->tiros_2pts_intentados) * 100, 1) : 0 }}%
                                    </p>
                                </div>
                                <div class="text-center">
                                    <p class="text-gray-600 dark:text-gray-400">Tiros 3pts</p>
                                    <p class="font-bold">{{ $estadistica->tiros_3pts_anotados }}/{{ $estadistica->tiros_3pts_intentados }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $estadistica->tiros_3pts_intentados > 0 ? round(($estadistica->tiros_3pts_anotados / $estadistica->tiros_3pts_intentados) * 100, 1) : 0 }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Defensa Visitante -->
                    <div x-show="activeTabVisitante === 'defensa-visitante'" class="space-y-4">
                        @foreach($estadisticasVisitante as $estadistica)
                        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ $estadistica->numero_camiseta }}
                                    </div>
                                    <p class="font-bold text-gray-800 dark:text-white">{{ $estadistica->jugador->nombre }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-4 text-sm text-center">
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Rebotes Def.</p>
                                    <p class="font-bold text-lg">{{ $estadistica->rebotes_defensivos }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Rebotes Of.</p>
                                    <p class="font-bold text-lg">{{ $estadistica->rebotes_ofensivos }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Robos</p>
                                    <p class="font-bold text-lg">{{ $estadistica->robos }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Bloqueos</p>
                                    <p class="font-bold text-lg">{{ $estadistica->bloqueos }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Faltas Visitante -->
                    <div x-show="activeTabVisitante === 'faltas-visitante'" class="space-y-4">
                        @foreach($estadisticasVisitante as $estadistica)
                        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ $estadistica->numero_camiseta }}
                                    </div>
                                    <p class="font-bold text-gray-800 dark:text-white">{{ $estadistica->jugador->nombre }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-4 text-sm text-center">
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Personales</p>
                                    <p class="font-bold text-lg">{{ $estadistica->faltas_personales }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Técnicas</p>
                                    <p class="font-bold text-lg">{{ $estadistica->faltas_tecnicas }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Descalif.</p>
                                    <p class="font-bold text-lg">{{ $estadistica->faltas_descalificantes }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Pérdidas</p>
                                    <p class="font-bold text-lg">{{ $estadistica->perdidas }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparación de equipos -->
        <div class="mt-8 bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900">
                <h3 class="text-xl font-bold text-black dark:text-primary-400">Comparación de Equipos</h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Puntos -->
                    <div class="text-center">
                        <h4 class="font-bold text-gray-800 dark:text-white mb-4">Puntos Totales</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-blue-600 dark:text-blue-400 font-medium">{{ $juego->equipoLocal->nombre }}</span>
                                <span class="font-bold text-blue-600 dark:text-blue-400">{{ $totalesLocal['puntos'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-red-600 dark:text-red-400 font-medium">{{ $juego->equipoVisitante->nombre }}</span>
                                <span class="font-bold text-red-600 dark:text-red-400">{{ $totalesVisitante['puntos'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rebotes -->
                    <div class="text-center">
                        <h4 class="font-bold text-gray-800 dark:text-white mb-4">Rebotes Totales</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-blue-600 dark:text-blue-400 font-medium">Local</span>
                                <span class="font-bold text-blue-600 dark:text-blue-400">{{ $totalesLocal['rebotes_totales'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-red-600 dark:text-red-400 font-medium">Visitante</span>
                                <span class="font-bold text-red-600 dark:text-red-400">{{ $totalesVisitante['rebotes_totales'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Asistencias -->
                    <div class="text-center">
                        <h4 class="font-bold text-gray-800 dark:text-white mb-4">Asistencias</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-blue-600 dark:text-blue-400 font-medium">Local</span>
                                <span class="font-bold text-blue-600 dark:text-blue-400">{{ $totalesLocal['asistencias'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-red-600 dark:text-red-400 font-medium">Visitante</span>
                                <span class="font-bold text-red-600 dark:text-red-400">{{ $totalesVisitante['asistencias'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Robos y Bloqueos -->
                    <div class="text-center">
                        <h4 class="font-bold text-gray-800 dark:text-white mb-4">Defensa</h4>
                        <div class="space-y-2">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Robos</div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-blue-600 dark:text-blue-400 font-medium">{{ $totalesLocal['robos'] }}</span>
                                <span class="text-red-600 dark:text-red-400 font-medium">{{ $totalesVisitante['robos'] }}</span>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Bloqueos</div>
                            <div class="flex justify-between items-center">
                                <span class="text-blue-600 dark:text-blue-400 font-medium">{{ $totalesLocal['bloqueos'] }}</span>
                                <span class="text-red-600 dark:text-red-400 font-medium">{{ $totalesVisitante['bloqueos'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Porcentajes de tiro -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <h4 class="font-bold text-gray-800 dark:text-white mb-4">Tiros Libres</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-blue-600 dark:text-blue-400 font-medium">Local</span>
                                <span class="font-bold">
                                    {{ $totalesLocal['tiros_libres_anotados'] }}/{{ $totalesLocal['tiros_libres_intentados'] }}
                                    ({{ $totalesLocal['tiros_libres_intentados'] > 0 ? round(($totalesLocal['tiros_libres_anotados'] / $totalesLocal['tiros_libres_intentados']) * 100, 1) : 0 }}%)
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-red-600 dark:text-red-400 font-medium">Visitante</span>
                                <span class="font-bold">
                                    {{ $totalesVisitante['tiros_libres_anotados'] }}/{{ $totalesVisitante['tiros_libres_intentados'] }}
                                    ({{ $totalesVisitante['tiros_libres_intentados'] > 0 ? round(($totalesVisitante['tiros_libres_anotados'] / $totalesVisitante['tiros_libres_intentados']) * 100, 1) : 0 }}%)
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <h4 class="font-bold text-gray-800 dark:text-white mb-4">Tiros 2 Puntos</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-blue-600 dark:text-blue-400 font-medium">Local</span>
                                <span class="font-bold">
                                    {{ $totalesLocal['tiros_2pts_anotados'] }}/{{ $totalesLocal['tiros_2pts_intentados'] }}
                                    ({{ $totalesLocal['tiros_2pts_intentados'] > 0 ? round(($totalesLocal['tiros_2pts_anotados'] / $totalesLocal['tiros_2pts_intentados']) * 100, 1) : 0 }}%)
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-red-600 dark:text-red-400 font-medium">Visitante</span>
                                <span class="font-bold">
                                    {{ $totalesVisitante['tiros_2pts_anotados'] }}/{{ $totalesVisitante['tiros_2pts_intentados'] }}
                                    ({{ $totalesVisitante['tiros_2pts_intentados'] > 0 ? round(($totalesVisitante['tiros_2pts_anotados'] / $totalesVisitante['tiros_2pts_intentados']) * 100, 1) : 0 }}%)
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <h4 class="font-bold text-gray-800 dark:text-white mb-4">Tiros 3 Puntos</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-blue-600 dark:text-blue-400 font-medium">Local</span>
                                <span class="font-bold">
                                    {{ $totalesLocal['tiros_3pts_anotados'] }}/{{ $totalesLocal['tiros_3pts_intentados'] }}
                                    ({{ $totalesLocal['tiros_3pts_intentados'] > 0 ? round(($totalesLocal['tiros_3pts_anotados'] / $totalesLocal['tiros_3pts_intentados']) * 100, 1) : 0 }}%)
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-red-600 dark:text-red-400 font-medium">Visitante</span>
                                <span class="font-bold">
                                    {{ $totalesVisitante['tiros_3pts_anotados'] }}/{{ $totalesVisitante['tiros_3pts_intentados'] }}
                                    ({{ $totalesVisitante['tiros_3pts_intentados'] > 0 ? round(($totalesVisitante['tiros_3pts_anotados'] / $totalesVisitante['tiros_3pts_intentados']) * 100, 1) : 0 }}%)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Observaciones -->
        @if($juego->observaciones)
        <div class="mt-8 bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900">
                <h3 class="text-xl font-bold text-black dark:text-primary-400">Observaciones del Partido</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-800 dark:text-white">{{ $juego->observaciones }}</p>
            </div>
        </div>
        @endif

        <!-- Acciones -->
        <div class="mt-8 flex justify-center space-x-4">
            <a href="{{ route('arbitro.dashboard') }}" 
               class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Volver al Dashboard
            </a>
            
            <button onclick="window.print()" 
                    class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Imprimir Estadísticas
            </button>
        </div>
    </div>
</main>

<script>
// Inicializar con tabs activos independientes para cada equipo
document.addEventListener('alpine:init', () => {
    Alpine.data('estadisticas', () => ({
        activeTabLocal: 'resumen-local',
        activeTabVisitante: 'resumen-visitante'
    }))
})
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white !important;
        color: black !important;
    }
    
    .bg-gradient-to-r {
        background: #6366f1 !important;
        color: white !important;
    }
}
</style>
@endsection