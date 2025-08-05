<!-- Información general -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Información básica - Estilo gris/blanco -->
    <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Información del Torneo
        </h4>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Tipo de Torneo:</span>
                <span 
                    class="text-gray-800 dark:text-white font-medium" 
                    x-text="
                        {
                            'eliminacion_directa': 'Eliminación Directa',
                            'doble_eliminacion': 'Doble Eliminación',
                            'round_robin': 'Round Robin',
                            'grupos_eliminacion': 'Grupos + Eliminación',
                            'por_puntos': 'Por Puntos'
                        }[selectedTorneo.tipo] || selectedTorneo.tipo
                    ">
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Liga:</span>
                <span class="text-gray-800 dark:text-white font-medium" x-text="selectedTorneo.liga?.nombre || 'No especificada'"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Temporada:</span>
                <span class="text-gray-800 dark:text-white font-medium" x-text="selectedTorneo.temporada?.nombre || 'No especificada'"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Categoría:</span>
                <span class="text-gray-800 dark:text-white font-medium" x-text="selectedTorneo.categoria?.nombre || 'No especificada'"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Duración por cuarto:</span>
                <span class="text-gray-800 dark:text-white font-medium" x-text="`${selectedTorneo.duracion_cuarto_minutos} minutos`"></span>
            </div>
        </div>
    </div>

    <!-- Configuración de puntos - Estilo azul -->
    <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Configuración de Puntos
        </h4>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Puntos por victoria:</span>
                <span class="text-gray-800 dark:text-white font-medium" x-text="selectedTorneo.puntos_por_victoria"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Puntos por empate:</span>
                <span class="text-gray-800 dark:text-white font-medium" x-text="selectedTorneo.puntos_por_empate"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Puntos por derrota:</span>
                <span class="text-gray-800 dark:text-white font-medium" x-text="selectedTorneo.puntos_por_derrota"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Premio total:</span>
                <span class="text-gray-800 dark:text-white font-medium" x-text="`$${selectedTorneo.premio_total}`"></span>
            </div>
        </div>
    </div>

    <!-- Configuración de playoffs - Estilo rojo -->
    <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Playoffs
        </h4>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Usa playoffs:</span>
                <span class="text-gray-800 dark:text-white font-medium" x-text="selectedTorneo.usa_playoffs ? 'Sí' : 'No'"></span>
            </div>
            <template x-if="selectedTorneo.usa_playoffs">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Equipos en playoffs:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedTorneo.equipos_playoffs"></span>
                </div>
            </template>
        </div>
    </div>
</div>