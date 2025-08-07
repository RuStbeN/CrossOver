<div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <!-- Logo del equipo -->
            <template x-if="selectedEquipo.logo_url">
                <img 
                    :src="'/storage/' + selectedEquipo.logo_url" 
                    class="w-16 h-16 rounded-full object-cover mr-4"
                    :alt="'Logo ' + selectedEquipo.nombre"
                >
            </template>
            <template x-if="!selectedEquipo.logo_url">
                <div class="w-16 h-16 rounded-full bg-gray-300 dark:bg-gray-600 mr-4 flex items-center justify-center">
                    <span class="text-xl text-gray-500 dark:text-gray-300 font-medium" 
                        x-text="selectedEquipo.nombre.substring(0, 1).toUpperCase()"></span>
                </div>
            </template>
            
            <div class="flex items-center gap-4">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white" x-text="selectedEquipo.nombre"></h3>
                <span class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap"
                    :class="selectedEquipo.activo ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
                    x-text="selectedEquipo.activo ? 'Activo' : 'Inactivo'">
                </span>
            </div>
        </div>
        
        <!-- Colores del equipo -->
        <div class="flex items-center space-x-2">
            <div class="w-6 h-6 rounded-full border border-gray-300 dark:border-gray-600" 
                :style="'background-color: ' + selectedEquipo.color_primario"></div>
            <div class="w-6 h-6 rounded-full border border-gray-300 dark:border-gray-600" 
                :style="'background-color: ' + selectedEquipo.color_secundario"></div>
        </div>
    </div>
    
    <!-- Información básica -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Categoría</p>
            <p class="font-medium text-gray-800 dark:text-white" x-text="selectedEquipo.categoria?.nombre || 'No especificada'"></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Liga</p>
            <p class="font-medium text-gray-800 dark:text-white" x-text="selectedEquipo.liga?.nombre || 'No especificada'"></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Entrenador</p>
            <p class="font-medium text-gray-800 dark:text-white" x-text="selectedEquipo.entrenador?.nombre || 'No especificado'"></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Fecha de fundación</p>
            <p class="font-medium text-gray-800 dark:text-white" 
            x-text="selectedEquipo.fecha_fundacion ? new Date(selectedEquipo.fecha_fundacion).toLocaleDateString('es-ES') : 'No especificada'">
            </p>
        </div>
    </div>
</div>

<!-- Estadísticas del equipo -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Juegos -->
    <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Estadísticas de Juegos
        </h4>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Juegos como local:</span>
                <span class="text-gray-800 dark:text-white font-medium" x-text="selectedEquipo.juegos_como_local?.length || 0"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Juegos como visitante:</span>
                <span class="text-gray-800 dark:text-white font-medium" x-text="selectedEquipo.juegos_como_visitante?.length || 0"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Total de juegos:</span>
                <span class="text-gray-800 dark:text-white font-medium" 
                    x-text="(selectedEquipo.juegos_como_local?.length || 0) + (selectedEquipo.juegos_como_visitante?.length || 0)">
                </span>
            </div>
        </div>
    </div>
    
    <!-- Jugadores -->
    <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Estadísticas de Jugadores
        </h4>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Total de jugadores:</span>
                <span class="text-gray-800 dark:text-white font-medium" x-text="selectedEquipo.jugadores?.length || 0"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Jugadores activos:</span>
                <span class="text-gray-800 dark:text-white font-medium"
                    x-text="selectedEquipo.jugadores?.filter(j => j.pivot.activo).length || 0">
                </span>
            </div>
        </div>
    </div>
</div>
