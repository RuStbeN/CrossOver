<!-- Tabla de clasificación -->
<div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700">
    <h3 class="text-2xl md:text-3xl font-extrabold text-center text-gray-800 dark:text-white tracking-tight">
        🏆 Tabla de Clasificación
    </h3>
</div>

<div class="bg-white dark:bg-dark-800 rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
            <thead class="bg-gray-50 dark:bg-dark-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pos.</th>
                    <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">Podio</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Equipo</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">PJ</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">PG</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">PE</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">PP</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">PF</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">PC</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">DP</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pts</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-700">
                <template x-for="(equipo, index) in selectedTorneo.clasificacion" :key="equipo.id">
                    <tr>
                        <!-- Posición - siempre visible -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="index + 1"></td>
                                            
                        <!-- Coronas -->
                        <td class="px-3 py-4 whitespace-nowrap text-center">
                            <!-- Oro - 1er lugar -->
                            <template x-if="index === 0">
                                <svg class="w-7 h-7 mx-auto text-yellow-500 drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M5 16L3 5.5l4.5 4.5L12 4l4.5 6L21 5.5L19 16H5zm2.38-2h9.24l.76-4.2-2.32 2.32L12 9.18l-2.06 2.94-2.32-2.32L9.38 14z"/>
                                    <circle cx="12" cy="17.5" r="1.5"/>
                                </svg>
                            </template>
                            
                            <!-- Plata - 2do lugar -->
                            <template x-if="index === 1">
                                <svg class="w-7 h-7 mx-auto text-gray-300 drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M5 16L3 5.5l4.5 4.5L12 4l4.5 6L21 5.5L19 16H5zm2.38-2h9.24l.76-4.2-2.32 2.32L12 9.18l-2.06 2.94-2.32-2.32L9.38 14z"/>
                                    <circle cx="12" cy="17.5" r="1.5"/>
                                </svg>
                            </template>
                            
                            <!-- Bronce - 3er lugar -->
                            <template x-if="index === 2">
                                <svg class="w-7 h-7 mx-auto text-amber-600 drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M5 16L3 5.5l4.5 4.5L12 4l4.5 6L21 5.5L19 16H5zm2.38-2h9.24l.76-4.2-2.32 2.32L12 9.18l-2.06 2.94-2.32-2.32L9.38 14z"/>
                                    <circle cx="12" cy="17.5" r="1.5"/>
                                </svg>
                            </template>
                            
                            <!-- Corona verde solo si hay playoffs y está dentro de los equipos calificados -->
                            <template x-if="selectedTorneo.usa_playoffs && index >= 3 && index < selectedTorneo.equipos_playoffs">
                                <svg class="w-6 h-6 mx-auto text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M5 16L3 5.5l4.5 4.5L12 4l4.5 6L21 5.5L19 16H5zm2.38-2h9.24l.76-4.2-2.32 2.32L12 9.18l-2.06 2.94-2.32-2.32L9.38 14z"/>
                                </svg>
                            </template>
                        </td>
                        
                        <!-- Nombre del equipo -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="equipo.equipo.nombre"></div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Estadísticas -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400" x-text="equipo.partidos_jugados"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400" x-text="equipo.partidos_ganados"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400" x-text="equipo.partidos_empatados"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400" x-text="equipo.partidos_perdidos"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400" x-text="equipo.puntos_favor"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400" x-text="equipo.puntos_contra"></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium" 
                            :class="{
                                'text-green-600 dark:text-green-400': equipo.diferencia_puntos > 0,
                                'text-red-600 dark:text-red-400': equipo.diferencia_puntos < 0,
                                'text-gray-500 dark:text-gray-400': equipo.diferencia_puntos === 0
                            }"
                            x-text="equipo.diferencia_puntos > 0 ? `+${equipo.diferencia_puntos}` : equipo.diferencia_puntos">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-gray-900 dark:text-white" x-text="equipo.puntos_totales"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>


<!-- Card explicativa de las estadísticas -->
<div class="mt-6 bg-white dark:bg-dark-800 rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Explicación de Estadísticas
        </h3>
    </div>
    
    <div class="px-6 py-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Columna 1: Abreviaciones -->
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Abreviaciones</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600 dark:text-gray-400">PJ:</span>
                        <span class="text-gray-800 dark:text-white">Partidos Jugados</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600 dark:text-gray-400">PG:</span>
                        <span class="text-gray-800 dark:text-white">Partidos Ganados</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600 dark:text-gray-400">PE:</span>
                        <span class="text-gray-800 dark:text-white">Partidos Empatados</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600 dark:text-gray-400">PP:</span>
                        <span class="text-gray-800 dark:text-white">Partidos Perdidos</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600 dark:text-gray-400">PF:</span>
                        <span class="text-gray-800 dark:text-white">Puntos a Favor</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600 dark:text-gray-400">PC:</span>
                        <span class="text-gray-800 dark:text-white">Puntos en Contra</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600 dark:text-gray-400">DP:</span>
                        <span class="text-gray-800 dark:text-white">Diferencia de Puntos</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Pts:</span>
                        <span class="text-gray-800 dark:text-white">Puntos Totales</span>
                    </div>
                </div>
            </div>
            
            <!-- Columna 2: Sistema de Puntuación -->
            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Sistema de Puntuación</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Victoria:</span>
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-md font-semibold">
                            <span x-text="selectedTorneo ? selectedTorneo.puntos_por_victoria : 3"></span> puntos
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Empate:</span>
                        <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-md font-semibold">
                            <span x-text="selectedTorneo ? selectedTorneo.puntos_por_empate : 1"></span> punto<span x-text="selectedTorneo && selectedTorneo.puntos_por_empate > 1 ? 's' : ''"></span>
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-600 dark:text-gray-400">Derrota:</span>
                        <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-md font-semibold">
                            <span x-text="selectedTorneo ? selectedTorneo.puntos_por_derrota : 0"></span> puntos
                        </span>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-dark-700">
                    <h5 class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Criterios de Desempate</h5>
                    <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                        <div>1. Mayor diferencia de puntos (DP)</div>
                        <div>2. Mayor cantidad de puntos a favor (PF)</div>
                        <div>3. Menor cantidad de puntos en contra (PC)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>