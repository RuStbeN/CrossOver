<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Columna 1: Foto y datos básicos -->
    <div class="lg:col-span-1">
        <div class="text-center mb-6">
            <div class="mx-auto mb-4">
                <template x-if="selectedJugador.foto_url">
                    <img :src="`/storage/${selectedJugador.foto_url.replace('public/', '')}`" 
                            :alt="`Foto de ${selectedJugador.nombre}`" 
                            class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-primary-500">
                </template>
                <template x-if="!selectedJugador.foto_url">
                    <div class="w-32 h-32 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold text-4xl mx-auto border-4 border-primary-600"
                            x-text="selectedJugador.nombre.charAt(0).toUpperCase()">
                    </div>
                </template>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white" x-text="selectedJugador.nombre"></h3>
            <p class="text-gray-600 dark:text-gray-400" x-text="`${selectedJugador.edad} años`"></p>
            <span class="inline-block px-3 py-1 text-sm font-semibold text-white rounded-full mt-2"
                    :class="selectedJugador.activo ? 'bg-green-500' : 'bg-red-500'"
                    x-text="selectedJugador.activo ? 'Activo' : 'Inactivo'">
            </span>
        </div>
    </div>

    <!-- Columna 2: Información personal -->
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Información Personal
            </h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">RFC:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJugador.rfc || 'No especificado'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Sexo:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJugador.sexo"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Fecha de Nacimiento:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJugador.fecha_nacimiento ? new Date(selectedJugador.fecha_nacimiento).toLocaleDateString('es-ES') : 'No especificada'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Teléfono:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJugador.telefono || 'No especificado'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Email:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJugador.email || 'No especificado'"></span>
                </div>
                <div class="pt-2">
                    <span class="text-gray-600 dark:text-gray-400">Dirección:</span>
                    <p class="text-gray-800 dark:text-white font-medium mt-1" x-text="selectedJugador.direccion || 'No especificada'"></p>
                </div>
            </div>
        </div>

        <!-- Contacto de Emergencia -->
        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-800 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                Contacto de Emergencia
            </h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Nombre:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJugador.contacto_emergencia_nombre || 'No especificado'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Relación:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJugador.contacto_emergencia_relacion || 'No especificado'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Teléfono:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJugador.contacto_emergencia_telefono || 'No especificado'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Columna 3: Información deportiva -->
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-800 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Información Deportiva
            </h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Posición:</span>
                    <span class="text-gray-800 dark:text-white font-medium" 
                        x-text="selectedJugador.equipos_actual?.posicion_principal || 'No asignada'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Número:</span>
                    <span class="text-gray-800 dark:text-white font-medium" 
                        x-text="selectedJugador.equipos_actual?.numero_camiseta || 'No asignado'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Equipo:</span>
                    <span class="text-gray-800 dark:text-white font-medium" 
                        x-text="selectedJugador.equipos_actual?.equipo?.nombre || 'Sin equipo'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Liga:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJugador.liga ? selectedJugador.liga.nombre : 'Sin liga'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Categoría:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJugador.categoria ? selectedJugador.categoria.nombre : 'Sin categoría'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Estado Físico:</span>
                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                            :class="{
                                'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400': selectedJugador.estado_fisico === 'Óptimo',
                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400': selectedJugador.estado_fisico === 'Regular',
                                'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400': selectedJugador.estado_fisico === 'Lesionado'
                            }"
                            x-text="selectedJugador.estado_fisico">
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Fecha de Ingreso:</span>
                    <span class="text-gray-800 dark:text-white font-medium" x-text="selectedJugador.fecha_ingreso ? new Date(selectedJugador.fecha_ingreso).toLocaleDateString('es-ES') : 'No especificada'"></span>
                </div>
            </div>
        </div>
    </div>  
</div>