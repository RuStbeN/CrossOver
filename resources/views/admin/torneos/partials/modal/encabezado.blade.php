<!-- Encabezado con información básica -->
    <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white" x-text="selectedTorneo.nombre"></h3>
                <p class="text-gray-600 dark:text-gray-400" x-text="selectedTorneo.descripcion || 'Sin descripción'"></p>
            </div>
            @foreach($torneos as $torneo)
                <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($torneo->estado === 'Programado') bg-blue-500 text-white
                            @elseif($torneo->estado === 'En Curso') bg-yellow-500 text-white
                            @elseif($torneo->estado === 'Finalizado') bg-green-500 text-white
                            @elseif($torneo->estado === 'Cancelado') bg-red-500 text-white
                            @elseif($torneo->estado === 'Suspendido') bg-gray-500 text-white @endif">
                    {{ $torneo->estado }}
                </span>
            @endforeach
        </div>
        
        <!-- Fechas -->
        <div class="mt-4 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Fecha de inicio</p>
                <p class="font-medium text-gray-800 dark:text-white" x-text="new Date(selectedTorneo.fecha_inicio).toLocaleDateString('es-ES')"></p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Fecha de fin</p>
                <p class="font-medium text-gray-800 dark:text-white" x-text="selectedTorneo.fecha_fin ? new Date(selectedTorneo.fecha_fin).toLocaleDateString('es-ES') : 'Sin fecha final'"></p>
            </div>
        </div>
    </div>