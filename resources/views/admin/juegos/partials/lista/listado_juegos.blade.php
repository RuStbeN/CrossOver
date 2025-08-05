<div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
    <h2 class="text-xl font-bold text-black dark:text-primary-400">Partidos Programados</h2>
    <button
        @click="showForm = !showForm; if (!showForm) resetForm()"
        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition text-sm"
    >
        <span x-text="showForm ? 'Ocultar Formulario' : '+ Nuevo Partido'"></span>
    </button>
</div>

<div class="p-4">
    @if($juegos->isEmpty())
        <div class="text-center py-8 text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium dark:text-gray-300 text-dark-800">No hay partidos programados</h3>
            <p class="mt-1 text-sm dark:text-gray-300 text-dark-700">Comienza registrando un nuevo partido.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($juegos as $juego)
            <div class="bg-white dark:bg-dark-700 rounded-lg border border-gray-300 dark:border-dark-600 overflow-hidden hover:border-primary-500 transition-colors flex flex-col h-full">
                <div class="p-4 flex-1 cursor-pointer" @click="openModal({{ $juego->toJson() }})">
                    <!-- Cabecera -->
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($juego->fecha)->format('d M') }} - {{ \Carbon\Carbon::parse($juego->hora)->format('H:i') }}
                        </span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            @if($juego->estado === 'Programado') bg-blue-500 text-white
                            @elseif($juego->estado === 'En Curso') bg-yellow-500 text-white
                            @elseif($juego->estado === 'Finalizado') bg-green-500 text-white
                            @elseif($juego->estado === 'Cancelado') bg-red-500 text-white
                            @elseif($juego->estado === 'Suspendido') bg-gray-500 text-white @endif">
                            {{ $juego->estado }}
                        </span>
                    </div>
                    
                    <!-- Cuerpo -->
                    <div class="mt-2">
                        <!-- Equipo local -->
                        <div class="flex items-center justify-between py-1">
                            <span class="font-medium text-gray-800 dark:text-white">
                                {{ $juego->equipoLocal->nombre ?? 'Sin definir' }}
                            </span>
                        </div>
                        
                        <!-- Equipo visitante -->
                        <div class="flex items-center justify-between py-1">
                            <span class="font-medium text-gray-800 dark:text-white">
                                {{ $juego->equipoVisitante->nombre ?? 'Sin definir' }}
                            </span>
                        </div>
                        
                        <!-- Detalles -->
                        <div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                            <p class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ $juego->liga->nombre ?? 'Sin liga' }}
                            </p>
                            <p class="flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $juego->cancha->nombre ?? 'Sin cancha' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Pie de card con acciones -->
                <div class="bg-gray-100 dark:bg-dark-900 px-4 py-3 flex justify-end space-x-2 border-t border-gray-300 dark:border-dark-700">
                    <button 
                        @click.stop="editJuego({{ $juego->toJson() }})"
                        class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 transition-colors"
                    >
                        Editar
                    </button>
                    <form action="{{ route('juegos.destroy', $juego->id) }}" method="POST" class="inline" @click.stop>
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('¿Estás seguro de eliminar este partido?')"
                                class="text-sm text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 transition-colors">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        @if($juegos->hasPages())
            <div class="mt-6 flex justify-between items-center">
                <!-- Botón Anterior -->
                @if($juegos->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded cursor-not-allowed">Anterior</span>
                @else
                    <a href="{{ $juegos->previousPageUrl() }}" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 transition">Anterior</a>
                @endif

                <!-- Información de página -->
                <span class="text-gray-700 dark:text-gray-300">
                    Página {{ $juegos->currentPage() }} de {{ $juegos->lastPage() }}
                </span>

                <!-- Botón Siguiente -->
                @if($juegos->hasMorePages())
                    <a href="{{ $juegos->nextPageUrl() }}" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 transition">Siguiente</a>
                @else
                    <span class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded cursor-not-allowed">Siguiente</span>
                @endif
            </div>
        @endif
    @endif
</div>