<!-- Vista Lista Mejorada -->
<div x-show="activeView === 'lista'" class="space-y-6">
    <!-- Título fuera del contenedor -->
    <div class="px-6 py-4">
        <h1 class="text-2xl md:text-3xl font-extrabold text-center text-gray-800 dark:text-white tracking-tight">
            🎮 Lista de Juegos
        </h1>
    </div>

    <!-- Contenedor principal -->
    <div class="bg-white dark:bg-dark-800 rounded-lg shadow-sm">
        <!-- Contenedor sin scroll, cards van hacia abajo -->
        <div class="p-6">
            <!-- Grid de cards que van hacia abajo -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="(juego, index) in selectedTorneo.juegos || []" :key="juego.id">
                    <div class="bg-white dark:bg-dark-700 rounded-lg p-4 border border-gray-300 dark:border-dark-600
                        hover:shadow-lg transition-all duration-200 
                        hover:border-primary-500 dark:hover:border-primary-400">
                        <!-- Cabecera del juego -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                    Juego <span x-text="index + 1"></span>
                                </span>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full font-medium" 
                                :class="{
                                    'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400': juego.estado === 'Finalizado',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400': juego.estado === 'En Curso',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400': juego.estado === 'Programado',
                                    'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400': juego.estado === 'Cancelado'
                                }"
                                x-text="juego.estado">
                            </span>
                        </div>

                        <!-- Enfrentamiento -->
                        <div class="space-y-3">
                            <!-- Equipo Local -->
                            <div class="flex items-center justify-between p-2 rounded"
                                :class="getGanador(juego) === juego.equipo_local_id ? 
                                    'bg-green-100 border border-green-300 dark:bg-green-900/30 dark:border-green-500' : 
                                    'bg-gray-50 dark:bg-dark-600'">
                                <div class="flex items-center space-x-2 flex-1 min-w-0">
                                    <template x-for="equipo in selectedTorneo.equipos" :key="'local-' + equipo.id">
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
                                
                                <!-- Puntos Local -->
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

                            <!-- Separador VS -->
                            <div class="text-center">
                                <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">VS</span>
                            </div>

                            <!-- Equipo Visitante -->
                            <div class="flex items-center justify-between p-2 rounded"
                                :class="getGanador(juego) === juego.equipo_visitante_id ? 
                                    'bg-green-100 border border-green-300 dark:bg-green-900/20 dark:border-green-600' : 
                                    'bg-gray-50 dark:bg-dark-600'">
                                <div class="flex items-center space-x-2 flex-1 min-w-0">
                                    <template x-for="equipo in selectedTorneo.equipos" :key="'visitante-' + equipo.id">
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
                                
                                <!-- Puntos Visitante -->
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
                        </div>

                        <!-- Información adicional -->
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-dark-300">
                                <!-- Fecha formateada -->
                                <span x-text="new Date(juego.fecha).toLocaleDateString('es-ES', {
                                    year: 'numeric',
                                    month: '2-digit',
                                    day: '2-digit'
                                }) + ' - ' + juego.hora"></span>
                                <template x-if="juego.cancha_id">
                                    <span>Cancha <span x-text="juego.cancha_id"></span></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>