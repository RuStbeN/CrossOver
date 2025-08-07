<!-- Encabezado con estilo mejorado -->
<div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700">
    <h3 class="text-2xl md:text-3xl font-extrabold text-center text-gray-800 dark:text-white tracking-tight">
        👥 Plantilla de Jugadores
    </h3>
</div>

<div class="bg-white dark:bg-dark-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
            <thead class="bg-gray-50 dark:bg-dark-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">N° Camiseta</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jugador</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Posición</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Capitán</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha Ingreso</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-700">
                <template x-for="jugador in selectedEquipo.jugadores" :key="jugador.id">
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors duration-150">
                        <!-- Número de camiseta con estilo destacado -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-block w-8 h-8 rounded-md bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 font-bold text-center leading-8"
                                  x-text="jugador.pivot.numero_camiseta || '-'">
                            </span>
                        </td>
                        
                        <!-- Nombre del jugador y edad -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white" x-text="jugador.nombre"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" 
                                 x-text="jugador.edad ? jugador.edad + ' años' : 'Edad no especificada'"></div>
                        </td>
                        
                        <!-- Posición principal y secundaria -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white" x-text="jugador.pivot.posicion_principal || 'No especificada'"></div>
                            <template x-if="jugador.pivot.posicion_secundaria">
                                <div class="text-xs text-gray-500 dark:text-gray-400" 
                                     x-text="'(' + jugador.pivot.posicion_secundaria + ')'"></div>
                            </template>
                        </td>
                        
                        <!-- Estado del jugador -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                  :class="jugador.pivot.activo ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'"
                                  x-text="jugador.pivot.activo ? 'Activo' : 'Inactivo'">
                            </span>
                        </td>
                        
                        <!-- Indicador de capitán -->
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <template x-if="jugador.pivot.es_capitan">
                                <span class="px-2 py-1 inline-flex items-center justify-center text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Capitán
                                </span>
                            </template>
                            <template x-if="!jugador.pivot.es_capitan">
                                <span class="text-xs text-gray-500 dark:text-gray-400">-</span>
                            </template>
                        </td>
                        
                        <!-- Fecha de ingreso -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <template x-if="jugador.pivot.fecha_ingreso">
                                <span x-text="new Date(jugador.pivot.fecha_ingreso).toLocaleDateString('es-ES')"></span>
                            </template>
                            <template x-if="!jugador.pivot.fecha_ingreso">
                                <span class="text-gray-400 dark:text-gray-500">No especificada</span>
                            </template>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

<!-- Mensaje cuando no hay jugadores -->
<template x-if="!selectedEquipo.jugadores || selectedEquipo.jugadores.length === 0">
    <div class="text-center py-12 bg-white dark:bg-dark-800 rounded-lg shadow">
        <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Plantilla vacía</h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Este equipo no tiene jugadores registrados actualmente.</p>
    </div>
</template>