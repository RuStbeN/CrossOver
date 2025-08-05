<div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-black dark:text-primary-400">Categorías Registradas</h2>
    <!-- Botón para mostrar/ocultar el formulario -->
    <button
        @click="showForm = !showForm; if (!showForm) resetForm()"
        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition"
    >
        <span x-text="showForm ? 'Ocultar Formulario' : 'Registrar Nueva Categoría'"></span>
    </button>
</div>
    <x-filtros.categorias 
        :ligas="$ligas"
        :total-categorias="$totalCategorias"
        :categorias-filtered="$categorias->count()"
    />
    
<div class="p-6">
    @if($categorias->isEmpty())
        <div class="text-center py-8 text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium dark:text-gray-300 text-dark-800">No hay categorías registradas</h3>
            <p class="mt-1 text-sm dark:text-gray-300 text-dark-700">Comienza registrando una nueva categoría.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-stretch">
            @foreach($categorias as $categoria)
            <div class="bg-white dark:bg-dark-700 rounded-lg border border-gray-300 dark:border-dark-600 overflow-hidden hover:border-primary-500 transition-colors flex flex-col h-full">
                <div class="p-4 flex-1">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                                {{ $categoria->nombre }}
                            </h3>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold {{ $categoria->activo ? 'bg-green-500' : 'bg-red-500' }} text-white rounded-full">
                            {{ $categoria->activo ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                    <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Liga: {{ $categoria->liga->nombre }}
                        </p>
                        <p class="flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Edades: {{ $categoria->edad_minima }} - {{ $categoria->edad_maxima }} años
                        </p>
                    </div>
                    <div class="mt-3 text-sm text-gray-400">
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Creada: {{ $categoria->created_at->format('d/m/Y') }}
                        </p>
                        @if($categoria->updated_at != $categoria->created_at)
                        <p class="flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Actualizada: {{ $categoria->updated_at->format('d/m/Y') }}
                        </p>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-100 dark:bg-dark-900 px-4 py-3 flex justify-end space-x-2 border-t border-gray-300 dark:border-dark-700">
                    <button 
                        @click="editCategoria({{ $categoria->toJson() }})"
                        class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 transition-colors"
                    >
                        Editar
                    </button>
                    <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 transition-colors"
                            onclick="return confirm('¿Estás seguro de que deseas eliminar esta categoría?')">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @if($categorias->hasPages())
            <div class="mt-6 flex justify-between items-center">
                <!-- Botón Anterior -->
                @if($categorias->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Anterior</span>
                @else
                    <a href="{{ $categorias->previousPageUrl() }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Anterior</a>
                @endif

                <!-- Información de página -->
                <span class="text-gray-700">
                    Página {{ $categorias->currentPage() }} de {{ $categorias->lastPage() }}
                </span>

                <!-- Botón Siguiente -->
                @if($categorias->hasMorePages())
                    <a href="{{ $categorias->nextPageUrl() }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Siguiente</a>
                @else
                    <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Siguiente</span>
                @endif
            </div>
        @endif
    @endif
</div>