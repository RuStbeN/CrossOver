<div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-black dark:text-primary-400">Canchas Registradas</h2>
    <!-- Botón para mostrar/ocultar el formulario -->
    <button
        @click="showForm = !showForm; if (!showForm) resetForm()"
        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition"
    >
        <span x-text="showForm ? 'Ocultar Formulario' : 'Registrar Nueva Cancha'"></span>
    </button>
</div>

<x-filtros.canchas 
    :total-canchas="$totalCanchas"
    :canchas-filtered="$canchas->count()"
    :tipos-superficie="$tiposSuperficie"
/>

<div class="p-6">
    @if($canchas->isEmpty())
        <div class="text-center py-8 text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium dark:text-gray-300 text-dark-800">No hay canchas registradas</h3>
            <p class="mt-1 text-sm dark:text-gray-300 text-dark-700">Comienza registrando una nueva cancha.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-stretch">
            @foreach($canchas as $cancha)
            <div class="bg-white dark:bg-dark-700 rounded-lg border border-gray-300 dark:border-dark-600 overflow-hidden hover:border-primary-500 transition-colors flex flex-col h-full">
                <div class="p-4 flex-1">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ $cancha->nombre }}
                        </h3>
                        <span class="px-2 py-1 text-xs font-semibold {{ $cancha->activo ? 'bg-green-500' : 'bg-red-500' }} text-white rounded-full">
                            {{ $cancha->activo ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                    
                    <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $cancha->direccion }}
                        </p>
                        
                        @if($cancha->capacidad)
                        <p class="flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Capacidad: {{ $cancha->capacidad }} espectadores
                        </p>
                        @endif
                        
                        <p class="flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Superficie: {{ ucfirst(str_replace('_', ' ', $cancha->tipo_superficie)) }}
                        </p>
                        
                        <div class="flex items-center space-x-4 mt-1">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                {{ $cancha->techada ? 'Techada' : 'Al aire libre' }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                {{ $cancha->iluminacion ? 'Con luz' : 'Sin luz' }}
                            </span>
                        </div>
                        
                        @if($cancha->tarifa_por_hora)
                        <p class="flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Tarifa: ${{ number_format($cancha->tarifa_por_hora, 2) }} /hora
                        </p>
                        @endif
                        
                        @if($cancha->equipamiento)
                        <div class="mt-2 flex items-start">
                            <svg class="w-4 h-4 mr-1 text-primary-400 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="line-clamp-2">{{ $cancha->equipamiento }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="mt-3 text-sm text-gray-400">
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Creada: {{ $cancha->created_at->format('d/m/Y') }}
                        </p>
                        @if($cancha->updated_at != $cancha->created_at)
                        <p class="flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Actualizada: {{ $cancha->updated_at->format('d/m/Y') }}
                        </p>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-100 dark:bg-dark-900 px-4 py-3 flex justify-end space-x-2 border-t border-gray-300 dark:border-dark-700">
                    <button 
                        @click="editCancha({{ $cancha->toJson() }})"
                        class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 transition-colors"
                    >
                        Editar
                    </button>
                    <form action="{{ route('canchas.destroy', $cancha->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 transition-colors"
                            onclick="return confirm('¿Estás seguro de que deseas eliminar esta cancha?')">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @if($canchas->hasPages())
            <div class="mt-6 flex justify-between items-center">
                <!-- Botón Anterior -->
                @if($canchas->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Anterior</span>
                @else
                    <a href="{{ $canchas->previousPageUrl() }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Anterior</a>
                @endif

                <!-- Información de página -->
                <span class="text-gray-700">
                    Página {{ $canchas->currentPage() }} de {{ $canchas->lastPage() }}
                </span>

                <!-- Botón Siguiente -->
                @if($canchas->hasMorePages())
                    <a href="{{ $canchas->nextPageUrl() }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Siguiente</a>
                @else
                    <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Siguiente</span>
                @endif
            </div>
        @endif
    @endif
</div>