<div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-black dark:text-primary-400">Árbitros Registrados</h2>
    <!-- Botón para mostrar/ocultar el formulario -->
    <button
        @click="showForm = !showForm; if (!showForm) resetForm()"
        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition"
    >
        <span x-text="showForm ? 'Ocultar Formulario' : 'Registrar Nuevo Árbitro'"></span>
    </button>
</div>
<!-- Incluir el componente de filtros -->
            <x-filtros.arbitros 
                :total-arbitros="$totalArbitros"
                :arbitros-filtered="$arbitros->count()"
            />

<div class="p-6">
    @if($arbitros->isEmpty())
        <div class="text-center py-8 text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium dark:text-gray-300 text-dark-800">No hay árbitros registrados</h3>
            <p class="mt-1 text-sm dark:text-gray-300 text-dark-700">Comienza registrando un nuevo árbitro.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-stretch">
            @foreach($arbitros as $arbitro)
            <div class="bg-white dark:bg-dark-700 rounded-lg border border-gray-300 dark:border-dark-600 overflow-hidden hover:border-primary-500 transition-colors flex flex-col h-full">
                <div class="p-4 flex-1">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            @if($arbitro->foto)
                                <img 
                                    src="{{ Storage::url($arbitro->foto) }}" 
                                    onerror="this.onerror=null; this.src='https://via.placeholder.com/48?text=IMG';"
                                    alt="Foto de {{ $arbitro->nombre }}" 
                                    class="w-12 h-12 rounded-full object-cover mr-3"
                                >
                            @else
                                <div class="w-12 h-12 rounded-full bg-primary-500 text-white flex items-center justify-center mr-3 text-sm font-semibold">
                                    {{ strtoupper(substr($arbitro->nombre, 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                                    {{ $arbitro->nombre }}
                                </h3>
                                @if($arbitro->edad)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $arbitro->edad }} años</p>
                                @endif
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold {{ $arbitro->activo ? 'bg-green-500' : 'bg-red-500' }} text-white rounded-full">
                            {{ $arbitro->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    
                    <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        @if($arbitro->telefono)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span>{{ $arbitro->telefono }}</span>
                            </div>
                        @endif
                        
                        @if($arbitro->correo)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="truncate">{{ $arbitro->correo }}</span>
                            </div>
                        @endif
                        
                        @if($arbitro->direccion)
                            <div class="flex items-start">
                                <svg class="w-4 h-4 mr-2 text-primary-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="line-clamp-2">{{ $arbitro->direccion }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-3 text-sm text-gray-400">
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Registrado: {{ $arbitro->created_at->format('d/m/Y') }}
                        </p>
                        @if($arbitro->updated_at != $arbitro->created_at)
                        <p class="flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Actualizado: {{ $arbitro->updated_at->format('d/m/Y') }}
                        </p>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-100 dark:bg-dark-900 px-4 py-3 flex justify-end space-x-2 border-t border-gray-300 dark:border-dark-700">
                    <button 
                        @click="editArbitro({{ $arbitro->toJson() }})"
                        class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 transition-colors"
                    >
                        Editar
                    </button>
                    <form action="{{ route('arbitros.destroy', $arbitro->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 transition-colors"
                            onclick="return confirm('¿Estás seguro de que deseas eliminar este árbitro?')">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @if($arbitros->hasPages())
            <div class="mt-6 flex justify-between items-center">
                <!-- Botón Anterior -->
                @if($arbitros->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Anterior</span>
                @else
                    <a href="{{ $arbitros->previousPageUrl() }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Anterior</a>
                @endif

                <!-- Información de página -->
                <span class="text-gray-700">
                    Página {{ $arbitros->currentPage() }} de {{ $arbitros->lastPage() }}
                </span>

                <!-- Botón Siguiente -->
                @if($arbitros->hasMorePages())
                    <a href="{{ $arbitros->nextPageUrl() }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Siguiente</a>
                @else
                    <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Siguiente</span>
                @endif
            </div>
        @endif
    @endif
</div>