@props([
    'searchTerm' => request('search', ''),
    'selectedEstado' => request('estado', ''),
    'selectedSexo' => request('sexo', ''),
    'selectedLiga' => request('liga_id', ''),
    'selectedCategoria' => request('categoria_id', ''),
    'selectedEquipo' => request('equipo_id', ''),
    'selectedEdadMin' => request('edad_min', ''),
    'selectedEdadMax' => request('edad_max', ''),
    'selectedOrdenar' => request('ordenar', 'created_at'),
    'totalJugadores' => 0,
    'jugadoresFiltered' => 0,
    'ligas' => collect(),
    'categorias' => collect(),
    'equipos' => collect()
])

<div class="p-6 border-b border-gray-200 dark:border-dark-700 bg-white dark:bg-dark-800" 
     x-data="{ 
        showFilters: false,
        searchTerm: '{{ $searchTerm }}',
        selectedEstado: '{{ $selectedEstado }}',
        selectedSexo: '{{ $selectedSexo }}',
        selectedLiga: '{{ $selectedLiga }}',
        selectedCategoria: '{{ $selectedCategoria }}',
        selectedEquipo: '{{ $selectedEquipo }}',
        selectedEdadMin: '{{ $selectedEdadMin }}',
        selectedEdadMax: '{{ $selectedEdadMax }}',
        selectedOrdenar: '{{ $selectedOrdenar }}',
     }">
    
    <!-- Barra de búsqueda principal -->
    <form method="GET" action="{{ route('jugadores.index') }}" class="flex flex-col lg:flex-row gap-4 mb-4">
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input 
                type="text" 
                name="search"
                x-model="searchTerm"
                placeholder="Buscar jugadores por nombre, RFC, email o teléfono..."
                value="{{ $searchTerm }}"
                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors"
            >
        </div>
        
        <!-- Botón de filtros -->
        <button 
            type="button"
            @click="showFilters = !showFilters"
            class="flex items-center px-4 py-2.5 border border-gray-300 dark:border-dark-600 rounded-lg bg-white dark:bg-dark-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-dark-600 transition-colors"
            :class="{ 'bg-primary-50 dark:bg-primary-900 border-primary-300 dark:border-primary-600 text-primary-700 dark:text-primary-300': showFilters }"
        >
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
            </svg>
            <span x-text="showFilters ? 'Ocultar Filtros' : 'Filtros'"></span>
            <svg class="h-4 w-4 ml-2 transform transition-transform" :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <!-- Botón de búsqueda rápida -->
        <button 
            type="submit"
            class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors flex items-center"
        >
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Buscar
        </button>
    </form>
    
    <!-- Panel de filtros expandible -->
    <div x-show="showFilters" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0 transform scale-95" 
         x-transition:enter-end="opacity-100 transform scale-100" 
         x-transition:leave="transition ease-in duration-150" 
         x-transition:leave-start="opacity-100 transform scale-100" 
         x-transition:leave-end="opacity-0 transform scale-95" 
         class="bg-gray-50 dark:bg-dark-900 rounded-lg p-4 border border-gray-200 dark:border-dark-600">
        
        <form method="GET" action="{{ route('jugadores.index') }}" id="filtrosForm">
            <!-- Mantener término de búsqueda -->
            <input type="hidden" name="search" :value="searchTerm">
            
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Filtrar y ordenar por:</h3>
            
            <!-- Primera fila de filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- Filtro por Estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                    <select name="estado" x-model="selectedEstado" class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ $selectedEstado == '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ $selectedEstado == '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                
                <!-- Filtro por Sexo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sexo</label>
                    <select name="sexo" x-model="selectedSexo" class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Ambos sexos</option>
                        <option value="M" {{ $selectedSexo == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ $selectedSexo == 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>
                
                <!-- Edad Mínima -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Edad Mínima</label>
                    <input 
                        type="number" 
                        name="edad_min" 
                        x-model="selectedEdadMin"
                        min="5" 
                        max="80" 
                        placeholder="Ej: 15"
                        value="{{ $selectedEdadMin }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                
                <!-- Edad Máxima -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Edad Máxima</label>
                    <input 
                        type="number" 
                        name="edad_max" 
                        x-model="selectedEdadMax"
                        min="5" 
                        max="80" 
                        placeholder="Ej: 35"
                        value="{{ $selectedEdadMax }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
            
            <!-- Segunda fila de filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Filtro por Liga -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Liga</label>
                    <select name="liga_id" x-model="selectedLiga" class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Todas las ligas</option>
                        @foreach($ligas as $liga)
                            <option value="{{ $liga->id }}" {{ $selectedLiga == $liga->id ? 'selected' : '' }}>
                                {{ $liga->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtro por Categoría -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoría</label>
                    <select name="categoria_id" x-model="selectedCategoria" class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ $selectedCategoria == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtro por Equipo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Equipo Actual</label>
                    <select name="equipo_id" x-model="selectedEquipo" class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Todos los equipos</option>
                        @foreach($equipos as $equipo)
                            <option value="{{ $equipo->id }}" {{ $selectedEquipo == $equipo->id ? 'selected' : '' }}>
                                {{ $equipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Ordenar por -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ordenar por</label>
                    <select name="ordenar" x-model="selectedOrdenar" class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="created_at" {{ $selectedOrdenar == 'created_at' ? 'selected' : '' }}>Fecha de registro</option>
                        <option value="updated_at" {{ $selectedOrdenar == 'updated_at' ? 'selected' : '' }}>Última actualización</option>
                        <option value="nombre" {{ $selectedOrdenar == 'nombre' ? 'selected' : '' }}>Nombre</option>
                        <option value="edad" {{ $selectedOrdenar == 'edad' ? 'selected' : '' }}>Edad</option>
                        <option value="email" {{ $selectedOrdenar == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="fecha_nacimiento" {{ $selectedOrdenar == 'fecha_nacimiento' ? 'selected' : '' }}>Fecha de nacimiento</option>
                    </select>
                </div>
                
            </div>
            
            <!-- Botones de acción -->
            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200 dark:border-dark-600">
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>
                        @if($jugadoresFiltered > 0 || $searchTerm || $selectedEstado || $selectedSexo || $selectedLiga || $selectedCategoria || $selectedEquipo || $selectedEdadMin || $selectedEdadMax || $selectedOrdenar != 'created_at' || $selectedDireccion != 'desc')
                            Mostrando {{ $jugadoresFiltered }} de {{ $totalJugadores }} jugadores
                        @else
                            Mostrando {{ $totalJugadores }} jugadores
                        @endif
                    </span>
                </div>
                
                <div class="flex space-x-2">
                    <a href="{{ route('jugadores.index') }}" 
                       class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 border border-gray-300 dark:border-dark-600 rounded-md hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
                        Limpiar
                    </a>
                    <button 
                        type="submit"
                        class="px-3 py-1.5 text-sm bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition-colors">
                        Aplicar Filtros
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Indicadores de filtros activos -->
    @if($searchTerm || $selectedEstado || $selectedSexo || $selectedLiga || $selectedCategoria || $selectedEquipo || $selectedEdadMin || $selectedEdadMax || $selectedOrdenar != 'created_at')
        <div class="mt-4 flex flex-wrap gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400">Filtros activos:</span>
            
            @if($searchTerm)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200">
                    Búsqueda: "{{ $searchTerm }}"
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 text-primary-600 hover:text-primary-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if($selectedEstado !== '')
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                    Estado: {{ $selectedEstado == '1' ? 'Activos' : 'Inactivos' }}
                    <a href="{{ request()->fullUrlWithQuery(['estado' => null]) }}" class="ml-1 text-green-600 hover:text-green-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if($selectedSexo)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-pink-100 dark:bg-pink-900 text-pink-800 dark:text-pink-200">
                    Sexo: {{ $selectedSexo == 'M' ? 'Masculino' : 'Femenino' }}
                    <a href="{{ request()->fullUrlWithQuery(['sexo' => null]) }}" class="ml-1 text-pink-600 hover:text-pink-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if($selectedLiga)
                @php $ligaNombre = $ligas->find($selectedLiga)->nombre ?? 'Liga seleccionada'; @endphp
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                    Liga: {{ $ligaNombre }}
                    <a href="{{ request()->fullUrlWithQuery(['liga_id' => null]) }}" class="ml-1 text-indigo-600 hover:text-indigo-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if($selectedCategoria)
                @php $categoriaNombre = $categorias->find($selectedCategoria)->nombre ?? 'Categoría seleccionada'; @endphp
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-teal-100 dark:bg-teal-900 text-teal-800 dark:text-teal-200">
                    Categoría: {{ $categoriaNombre }}
                    <a href="{{ request()->fullUrlWithQuery(['categoria_id' => null]) }}" class="ml-1 text-teal-600 hover:text-teal-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if($selectedEquipo)
                @php $equipoNombre = $equipos->find($selectedEquipo)->nombre ?? 'Equipo seleccionado'; @endphp
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                    Equipo: {{ $equipoNombre }}
                    <a href="{{ request()->fullUrlWithQuery(['equipo_id' => null]) }}" class="ml-1 text-yellow-600 hover:text-yellow-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if($selectedEdadMin)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200">
                    Edad mín: {{ $selectedEdadMin }} años
                    <a href="{{ request()->fullUrlWithQuery(['edad_min' => null]) }}" class="ml-1 text-orange-600 hover:text-orange-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if($selectedEdadMax)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200">
                    Edad máx: {{ $selectedEdadMax }} años
                    <a href="{{ request()->fullUrlWithQuery(['edad_max' => null]) }}" class="ml-1 text-orange-600 hover:text-orange-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if($selectedOrdenar != 'created_at')
                @php
                    $ordenamientos = [
                        'nombre' => 'Nombre',
                        'edad' => 'Edad',
                        'email' => 'Email',
                        'fecha_nacimiento' => 'Fecha de nacimiento',
                        'updated_at' => 'Última actualización'
                    ];
                @endphp
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                    Ordenar: {{ $ordenamientos[$selectedOrdenar] ?? $selectedOrdenar }}
                    <a href="{{ request()->fullUrlWithQuery(['ordenar' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
        </div>
    @endif
</div>