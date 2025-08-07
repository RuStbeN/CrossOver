@extends('layouts.app')

@section('content')

        <!-- Contenido principal -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header de bienvenida -->
            <div class="rounded-xl p-8 mb-8 glass-effect-light dark:glass-effect-dark">
                <h1 class="text-3xl font-bold mb-2 bg-gradient-to-r from-primary-500 to-yellow-500 bg-clip-text text-transparent [text-shadow:0_2px_4px_rgba(0,0,0,0.1)]">
                    Bienvenido, {{ Auth::user()->name }}
                </h1>
                <p class="text-lg text-gray-800 dark:text-gray-200">
                    Panel de control del sistema deportivo
                </p>
            </div>

            
            <!-- Sección de estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-4">
                <!-- Tarjeta 1 - Ligas -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-700 dark:to-blue-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-white/80">Ligas</p>
                            <p class="text-2xl font-bold text-white">{{ $stats['total_ligas'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 2 - Categorías -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-700 dark:to-purple-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-white/80">Categorías</p>
                            <p class="text-2xl font-bold text-white">{{ $stats['total_categorias'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 3 - Equipos -->
                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-700 dark:to-indigo-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-white/80">Equipos</p>
                            <p class="text-2xl font-bold text-white">{{ $stats['total_equipos'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 4 - Temporadas -->
                <div class="bg-gradient-to-br from-violet-500 to-violet-600 dark:from-violet-700 dark:to-violet-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-white/80">Temporadas</p>
                            <p class="text-2xl font-bold text-white">{{ $stats['total_temporadas'] }}</p>
                        </div>
                    </div>
                </div>

                
            </div>


            <!-- Sección de Partidos -->
            <div class="mb-4">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">Partidos y Eventos</h2>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Próximos partidos -->
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 dark:from-yellow-600 dark:to-yellow-700 rounded-xl shadow-lg overflow-hidden">
                        <div class="px-4 py-3 border-b border-white/20">
                            <h3 class="text-md font-medium text-white">Próximos Partidos</h3>
                        </div>
                        <div class="p-4">
                            @if($proximosPartidos->count() > 0)
                                <div class="space-y-2">
                                    @foreach($proximosPartidos->take(3) as $partido)
                                        <div class="flex justify-between items-center p-2 bg-white/5 rounded hover:bg-white/10 transition-colors">
                                            <div class="truncate">
                                                <div class="flex items-center space-x-1">
                                                    <span class="text-xs font-medium text-white/70">
                                                        {{ $partido->fecha->format('d/m') }} {{ $partido->hora }}
                                                    </span>
                                                    @if($partido->cancha)
                                                        <span class="text-xs text-white/50">• {{ $partido->cancha->nombre }}</span>
                                                    @endif
                                                </div>
                                                <h4 class="text-sm font-medium text-white truncate">
                                                    {{ $partido->equipoLocal->nombre ?? 'Local' }} vs {{ $partido->equipoVisitante->nombre ?? 'Visitante' }}
                                                </h4>
                                            </div>
                                            <div>
                                                @php
                                                    $fechaPartido = \Carbon\Carbon::parse($partido->fecha->format('Y-m-d') . ' ' . $partido->hora);
                                                    $ahora = now();
                                                    
                                                    if ($fechaPartido->isToday()) {
                                                        $estado = 'Hoy';
                                                        $colorClass = 'bg-yellow-500/20 text-yellow-100';
                                                    } elseif ($fechaPartido->isTomorrow()) {
                                                        $estado = 'Mañana';
                                                        $colorClass = 'bg-green-500/20 text-green-100';
                                                    } elseif ($fechaPartido->diffInDays($ahora) <= 7) {
                                                        $estado = substr($fechaPartido->locale('es')->dayName, 0, 3);
                                                        $colorClass = 'bg-blue-500/20 text-blue-100';
                                                    } else {
                                                        $estado = 'Próximo';
                                                        $colorClass = 'bg-white/20 text-white';
                                                    }
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs {{ $colorClass }}">
                                                    @php
                                                        $fechaPartido = \Carbon\Carbon::parse($partido->fecha->format('Y-m-d') . ' ' . $partido->hora);
                                                        $ahora = now();
                                                        
                                                        if ($fechaPartido->isToday()) {
                                                            echo 'Hoy';
                                                        } elseif ($fechaPartido->isTomorrow()) {
                                                            echo 'Mañana';
                                                        } elseif ($fechaPartido->diffInDays($ahora) <= 7) {
                                                            // Usamos el formato 'D' para obtener el nombre corto del día sin tildes
                                                            echo $fechaPartido->isoFormat('ddd'); // Esto mostrará "lun", "mar", etc.
                                                        } else {
                                                            echo 'Próximo';
                                                        }
                                                    @endphp
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-white/70 text-xs">No hay partidos próximos</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Juegos en vivo -->
                    <div class="bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-xl shadow-lg overflow-hidden">
                        <div class="px-4 py-3 border-b border-white/20">
                            <h3 class="text-md font-medium text-white flex items-center">
                                <span class="w-2 h-2 bg-white rounded-full animate-pulse mr-2"></span>
                                Juegos en Vivo
                            </h3>
                        </div>
                        <div class="p-4">
                            @if($juegosEnVivo->count() > 0)
                                <div class="space-y-2">
                                    @foreach($juegosEnVivo->take(3) as $juego)
                                        <div class="flex justify-between items-center p-2 bg-white/5 rounded hover:bg-white/10 transition-colors">
                                            <div class="truncate">
                                                <div class="flex items-center space-x-1">
                                                    @if($juego->cancha)
                                                        <span class="text-xs text-white/50">{{ $juego->cancha->nombre }}</span>
                                                    @endif
                                                </div>
                                                <h4 class="text-sm font-medium text-white truncate">
                                                    {{ $juego->equipoLocal->nombre ?? 'Local' }} vs {{ $juego->equipoVisitante->nombre ?? 'Visitante' }}
                                                </h4>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white">
                                                    {{ $juego->puntos_local ?? 0 }}-{{ $juego->puntos_visitante ?? 0 }}
                                                </span>
                                                <p class="text-xs text-white/70 mt-1">
                                                    @php
                                                        $fechaHoraInicio = \Carbon\Carbon::parse($juego->fecha->format('Y-m-d') . ' ' . $juego->hora);
                                                        $tiempoTranscurrido = now()->diffInMinutes($fechaHoraInicio);
                                                        $duracionCuarto = $juego->duracion_cuarto ?? 12;
                                                        $duracionDescanso = $juego->duracion_descanso ?? 2;
                                                        
                                                        if ($tiempoTranscurrido <= 0) {
                                                            echo "Por comenzar";
                                                        } elseif ($tiempoTranscurrido <= $duracionCuarto) {
                                                            echo "Q1 - {$tiempoTranscurrido}'";
                                                        } elseif ($tiempoTranscurrido <= ($duracionCuarto + $duracionDescanso)) {
                                                            echo "Descanso";
                                                        } elseif ($tiempoTranscurrido <= ($duracionCuarto * 2 + $duracionDescanso)) {
                                                            $minutos = $tiempoTranscurrido - $duracionCuarto - $duracionDescanso;
                                                            echo "Q2 - {$minutos}'";
                                                        } elseif ($tiempoTranscurrido <= ($duracionCuarto * 2 + $duracionDescanso * 2)) {
                                                            echo "Medio T.";
                                                        } elseif ($tiempoTranscurrido <= ($duracionCuarto * 3 + $duracionDescanso * 2)) {
                                                            $minutos = $tiempoTranscurrido - ($duracionCuarto * 2) - ($duracionDescanso * 2);
                                                            echo "Q3 - {$minutos}'";
                                                        } elseif ($tiempoTranscurrido <= ($duracionCuarto * 3 + $duracionDescanso * 3)) {
                                                            echo "Descanso";
                                                        } elseif ($tiempoTranscurrido <= ($duracionCuarto * 4 + $duracionDescanso * 3)) {
                                                            $minutos = $tiempoTranscurrido - ($duracionCuarto * 3) - ($duracionDescanso * 3);
                                                            echo "Q4 - {$minutos}'";
                                                        } else {
                                                            echo "T. Extra";
                                                        }
                                                    @endphp
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-white/70 text-xs">No hay juegos en vivo</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Resultados -->
                    <div class="bg-gradient-to-br from-red-500 to-red-600 dark:from-red-600 dark:to-red-700 rounded-xl shadow-lg overflow-hidden">
                        <div class="px-4 py-3 border-b border-white/20">
                            <h3 class="text-md font-medium text-white">Resultados Recientes</h3>
                        </div>
                        <div class="p-4">
                            @if($resultadosRecientes->count() > 0)
                                <div class="space-y-2">
                                    @foreach($resultadosRecientes as $partido)
                                        <div class="flex justify-between items-center p-2 bg-white/5 rounded hover:bg-white/10 transition-colors">
                                            <div class="truncate">
                                                <div class="flex items-center space-x-1">
                                                    @if($partido->cancha)
                                                        <span class="text-xs text-white/50">{{ $partido->cancha->nombre }}</span>
                                                    @endif
                                                </div>
                                                <h4 class="text-sm font-medium text-white truncate">
                                                    {{ $partido->equipoLocal->nombre ?? 'Local' }} vs {{ $partido->equipoVisitante->nombre ?? 'Visitante' }}
                                                </h4>
                                            </div>
                                            <div class="text-right min-w-[80px]">
                                                <!-- Marcador con campos unificados -->
                                                <p class="text-lg font-bold text-white">
                                                    {{ $partido->puntos_local ?? $partido->goles_local ?? 0 }}-{{ $partido->puntos_visitante ?? $partido->goles_visitante ?? 0 }}
                                                </p>
                                                <div class="flex justify-between items-center">
                                                    @if($partido->torneo)
                                                        <span class="text-xs text-white/60">{{ $partido->torneo->nombre }}</span>
                                                    @endif
                                                    <span class="text-xs text-white/60 ml-2">
                                                        {{ $partido->fecha->format('d/m') }}  <!-- Fecha completa -->
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-white/70 text-xs">No hay resultados recientes</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                </div>
            </div>

            

            <!-- Sección de Gestión -->
            <div class="mb-4">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">
                    Gestión y Actividades
                </h2>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Columna 1: Torneos por iniciar (Programados) -->
                    <div class="bg-gradient-to-br from-teal-500 to-teal-600 dark:from-teal-700 dark:to-teal-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-white/20">
                            <h3 class="text-lg font-medium text-white">Torneos por Iniciar</h3>
                        </div>
                        <div class="p-6">
                            @if($torneosProgramados->count() > 0)
                                <div class="space-y-3">
                                    @foreach($torneosProgramados as $torneo)
                                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 hover:bg-white/20 transition-colors">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="font-medium text-white">{{ $torneo->nombre }}</p>
                                                    <p class="text-xs text-white/70">
                                                        {{ $torneo->fecha_inicio->format('d M') }} - {{ $torneo->fecha_fin->format('d M Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-2 flex gap-1 flex-wrap">
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-teal-900/30 text-teal-100">
                                                    {{ $torneo->tipo_formateado }}
                                                </span>
                                                @if($torneo->liga)
                                                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-900/30 text-blue-100">
                                                        {{ $torneo->liga->nombre }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-white/70">
                                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-sm">No hay torneos programados</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actividades Recientes -->
                    <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 dark:from-cyan-700 dark:to-cyan-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-white/10">
                            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                                <i class="fas fa-bolt text-amber-200"></i> 
                                <span>Actividades Recientes</span>
                            </h3>
                        </div>
                        <div class="p-6">
                            @if($actividadesRecientes->count() > 0)
                                <div class="space-y-3">
                                    @foreach($actividadesRecientes as $actividad)
                                        <div class="flex items-center gap-3 group"> <!-- Cambiado a items-center -->
                                            <!-- Icono realineado -->
                                            <div class="flex-shrink-0 p-2 rounded-lg bg-white/5 group-hover:bg-white/10 transition-all flex items-center justify-center h-8 w-8"> <!-- Tamaño fijo -->
                                                <i class="fas {{ $actividad['icono'] }} text-sm text-cyan-300 opacity-80"></i> <!-- Aumentado a text-sm -->
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-baseline gap-1.5">
                                                    <p class="text-sm font-medium text-white truncate">{{ $actividad['descripcion'] }}</p>
                                                    <span class="text-xs text-white/40">{{ $actividad['created_at']->diffForHumans() }}</span>
                                                </div>
                                                @if($actividad['user'])
                                                    <p class="text-xs text-cyan-200/80 mt-0.5">por {{ $actividad['user']->name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6 text-white/60">
                                    <i class="fas fa-inbox text-3xl mb-2 opacity-40"></i>
                                    <p class="text-sm">No hay actividades recientes</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Acciones rápidas -->
                    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 dark:from-emerald-700 dark:to-emerald-800 rounded-xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-white/20">
                            <h3 class="text-lg font-medium text-white">Acciones Rápidas</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4">
                                <button class="bg-white/20 text-white rounded-lg p-3 hover:bg-white/30 transition-colors flex flex-col items-center">
                                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span class="text-xs">Nuevo</span>
                                </button>
                                <button class="bg-white/20 text-white rounded-lg p-3 hover:bg-white/30 transition-colors flex flex-col items-center">
                                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span class="text-xs">Editar</span>
                                </button>
                                <button class="bg-white/20 text-white rounded-lg p-3 hover:bg-white/30 transition-colors flex flex-col items-center">
                                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <span class="text-xs">Reportes</span>
                                </button>
                                <button class="bg-white/20 text-white rounded-lg p-3 hover:bg-white/30 transition-colors flex flex-col items-center">
                                    <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-xs">Config</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection