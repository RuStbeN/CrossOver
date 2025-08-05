<div class="space-y-6">
    <!-- Encabezado con equipos y marcador -->
    <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-6 text-center">
        <div class="flex items-center justify-center space-x-8">
            <!-- Equipo Local: Logo (izquierda) + Nombre (derecha) -->
            <div class="flex-1 flex items-center justify-end space-x-6">
                <!-- Logo - Tamaño aumentado a w-24 h-24 -->
                <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-dark-700 border-2 border-gray-300 dark:border-dark-600">
                    <template x-if="selectedJuego.equipo_local?.logo_url">
                        <img :src="`/storage/${selectedJuego.equipo_local.logo_url}`" 
                            :alt="selectedJuego.equipo_local.nombre" 
                            class="w-full h-full object-cover">
                    </template>
                    <template x-if="!selectedJuego.equipo_local?.logo_url">
                        <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-dark-800 text-gray-500 dark:text-gray-400">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3a9 9 0 110 18 9 9 0 010-18zm0 1a8 8 0 100 16 8 8 0 000-16z"/>
                            </svg>
                        </div>
                    </template>
                </div>
                <!-- Nombre y puntos - Centrado exactamente con el logo -->
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-800 dark:text-white leading-none" x-text="selectedJuego.equipo_local?.nombre || 'Equipo Local'"></div>
                    <div class="text-4xl font-bold text-primary-600 dark:text-primary-400 leading-none" x-text="selectedJuego.puntos_local || '-'"></div>
                </div>
            </div>

            <!-- Separador VS - Tamaño aumentado -->
            <div class="text-gray-500 dark:text-gray-400 font-bold text-2xl px-6">VS</div>

            <!-- Equipo Visitante: Nombre (izquierda) + Logo (derecha) -->
            <div class="flex-1 flex items-center space-x-6">
                <!-- Nombre y puntos - Centrado exactamente con el logo -->
                <div class="text-left">
                    <div class="text-2xl font-bold text-gray-800 dark:text-white leading-none" x-text="selectedJuego.equipo_visitante?.nombre || 'Equipo Visitante'"></div>
                    <div class="text-4xl font-bold text-primary-600 dark:text-primary-400 leading-none" x-text="selectedJuego.puntos_visitante || '-'"></div>
                </div>
                <!-- Logo - Tamaño aumentado a w-16 h-16 -->
                <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-dark-700 border-2 border-gray-300 dark:border-dark-600">
                    <template x-if="selectedJuego.equipo_visitante?.logo_url">
                        <img :src="`/storage/${selectedJuego.equipo_visitante.logo_url}`" 
                            :alt="selectedJuego.equipo_visitante.nombre" 
                            class="w-full h-full object-cover">
                    </template>
                    <template x-if="!selectedJuego.equipo_visitante?.logo_url">
                        <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-dark-800 text-gray-500 dark:text-gray-400">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3a9 9 0 110 18 9 9 0 010-18zm0 1a8 8 0 100 16 8 8 0 000-16z"/>
                            </svg>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        
        <!-- Estado y fecha -->
        <div class="mt-4 flex items-center justify-center space-x-4">
            <span class="px-3 py-1 text-sm font-semibold rounded-full"
                    :class="{
                        'bg-blue-500 text-white': selectedJuego.estado === 'Programado',
                        'bg-yellow-500 text-white': selectedJuego.estado === 'En Curso',
                        'bg-green-500 text-white': selectedJuego.estado === 'Finalizado',
                        'bg-red-500 text-white': selectedJuego.estado === 'Cancelado',
                        'bg-gray-500 text-white': selectedJuego.estado === 'Suspendido'
                    }"
                    x-text="selectedJuego.estado">
            </span>
            <span class="text-gray-600 dark:text-gray-400" x-text="`${new Date(selectedJuego.fecha).toLocaleDateString('es-ES')} - ${selectedJuego.hora}`"></span>
        </div>
    </div>
    
    <!-- Información general -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Información básica - Gris como en el primer modal -->
        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Información del Partido
            </h4>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Liga:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJuego.liga?.nombre || 'No especificada'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Temporada:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJuego.temporada?.nombre || 'No especificada'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Torneo:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJuego.torneo?.nombre || 'No especificado'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Cancha:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJuego.cancha?.nombre || 'No especificada'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Duración por cuarto:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="`${selectedJuego.duracion_cuarto} minutos`"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Duración descanso:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="`${selectedJuego.duracion_descanso} minutos`"></span>
                </div>
            </div>
        </div>

        <!-- Árbitros - Azul como en la información deportiva -->
        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Árbitros
            </h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Árbitro Principal:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJuego.arbitro_principal?.nombre || 'No asignado'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Árbitro Auxiliar:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJuego.arbitro_auxiliar?.nombre || 'No asignado'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Mesa de Control:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJuego.mesa_control?.nombre || 'No asignado'"></span>
                </div>
            </div>
        </div>

        <!-- Observaciones - Rojo como en el contacto de emergencia -->
        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                Observaciones
            </h4>
            <p class="text-gray-800 dark:text-white" x-text="selectedJuego.observaciones || 'No hay observaciones registradas'"></p>
        </div>
    </div>
</div>