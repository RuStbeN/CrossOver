<!-- Encabezado de la sección -->
<div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
    <h2 class="text-xl font-bold text-black dark:text-primary-400">Torneos Registrados</h2>
    <button @click="showForm = !showForm; if (!showForm) resetForm()"
            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition text-sm">
        <span x-text="showForm ? 'Ocultar Formulario' : 'Nuevo Torneo'"></span>
    </button>
</div>

<div class="p-4">
    @if($torneos->isEmpty())
        <!-- Mensaje cuando no hay torneos -->
        <div class="text-center py-8 text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium dark:text-gray-300 text-dark-800">No hay torneos registrados</h3>
            <p class="mt-1 text-sm dark:text-gray-300 text-dark-700">Comienza registrando un nuevo torneo.</p>
        </div>
    @else
        <!-- Grid de Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($torneos as $torneo)
            <div class="bg-white dark:bg-dark-700 rounded-lg border border-gray-300 dark:border-dark-600 overflow-hidden hover:border-primary-500 transition-colors flex flex-col h-full">
                <!-- Contenido de la Card -->
                <div class="p-4 flex-1 cursor-pointer" @click="openModal({{ $torneo->toJson() }})">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('d M Y') }} - 
                            {{ $torneo->fecha_fin ? \Carbon\Carbon::parse($torneo->fecha_fin)->format('d M Y') : 'Actual' }}
                        </span>
                        <!-- Estado idéntico al de partidos -->
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            @if($torneo->estado === 'Programado') bg-blue-500 text-white
                            @elseif($torneo->estado === 'En Curso') bg-yellow-500 text-white
                            @elseif($torneo->estado === 'Finalizado') bg-green-500 text-white
                            @elseif($torneo->estado === 'Cancelado') bg-red-500 text-white
                            @elseif($torneo->estado === 'Suspendido') bg-gray-500 text-white @endif">
                            {{ $torneo->estado }}
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-3">
                        {{ $torneo->nombre }}
                    </h3>
                    
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-2">
                        <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        {{ $torneo->tipo_formateado }}
                    </div>
                    
                    <div class="mt-3 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ $torneo->liga->nombre ?? 'Sin liga' }}
                        </p>
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            {{ $torneo->categoria->nombre ?? 'Sin categoría' }}
                        </p>
                    </div>
                </div>
                
                <!-- Acciones -->
                <div class="bg-gray-100 dark:bg-dark-900 px-4 py-3 flex justify-end space-x-2 border-t border-gray-300 dark:border-dark-700">
                    <button @click.stop="editTorneo({{ $torneo->toJson() }})"
                            class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 transition-colors">
                        Editar
                    </button>
                    <form action="{{ route('torneos.destroy', $torneo->id) }}" method="POST" class="inline" @click.stop>
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('¿Estás seguro de eliminar este torneo?')"
                                class="text-sm text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 transition-colors">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Paginación -->
        @if($torneos->hasPages())
            <div class="mt-6 flex justify-between items-center">
                @if($torneos->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded cursor-not-allowed">Anterior</span>
                @else
                    <a href="{{ $torneos->previousPageUrl() }}" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 transition">Anterior</a>
                @endif

                <span class="text-gray-700 dark:text-gray-300">
                    Página {{ $torneos->currentPage() }} de {{ $torneos->lastPage() }}
                </span>

                @if($torneos->hasMorePages())
                    <a href="{{ $torneos->nextPageUrl() }}" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 transition">Siguiente</a>
                @else
                    <span class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded cursor-not-allowed">Siguiente</span>
                @endif
            </div>
        @endif
    @endif
</div>