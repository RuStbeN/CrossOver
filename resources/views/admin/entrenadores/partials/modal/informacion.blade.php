<div class="space-y-6">
    <!-- Encabezado con foto y nombre - Fondo blanco -->
    <div class="bg-white dark:bg-dark-700 rounded-lg p-6 flex items-center space-x-6">
        <!-- Foto del entrenador -->
        <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200 dark:bg-dark-600 border-2 border-gray-300 dark:border-dark-500">
            <template x-if="selectedEntrenador.foto_url">
                <img :src="`/storage/${selectedEntrenador.foto_url}`" 
                    alt="Foto del entrenador" 
                    class="w-full h-full object-cover">
            </template>
            <template x-if="!selectedEntrenador.foto_url">
                <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-dark-700 text-gray-500 dark:text-gray-400">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </template>
        </div>
        
        <!-- Nombre y estado -->
        <div>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white" x-text="selectedEntrenador.nombre"></h3>
            <div class="mt-2 flex items-center">
                <span class="px-3 py-1 text-sm font-semibold rounded-full" 
                    :class="selectedEntrenador.activo ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
                    x-text="selectedEntrenador.activo ? 'Activo' : 'Inactivo'">
                </span>
                <span class="ml-3 text-gray-600 dark:text-gray-400" x-text="selectedEntrenador.edad ? `${selectedEntrenador.edad} años` : 'Edad no especificada'"></span>
            </div>
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400" x-text="`Registrado el ${new Date(selectedEntrenador.created_at).toLocaleDateString('es-ES')}`"></div>
        </div>
    </div>
    
    <!-- Información general -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Información de contacto - Azul -->
        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                Información de Contacto
            </h4>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Teléfono:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedEntrenador.telefono || 'No especificado'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Email:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedEntrenador.email || 'No especificado'"></span>
                </div>
            </div>
        </div>

        <!-- Información profesional - Blanco -->
        <div class="bg-white dark:bg-dark-700 rounded-lg p-4 border border-gray-200 dark:border-dark-600">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Información Profesional
            </h4>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Cédula Profesional:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedEntrenador.cedula_profesional || 'No especificada'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Equipos a cargo:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedEntrenador.equipos_count || '0'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Experiencia - Rojo -->
    <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            Experiencia
        </h4>
        <p class="text-gray-800 dark:text-white" x-text="selectedEntrenador.experiencia || 'No hay información de experiencia registrada'"></p>
    </div>

    <!-- Equipos (si tiene) - Azul -->
    <template x-if="selectedEntrenador.equipos && selectedEntrenador.equipos.length > 0">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-100 dark:border-blue-800/50">
            <h4 class="text-lg font-semibold text-blue-800 dark:text-blue-400 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Equipos a Cargo
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <template x-for="equipo in selectedEntrenador.equipos" :key="equipo.id">
                    <div class="flex items-center space-x-3 bg-white dark:bg-dark-800 p-3 rounded-lg border border-gray-200 dark:border-dark-700">
                        <template x-if="equipo.logo_url">
                            <img :src="`/storage/${equipo.logo_url}`" 
                                :alt="equipo.nombre" 
                                class="w-10 h-10 rounded-full object-cover">
                        </template>
                        <template x-if="!equipo.logo_url">
                            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-dark-700 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3a9 9 0 110 18 9 9 0 010-18zm0 1a8 8 0 100 16 8 8 0 000-16z"/>
                                </svg>
                            </div>
                        </template>
                        <span class="font-medium text-gray-800 dark:text-white" x-text="equipo.nombre"></span>
                    </div>
                </template>
            </div>
        </div>
    </template>
</div>