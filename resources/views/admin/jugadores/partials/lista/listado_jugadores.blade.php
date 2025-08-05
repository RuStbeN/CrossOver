<div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-black dark:text-primary-400 ">Jugadores Registrados</h2>
    <!-- Botón para mostrar/ocultar el formulario -->
    <button
        @click="showForm = !showForm; if (!showForm) resetForm()"
        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition"
    >
        <span x-text="showForm ? 'Ocultar Formulario' : 'Registrar Nuevo Jugador'"></span>
    </button>
</div>

<div class="p-6">
    @if($jugadores->isEmpty())
        <div class="text-center py-8 text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium dark:text-gray-300 text-dark-800">No hay jugadores registrados</h3>
            <p class="mt-1 text-sm dark:text-gray-300 text-dark-700">Comienza registrando un nuevo jugador.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-stretch">
            @foreach($jugadores as $jugador)
            <div class="bg-white dark:bg-dark-700 rounded-lg border border-gray-300 dark:border-dark-600 overflow-hidden hover:border-primary-500 transition-colors flex flex-col h-full cursor-pointer"
                    @click="openModal({{ $jugador->toJson() }})">
                <div class="p-4 flex-1">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            @if($jugador->foto_url)
                                <img src="{{ asset('storage/' . str_replace('public/', '', $jugador->foto_url)) }}" 
                                    alt="Foto de {{ $jugador->nombre }}" 
                                    class="w-10 h-10 rounded-full object-cover mr-3">
                            @else
                            <div class="w-12 h-12 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold mr-3">
                                {{ strtoupper(substr($jugador->nombre, 0, 1)) }}
                            </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                                    {{ $jugador->nombre }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $jugador->edad }} años
                                </p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold {{ $jugador->activo ? 'bg-green-500' : 'bg-red-500' }} text-white rounded-full">
                            {{ $jugador->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    
                    <div class="mt-3 text-sm text-gray-700 dark:text-gray-300 space-y-2">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <span class="font-medium">Número {{ $jugador->numero_actual ?? 'N/A' }}</span> • 
                                {{ $jugador->posicion_actual ?? 'Sin posición' }}
                            </div>
                        </div>

                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>{{ $jugador->equipo_actual->nombre ?? 'Sin equipo' }}</span>
                        </div>

                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $jugador->liga ? $jugador->liga->nombre : 'Sin liga' }}</span>
                        </div>

                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>{{ $jugador->categoria ? $jugador->categoria->nombre : 'Sin categoria' }}</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-100 dark:bg-dark-900 px-4 py-3 flex justify-end space-x-2 border-t border-gray-300 dark:border-dark-700">
                    <button 
                        @click.stop="editJugador({{ $jugador->toJson() }})"
                        class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 transition-colors"
                    >
                        Editar
                    </button>
                    <form action="{{ route('jugadores.destroy', $jugador->id) }}" method="POST" class="inline" @click.stop>
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('¿Estás seguro de eliminar este jugador?')"
                                class="text-sm text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300 transition-colors">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        @if($jugadores->hasPages())
            <div class="mt-6 flex justify-between items-center">
                <!-- Botón Anterior -->
                @if($jugadores->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded cursor-not-allowed">Anterior</span>
                @else
                    <a href="{{ $jugadores->previousPageUrl() }}" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 transition">Anterior</a>
                @endif

                <!-- Información de página -->
                <span class="text-gray-700 dark:text-gray-300">
                    Página {{ $jugadores->currentPage() }} de {{ $jugadores->lastPage() }}
                </span>

                <!-- Botón Siguiente -->
                @if($jugadores->hasMorePages())
                    <a href="{{ $jugadores->nextPageUrl() }}" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700 transition">Siguiente</a>
                @else
                    <span class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded cursor-not-allowed">Siguiente</span>
                @endif
            </div>
        @endif
    @endif
</div>