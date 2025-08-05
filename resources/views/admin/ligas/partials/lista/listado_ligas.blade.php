<div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-black dark:text-primary-400">Ligas Registradas</h2>
    <!-- Botón para mostrar/ocultar el formulario -->
    <button
        @click="showForm = !showForm; if (!showForm) resetForm()"
        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition"
    >
        <span x-text="showForm ? 'Ocultar Formulario' : 'Registrar Nueva Liga'"></span>
    </button>
</div> 
<!-- Incluir el componente de filtros -->
<x-filtros.ligas 
    :total-ligas="$totalLigas"
    :ligas-filtered="$ligas->count()"
/>

<div class="p-6">
    @if($ligas->isEmpty())
        <div class="text-center py-8 text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium dark:text-gray-300 text-dark-800">No hay ligas registradas</h3>
            <p class="mt-1 text-sm dark:text-gray-300 text-dark-700">Comienza registrando una nueva liga.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-stretch">
            @foreach($ligas as $liga)
            <div class="bg-white dark:bg-dark-700 rounded-lg border border-gray-300 dark:border-dark-600 overflow-hidden hover:border-primary-500 transition-colors flex flex-col h-full">
                <div class="p-4 flex-1">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ $liga->nombre }}
                        </h3>
                        <span class="px-2 py-1 text-xs font-semibold {{ $liga->activo ? 'bg-green-500' : 'bg-red-500' }} text-white rounded-full">
                            {{ $liga->activo ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                    
                    @if($liga->descripcion)
                        <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                            <div class="flex items-start">
                                <svg class="w-4 h-4 mr-1 text-primary-400 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="line-clamp-3">{{ $liga->descripcion }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="mt-3 text-sm text-gray-400">
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Creada: {{ $liga->created_at->format('d/m/Y') }}
                        </p>
                        @if($liga->updated_at != $liga->created_at)
                        <p class="flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Actualizada: {{ $liga->updated_at->format('d/m/Y') }}
                        </p>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-100 dark:bg-dark-900 px-4 py-3 flex justify-end space-x-2 border-t border-gray-300 dark:border-dark-700">
                    <button 
                        @click="editLiga({{ $liga->toJson() }})"
                        class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 transition-colors"
                    >
                        Editar
                    </button>
                    <form action="{{ route('ligas.destroy', $liga->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 transition-colors"
                            onclick="return confirm('¿Estás seguro de que deseas eliminar esta liga?')">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @if($ligas->hasPages())
            <div class="mt-6 flex justify-between items-center">
                <!-- Botón Anterior -->
                @if($ligas->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded cursor-not-allowed">Anterior</span>
                @else
                    <a href="{{ $ligas->previousPageUrl() }}" class="px-4 py-2 bg-blue-500 dark:bg-blue-700 text-white rounded hover:bg-blue-600 dark:hover:bg-blue-800">Anterior</a>
                @endif

                <!-- Información de página -->
                <span class="text-gray-700 dark:text-gray-300">
                    Página {{ $ligas->currentPage() }} de {{ $ligas->lastPage() }}
                </span>

                <!-- Botón Siguiente -->
                @if($ligas->hasMorePages())
                    <a href="{{ $ligas->nextPageUrl() }}" class="px-4 py-2 bg-blue-500 dark:bg-blue-700 text-white rounded hover:bg-blue-600 dark:hover:bg-blue-800">Siguiente</a>
                @else
                    <span class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded cursor-not-allowed">Siguiente</span>
                @endif
            </div>
        @endif
    @endif
</div>