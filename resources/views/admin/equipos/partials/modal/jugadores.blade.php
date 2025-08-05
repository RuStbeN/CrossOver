<h3 class="text-xl font-bold text-gray-800 dark:text-white">Jugadores del Equipo</h3>

<template x-if="selectedEquipo.jugadores && selectedEquipo.jugadores.length > 0">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
            <thead class="bg-gray-50 dark:bg-dark-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Camiseta</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Posición</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-700">
                <template x-for="jugador in selectedEquipo.jugadores" :key="jugador.id">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="jugador.pivot.numero_camiseta || '-'"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="jugador.nombre"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="jugador.pivot.posicion_principal || 'No especificada'"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                                :class="jugador.pivot.activo ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
                                x-text="jugador.pivot.activo ? 'Activo' : 'Inactivo'">
                            </span>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>

<template x-if="!selectedEquipo.jugadores || selectedEquipo.jugadores.length === 0">
    <div class="text-center py-8 text-gray-400">
        <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium dark:text-gray-300 text-dark-800">No hay jugadores registrados</h3>
        <p class="mt-1 text-sm dark:text-gray-300 text-dark-700">Este equipo no tiene jugadores asignados.</p>
    </div>
</template>