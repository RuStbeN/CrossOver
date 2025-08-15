@props([
    'searchTerm' => request('search', ''),
    'selectedEstado' => request('estado', ''),
    'selectedPeriodo' => request('periodo', ''),
    'selectedOrdenar' => request('ordenar', 'fecha_inicio'),
    'selectedDireccion' => request('direccion', 'desc'),
    'totalTemporadas' => 0,
    'temporadasFiltered' => 0
])

<div class="p-6 border-b border-gray-200 dark:border-dark-700 bg-white dark:bg-dark-800" 
     x-data="{ 
        showFilters: false,
        searchTerm: '{{ $searchTerm }}',
        selectedEstado: '{{ $selectedEstado }}',
        selectedPeriodo: '{{ $selectedPeriodo }}',
        selectedOrdenar: '{{ $selectedOrdenar }}',
        selectedDireccion: '{{ $selectedDireccion }}'
     }">
    
    <!-- Barra de búsqueda principal -->
    <form method="GET" action="{{ route('temporadas.index') }}" class="flex flex-col lg:flex-row gap-4 mb-4">
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
                placeholder="Buscar temporadas por nombre o descripción..."
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
        
        <form method="GET" action="{{ route('temporadas.index') }}" id="filtrosForm">
            <!-- Mantener término de búsqueda -->
            <input type="hidden" name="search" :value="searchTerm">
            
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Filtrar y ordenar por:</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Filtro por Estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                    <select name="estado" x-model="selectedEstado" class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ $selectedEstado == '1' ? 'selected' : '' }}>Activas</option>
                        <option value="0" {{ $selectedEstado == '0' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                
                <!-- Filtro por Período -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Período</label>
                    <select name="periodo" x-model="selectedPeriodo" class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Todos los períodos</option>
                        <option value="vigente" {{ $selectedPeriodo == 'vigente' ? 'selected' : '' }}>Vigentes</option>
                        <option value="futura" {{ $selectedPeriodo == 'futura' ? 'selected' : '' }}>Futuras</option>
                        <option value="pasada" {{ $selectedPeriodo == 'pasada' ? 'selected' : '' }}>Pasadas</option>
                    </select>
                </div>
                
                <!-- Ordenar por -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ordenar por</label>
                    <select name="ordenar" x-model="selectedOrdenar" class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="fecha_inicio" {{ $selectedOrdenar == 'fecha_inicio' ? 'selected' : '' }}>Fecha de inicio</option>
                        <option value="fecha_fin" {{ $selectedOrdenar == 'fecha_fin' ? 'selected' : '' }}>Fecha de fin</option>
                        <option value="nombre" {{ $selectedOrdenar == 'nombre' ? 'selected' : '' }}>Nombre</option>
                        <option value="created_at" {{ $selectedOrdenar == 'created_at' ? 'selected' : '' }}>Fecha de creación</option>
                        <option value="updated_at" {{ $selectedOrdenar == 'updated_at' ? 'selected' : '' }}>Última actualización</option>
                    </select>
                </div>
                
                <!-- Dirección de ordenamiento -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dirección</label>
                    <select name="direccion" x-model="selectedDireccion" class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="desc" {{ $selectedDireccion == 'desc' ? 'selected' : '' }}>Descendente</option>
                        <option value="asc" {{ $selectedDireccion == 'asc' ? 'selected' : '' }}>Ascendente</option>
                    </select>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200 dark:border-dark-600">
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>
                        @if($temporadasFiltered > 0 || $searchTerm || $selectedEstado || $selectedPeriodo || $selectedOrdenar != 'fecha_inicio' || $selectedDireccion != 'desc')
                            Mostrando {{ $temporadasFiltered }} de {{ $totalTemporadas }} temporadas
                        @else
                            Mostrando {{ $totalTemporadas }} temporadas
                        @endif
                    </span>
                </div>
                
                <div class="flex space-x-2">
                    <a href="{{ route('temporadas.index') }}" 
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
    @if($searchTerm || $selectedEstado || $selectedPeriodo || $selectedOrdenar != 'fecha_inicio' || $selectedDireccion != 'desc')
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
                    Estado: {{ $selectedEstado == '1' ? 'Activas' : 'Inactivas' }}
                    <a href="{{ request()->fullUrlWithQuery(['estado' => null]) }}" class="ml-1 text-green-600 hover:text-green-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if($selectedPeriodo !== '')
                @php
                    $periodos = [
                        'vigente' => 'Vigentes',
                        'futura' => 'Futuras',
                        'pasada' => 'Pasadas'
                    ];
                @endphp
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                    Período: {{ $periodos[$selectedPeriodo] ?? $selectedPeriodo }}
                    <a href="{{ request()->fullUrlWithQuery(['periodo' => null]) }}" class="ml-1 text-yellow-600 hover:text-yellow-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if($selectedOrdenar != 'fecha_inicio')
                @php
                    $ordenamientos = [
                        'nombre' => 'Nombre',
                        'fecha_fin' => 'Fecha de fin',
                        'created_at' => 'Fecha de creación',
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
            
            @if($selectedDireccion != 'desc')
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                    Dirección: Ascendente
                    <a href="{{ request()->fullUrlWithQuery(['direccion' => null]) }}" class="ml-1 text-purple-600 hover:text-purple-500">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
        </div>
    @endif
</div>