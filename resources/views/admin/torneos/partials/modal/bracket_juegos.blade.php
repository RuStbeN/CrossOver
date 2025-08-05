<!-- Bracket juegos optimizado -->
<div 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 max-h-0"
    x-transition:enter-end="opacity-100 max-h-screen"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 max-h-screen"
    x-transition:leave-end="opacity-0 max-h-0"
    class="overflow-hidden transition-all duration-300 ease-in-out"
>
    <div class="p-4 space-y-4">
    <!-- Vista Bracket con Mini-Mapa y Zoom -->
    <!-- Título fuera del contenedor -->
    <div class="px-6 py-4">
        <h1 class="text-2xl md:text-3xl font-extrabold text-center text-gray-800 dark:text-white tracking-tight">
            🏆 Bracket del Torneo
        </h1>
    </div>

        <!-- Contenedor principal -->
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-sm" 
            :class="{ 'fixed top-16 left-6 right-6 bottom-2 z-50 rounded-lg': isFullscreen }"
            x-data="{ 
                isFullscreen: false, 
                zoomLevel: 0.5, // Valor para zoom inicial en mini bracket
                minZoom: 0.2,
                maxZoom: 1.5,
                fullscreenZoom: 0.8 // Valor para zoom inicial en pantalla completa
            }">
            
            <!-- Header con controles -->
            <div class="p-4 border-b border-gray-200 dark:border-dark-600 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <p class="text-sm text-gray-600 dark:text-dark-300" x-show="!isFullscreen">
                        Vista Mini-Mapa del Bracket
                    </p>
                    <p class="text-sm text-gray-600 dark:text-dark-300" x-show="isFullscreen">
                        🏆 Bracket Completo - Navega y haz zoom
                    </p>
                </div>
                
                <div class="flex items-center space-x-2">
                    <!-- Controles de zoom (solo en vista normal) -->
                    <div x-show="!isFullscreen" class="flex items-center space-x-1 bg-gray-100 dark:bg-dark-700 rounded-lg p-1">
                        <!-- Zoom Out -->
                        <button @click="zoomLevel = Math.max(minZoom, zoomLevel - 0.1)"
                            class="flex items-center justify-center w-8 h-8 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors"
                            title="Reducir zoom">
                            <svg class="w-4 h-4 text-gray-600 dark:text-dark-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        
                        <!-- Indicador de zoom -->
                        <span class="text-xs text-gray-500 dark:text-dark-400 px-2 min-w-12 text-center" 
                            x-text="Math.round(zoomLevel * 100) + '%'"></span>
                        
                        <!-- Zoom In -->
                        <button @click="zoomLevel = Math.min(maxZoom, zoomLevel + 0.1)"
                            class="flex items-center justify-center w-8 h-8 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors"
                            title="Aumentar zoom">
                            <svg class="w-4 h-4 text-gray-600 dark:text-dark-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Botón expandir -->
                    <button x-show="!isFullscreen"
                        @click="isFullscreen = true; zoomLevel = fullscreenZoom" // Usamos fullscreenZoom en lugar de 1
                        class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/20 hover:bg-blue-200 dark:hover:bg-blue-900/40 transition-colors duration-200 group"
                        title="Ver bracket completo">
                        
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 group-hover:text-blue-800 dark:group-hover:text-blue-300 transition-colors" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                    </button>
                    
                    <!-- Controles de zoom en pantalla completa -->
                    <div x-show="isFullscreen" class="flex items-center space-x-1 bg-gray-100 dark:bg-dark-700 rounded-lg p-1">
                        <!-- Zoom Out -->
                        <button @click="zoomLevel = Math.max(0.5, zoomLevel - 0.1)"
                            class="flex items-center justify-center w-8 h-8 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors"
                            title="Reducir zoom">
                            <svg class="w-4 h-4 text-gray-600 dark:text-dark-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        
                        <!-- Reset zoom -->
                        <button @click="zoomLevel = fullscreenZoom" // Reset al zoom de pantalla completa
                            class="text-xs text-gray-500 dark:text-dark-400 px-2 hover:bg-gray-200 dark:hover:bg-dark-600 rounded"
                            title="Zoom 100%">
                            <span x-text="Math.round(zoomLevel * 100) + '%'"></span>
                        </button>
                        
                        <!-- Zoom In -->
                        <button @click="zoomLevel = Math.min(2, zoomLevel + 0.1)"
                            class="flex items-center justify-center w-8 h-8 rounded hover:bg-gray-200 dark:hover:bg-dark-600 transition-colors"
                            title="Aumentar zoom">
                            <svg class="w-4 h-4 text-gray-600 dark:text-dark-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Botón cerrar (solo en pantalla completa) -->
                    <button x-show="isFullscreen"
                        @click="isFullscreen = false; zoomLevel = 0.5" // Volvemos al 50% al cerrar
                        class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/20 hover:bg-red-200 dark:hover:bg-red-900/40 transition-colors duration-200 group"
                        title="Cerrar pantalla completa">
                        
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 group-hover:text-red-800 dark:group-hover:text-red-300 transition-colors" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Contenedor con scroll y zoom -->
            <div class="overflow-auto custom-scrollbar"
                :class="isFullscreen ? 'h-[calc(100vh-144px)]' : 'h-64'">
                <div class="p-6 min-w-max transition-transform duration-200 origin-top-left"
                    :style="'transform: scale(' + zoomLevel + ')'">
                    <div class="flex space-x-16 justify-start items-start">
                        <template x-for="(juegos, ronda) in getJuegosPorRonda()" :key="'ronda-' + ronda">
                            <div class="space-y-8">
                                <!-- Título de la ronda -->
                                <div class="text-center mb-6">
                                    <h5 class="text-lg font-bold text-gray-800 dark:text-white bg-primary-100 dark:bg-primary-900/20 rounded-lg py-2 px-4 inline-block">
                                        Ronda <span x-text="ronda"></span>
                                    </h5>
                                </div>
                                
                                <!-- Juegos de la ronda -->
                                <template x-for="juego in juegos" :key="'juego-bracket-' + juego.id">
                                    <div class="relative">
                                        <!-- Línea conectora (solo si no es la última ronda) -->
                                        <template x-if="parseInt(ronda) < Math.max(...Object.keys(getJuegosPorRonda()).map(r => parseInt(r)))">
                                            <div class="absolute top-1/2 -right-8 w-8 h-0.5 bg-gray-300 dark:bg-dark-600 bracket-line"></div>
                                        </template>
                                        
                                        <!-- Card del juego -->
                                        <div class="bg-white dark:bg-dark-700 border-2 rounded-lg p-4 w-64 shadow-md hover:shadow-lg transition-all duration-200 hover:border-primary-500 dark:hover:border-primary-400"
                                            :class="{
                                                'border-green-300 bg-green-50 dark:border-green-600 dark:bg-green-900/10': juego.estado === 'Finalizado',
                                                'border-blue-300 bg-blue-50 dark:border-blue-600 dark:bg-blue-900/10': juego.estado === 'En Curso',
                                                'border-yellow-300 bg-yellow-50 dark:border-yellow-600 dark:bg-yellow-900/10': juego.estado === 'Programado',
                                                'border-gray-300 dark:border-dark-600': juego.estado === 'Cancelado'
                                            }">
                                            
                                            <!-- Estado del juego -->
                                            <div class="text-center mb-3">
                                                <span class="px-3 py-1 text-xs rounded-full font-medium" 
                                                    :class="{
                                                        'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400': juego.estado === 'Finalizado',
                                                        'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400': juego.estado === 'En Curso',
                                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400': juego.estado === 'Programado',
                                                        'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400': juego.estado === 'Cancelado'
                                                    }"
                                                    x-text="juego.estado">
                                                </span>
                                            </div>

                                            <!-- Equipo Local -->
                                            <div class="flex items-center justify-between mb-2 p-2 rounded"
                                                :class="getGanador(juego) === juego.equipo_local_id ? 'bg-green-100 border border-green-300 dark:bg-green-900/30 dark:border-green-500' : 'bg-gray-50 dark:bg-dark-600'">
                                                <div class="flex items-center space-x-2 flex-1 min-w-0">
                                                    <template x-for="equipo in selectedTorneo.equipos" :key="'bracket-local-' + equipo.id">
                                                        <template x-if="equipo.id == juego.equipo_local_id">
                                                            <div class="flex items-center space-x-2 flex-1 min-w-0">
                                                                <!-- Logo del equipo local -->
                                                                <template x-if="equipo.logo_url">
                                                                    <img 
                                                                        :src="'/storage/' + equipo.logo_url" 
                                                                        class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                                                                        :alt="'Logo ' + equipo.nombre"
                                                                    >
                                                                </template>
                                                                <template x-if="!equipo.logo_url">
                                                                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center flex-shrink-0">
                                                                        <span class="text-xs text-white font-medium" 
                                                                            x-text="equipo.nombre.substring(0, 1).toUpperCase()"></span>
                                                                    </div>
                                                                </template>
                                                                <!-- Nombre del equipo local -->
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-sm font-medium text-gray-800 dark:text-white truncate" 
                                                                    x-text="equipo.nombre"></p>
                                                                    <p class="text-xs text-gray-500 dark:text-dark-300">Local</p>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </template>
                                                </div>
                                                <div class="text-right flex-shrink-0 ml-2">
                                                    <template x-if="juego.estado === 'Finalizado' && juego.puntos_local !== null">
                                                        <span class="text-lg font-bold text-gray-800 dark:text-white" 
                                                            x-text="juego.puntos_local"></span>
                                                    </template>
                                                    <template x-if="juego.estado !== 'Finalizado' || juego.puntos_local === null">
                                                        <span class="text-lg font-bold text-gray-400">-</span>
                                                    </template>
                                                </div>
                                            </div>

                                            <!-- VS -->
                                            <div class="text-center py-1">
                                                <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">VS</span>
                                            </div>

                                            <!-- Equipo Visitante -->
                                            <div class="flex items-center justify-between mb-3 p-2 rounded"
                                                :class="getGanador(juego) === juego.equipo_visitante_id ? 'bg-green-100 border border-green-300 dark:bg-green-900/30 dark:border-green-500' : 'bg-gray-50 dark:bg-dark-600'">
                                                <div class="flex items-center space-x-2 flex-1 min-w-0">
                                                    <template x-for="equipo in selectedTorneo.equipos" :key="'bracket-visitante-' + equipo.id">
                                                        <template x-if="equipo.id == juego.equipo_visitante_id">
                                                            <div class="flex items-center space-x-2 flex-1 min-w-0">
                                                                <!-- Logo del equipo visitante -->
                                                                <template x-if="equipo.logo_url">
                                                                    <img 
                                                                        :src="'/storage/' + equipo.logo_url" 
                                                                        class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                                                                        :alt="'Logo ' + equipo.nombre"
                                                                    >
                                                                </template>
                                                                <template x-if="!equipo.logo_url">
                                                                    <div class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0">
                                                                        <span class="text-xs text-white font-medium" 
                                                                            x-text="equipo.nombre.substring(0, 1).toUpperCase()"></span>
                                                                    </div>
                                                                </template>
                                                                <!-- Nombre del equipo visitante -->
                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-sm font-medium text-gray-800 dark:text-white truncate" 
                                                                    x-text="equipo.nombre"></p>
                                                                    <p class="text-xs text-gray-500 dark:text-dark-300">Visitante</p>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </template>
                                                </div>
                                                <div class="text-right flex-shrink-0 ml-2">
                                                    <template x-if="juego.estado === 'Finalizado' && juego.puntos_visitante !== null">
                                                        <span class="text-lg font-bold text-gray-800 dark:text-white" 
                                                            x-text="juego.puntos_visitante"></span>
                                                    </template>
                                                    <template x-if="juego.estado !== 'Finalizado' || juego.puntos_visitante === null">
                                                        <span class="text-lg font-bold text-gray-400">-</span>
                                                    </template>
                                                </div>
                                            </div>

                                            <!-- Información del juego -->
                                            <div class="text-center pt-2 border-t border-gray-200 dark:border-dark-600">
                                                <p class="text-xs text-gray-500 dark:text-dark-300" 
                                                    x-text="new Date(juego.fecha).toLocaleDateString('es-ES', {
                                                        year: 'numeric',
                                                        month: '2-digit',
                                                        day: '2-digit'
                                                    }) + ' - ' + juego.hora"></p>
                                                <template x-if="juego.cancha_id">
                                                    <p class="text-xs text-gray-500 dark:text-dark-300">Cancha <span x-text="juego.cancha_id"></span></p>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

                
            <!-- Indicador de ayuda -->
            <div x-show="!isFullscreen" class="px-4 py-2 bg-gray-50 dark:bg-dark-700 border-t border-gray-200 dark:border-dark-600">
                <p class="text-xs text-gray-500 dark:text-dark-400 text-center">
                    💡 Usa los controles de zoom para explorar o expande para vista completa
                </p>
            </div>
            
        </div>
    </div>
</div>
