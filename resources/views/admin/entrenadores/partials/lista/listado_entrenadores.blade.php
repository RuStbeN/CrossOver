<div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-black dark:text-primary-400">Entrenadores Registrados</h2>
    <!-- Botón para mostrar/ocultar el formulario -->
    <button
        @click="showForm = !showForm; if (!showForm) resetForm()"
        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition"
    >
        <span x-text="showForm ? 'Ocultar Formulario' : 'Registrar Nuevo Entrenador'"></span>
    </button>
</div>

<x-filtros.entrenadores 
    :total-entrenadores="$totalEntrenadores"
    :entrenadores-filtered="$entrenadores->count()"
/>

<div class="p-6">
    @if($entrenadores->isEmpty())
        <div class="text-center py-8 text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium dark:text-gray-300 text-dark-800">No hay entrenadores registrados</h3>
            <p class="mt-1 text-sm dark:text-gray-300 text-dark-700">Comienza registrando un nuevo entrenador.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-stretch">
            @foreach($entrenadores as $entrenador)
            <div class="bg-white dark:bg-dark-700 rounded-lg border border-gray-300 dark:border-dark-600 overflow-hidden hover:border-primary-500 transition-colors flex flex-col h-full">
                <!-- Área clickeable para abrir el modal -->
                <div class="p-4 flex-1 cursor-pointer" @click.stop="openEntrenadorModal({{ $entrenador->toJson() }})">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                                {{ $entrenador->nombre }}
                            </h3>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold {{ $entrenador->activo ? 'bg-green-500' : 'bg-red-500' }} text-white rounded-full">
                            {{ $entrenador->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        @if($entrenador->telefono)
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            {{ $entrenador->telefono }}
                        </p>
                        @endif
                        @if($entrenador->email)
                        <p class="flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            {{ $entrenador->email }}
                        </p>
                        @endif
                        @if($entrenador->cedula_profesional)
                        <p class="flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Cédula: {{ $entrenador->cedula_profesional }}
                        </p>
                        @endif
                        @if($entrenador->edad)
                        <p class="flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Edad: {{ $entrenador->edad }} años
                        </p>
                        @endif
                        @if($entrenador->experiencia)
                        <div class="mt-1">
                            <div class="flex items-start">
                                <svg class="w-4 h-4 mr-1 text-primary-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Experiencia:</span>
                            </div>
                            <p class="text-sm line-clamp-3 pl-5 text-gray-600 dark:text-gray-300">
                                {{ $entrenador->experiencia }}
                            </p>
                        </div>
                        @endif
                        <p class="flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Equipos: {{ $entrenador->equipos_count }}
                        </p>
                    </div>
                    <div class="mt-3 text-sm text-gray-400">
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Registrado: {{ $entrenador->created_at->format('d/m/Y') }}
                        </p>
                        @if($entrenador->updated_at != $entrenador->created_at)
                        <p class="flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Actualizado: {{ $entrenador->updated_at->format('d/m/Y') }}
                        </p>
                        @endif
                    </div>
                </div>
                
                <!-- Área de botones (no clickeable para el modal) -->
                <div class="bg-gray-100 dark:bg-dark-900 px-4 py-3 flex justify-end space-x-2 border-t border-gray-300 dark:border-dark-700">
                    <button 
                        @click.stop="editEntrenador({{ $entrenador->toJson() }})"
                        class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 transition-colors"
                    >
                        Editar
                    </button>
                    <form action="{{ route('entrenadores.destroy', $entrenador->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 transition-colors"
                            onclick="return confirm('¿Estás seguro de que deseas eliminar este entrenador?')">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @if($entrenadores->hasPages())
            <div class="mt-6 flex justify-between items-center">
                <!-- Botón Anterior -->
                @if($licategoriasas->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Anterior</span>
                @else
                    <a href="{{ $entrenadores->previousPageUrl() }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Anterior</a>
                @endif

                <!-- Información de página -->
                <span class="text-gray-700">
                    Página {{ $entrenadores->currentPage() }} de {{ $entrenadores->lastPage() }}
                </span>

                <!-- Botón Siguiente -->
                @if($entrenadores->hasMorePages())
                    <a href="{{ $entrenadores->nextPageUrl() }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Siguiente</a>
                @else
                    <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Siguiente</span>
                @endif
            </div>
        @endif
    @endif
</div>