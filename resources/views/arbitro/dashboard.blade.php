@extends('layouts.arbitro')
<head>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('iniciarPartidoModal', {
            show: false,
            selectedPartidoId: null,
            equipoLocal: null,
            equipoVisitante: null,
            equipoLocalNombre: '',
            equipoVisitanteNombre: '',
            jugadoresLocal: [],
            jugadoresVisitante: [],
            titularesLocal: [],
            titularesVisitante: [],
            isLoading: false,
            error: null,
            
            async open(partidoId, equipoLocalId, equipoVisitanteId, equipoLocalNombre, equipoVisitanteNombre) {
                this.selectedPartidoId = partidoId;
                this.equipoLocal = equipoLocalId;
                this.equipoVisitante = equipoVisitanteId;
                this.equipoLocalNombre = equipoLocalNombre;
                this.equipoVisitanteNombre = equipoVisitanteNombre;
                this.isLoading = true;
                this.error = null;
                
                try {
                    const response = await fetch(`/api/partidos/${partidoId}/jugadores`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'include'
                    });
                    
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.error || 'Error al cargar los jugadores');
                    }
                    
                    const data = await response.json();
                    
                    this.jugadoresLocal = data.local || [];
                    this.jugadoresVisitante = data.visitante || [];
                    this.titularesLocal = [];
                    this.titularesVisitante = [];
                    
                    if (this.jugadoresLocal.length === 0 && this.jugadoresVisitante.length === 0) {
                        this.error = 'No se encontraron jugadores activos para este partido';
                    }
                    
                    this.show = true;
                } catch (error) {
                    console.error('Error al cargar jugadores:', error);
                    this.error = error.message;
                    alert(this.error);
                } finally {
                    this.isLoading = false;
                }
            },
                
            close() {
                this.show = false;
                setTimeout(() => {
                    this.selectedPartidoId = null;
                    this.equipoLocal = null;
                    this.equipoVisitante = null;
                    this.equipoLocalNombre = '';
                    this.equipoVisitanteNombre = '';
                    this.jugadoresLocal = [];
                    this.jugadoresVisitante = [];
                    this.titularesLocal = [];
                    this.titularesVisitante = [];
                    this.error = null;
                    this.isLoading = false;
                }, 100);
            },
            
            toggleJugadorLocal(jugadorId) {
                const index = this.titularesLocal.indexOf(jugadorId);
                if (index === -1) {
                    if (this.titularesLocal.length < 5) {
                        this.titularesLocal.push(jugadorId);
                    }
                } else {
                    this.titularesLocal.splice(index, 1);
                }
            },
            
            toggleJugadorVisitante(jugadorId) {
                const index = this.titularesVisitante.indexOf(jugadorId);
                if (index === -1) {
                    if (this.titularesVisitante.length < 5) {
                        this.titularesVisitante.push(jugadorId);
                    }
                } else {
                    this.titularesVisitante.splice(index, 1);
                }
            },
            
            async confirmarInicio() {
                if (this.titularesLocal.length === 0 && this.titularesVisitante.length === 0) {
                    alert('Debes seleccionar al menos un jugador titular');
                    return;
                }
                
                try {
                    const response = await fetch(`/api/partidos/${this.selectedPartidoId}/iniciar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            titulares_local: this.titularesLocal,
                            titulares_visitante: this.titularesVisitante
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        this.close();
                        window.location.href = data.redirect_url;
                    } else {
                        alert(data.error || 'Error al iniciar el partido');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al iniciar el partido');
                }
            }
        });

        // Store para filtros
        Alpine.store('filtros', {
            fecha: '',
            torneo: '',
            equipo: '',
            estado: '',
            initialized: false,
            
            init() {
                // Obtener fecha actual en zona horaria de México
                const now = new Date();
                const mexicoDate = new Date(now.toLocaleString("en-US", {timeZone: "America/Mexico_City"}));
                const today = mexicoDate.getFullYear() + '-' + 
                    String(mexicoDate.getMonth() + 1).padStart(2, '0') + '-' + 
                    String(mexicoDate.getDate()).padStart(2, '0');
                
                // Inicializar valores desde la URL o usar valores por defecto
                const urlParams = new URLSearchParams(window.location.search);
                this.fecha = urlParams.get('fecha') || today;
                this.torneo = urlParams.get('torneo') || '';
                this.equipo = urlParams.get('equipo') || '';
                this.estado = urlParams.get('estado') || '';
                
                this.initialized = true;
                
                // Solo aplicar filtros si no hay parámetros en la URL (primera carga)
                if (!window.location.search && !urlParams.has('fecha')) {
                    this.aplicarFiltros();
                }
            },
            
            aplicarFiltros() {
                if (!this.initialized) return;
                
                const params = new URLSearchParams();
                if (this.fecha) params.append('fecha', this.fecha);
                if (this.torneo) params.append('torneo', this.torneo);
                if (this.equipo) params.append('equipo', this.equipo);
                if (this.estado) params.append('estado', this.estado);
                
                // Evitar recarga innecesaria si los parámetros son iguales
                const currentParams = new URLSearchParams(window.location.search);
                const newParamsString = params.toString();
                const currentParamsString = currentParams.toString();
                
                if (newParamsString !== currentParamsString) {
                    window.location.href = '?' + newParamsString;
                }
            },
            
            limpiarFiltros() {
                const now = new Date();
                const mexicoDate = new Date(now.toLocaleString("en-US", {timeZone: "America/Mexico_City"}));
                const today = mexicoDate.getFullYear() + '-' + 
                    String(mexicoDate.getMonth() + 1).padStart(2, '0') + '-' + 
                    String(mexicoDate.getDate()).padStart(2, '0');
                
                this.fecha = today;
                this.torneo = '';
                this.equipo = '';
                this.estado = '';
                this.aplicarFiltros();
            }
        });
    });
</script>
@endpush

@section('content')
<main class="relative z-10 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Encabezado compacto -->
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 mb-6">
            <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900">
                <h2 class="text-xl font-bold text-black dark:text-primary-400">Panel del Árbitro</h2>
            </div>
            
            <!-- Estadísticas compactas -->
            <div class="p-4 grid grid-cols-3 gap-4">
                <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalPartidos }}</p>
                    <p class="text-sm text-blue-800 dark:text-blue-300">Total</p>
                </div>
                <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $partidosCompletados }}</p>
                    <p class="text-sm text-green-800 dark:text-green-300">Completados</p>
                </div>
                <div class="text-center p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-700">
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $partidosPendientes }}</p>
                    <p class="text-sm text-yellow-800 dark:text-yellow-300">Pendientes</p>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 mb-6">
            <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900">
                <h3 class="text-lg font-bold text-black dark:text-primary-400">Filtros de Búsqueda</h3>
            </div>
            
            <div class="p-4" x-data="$store.filtros">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha</label>
                        <input 
                            type="date" 
                            x-model="fecha"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Torneo</label>
                        <select 
                            x-model="torneo"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="">Todos los torneos</option>
                            @foreach($torneos ?? [] as $torneoOption)
                                <option value="{{ $torneoOption->id }}">{{ $torneoOption->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Equipo</label>
                        <input 
                            type="text" 
                            x-model="equipo"
                            placeholder="Buscar equipo..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                        <select 
                            x-model="estado"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md bg-white dark:bg-dark-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="">Todos los estados</option>
                            <option value="Programado">Programado</option>
                            <option value="En Curso">En Curso</option>
                            <option value="Finalizado">Finalizado</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end space-x-2">
                        <button 
                            @click="aplicarFiltros()"
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition-colors flex-1"
                        >
                            Filtrar
                        </button>
                        <button 
                            @click="limpiarFiltros()"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md font-medium transition-colors"
                        >
                            Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de partidos compacta -->
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700">
            <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-black dark:text-primary-400">Partidos Asignados</h2>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        @if(request('fecha'))
                            Fecha: {{ \Carbon\Carbon::parse(request('fecha'))->format('d/m/Y') }}
                        @else
                            Mostrando partidos de hoy
                        @endif
                    </span>
                </div>
            </div>

            <div class="p-6">
                @if($partidos->isEmpty())
                    <div class="text-center py-8 text-gray-400">
                        <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium dark:text-gray-300 text-dark-800">No hay partidos para mostrar</h3>
                        <p class="mt-1 text-sm dark:text-gray-300 text-dark-700">
                            @if(request()->hasAny(['fecha', 'torneo', 'equipo', 'estado']))
                                Intenta cambiar los filtros para ver más partidos.
                            @else
                                Los partidos asignados aparecerán aquí.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($partidos as $partido)
                        <div class="bg-white dark:bg-dark-700 rounded-lg border border-gray-300 dark:border-dark-600 overflow-hidden hover:border-primary-500 hover:shadow-md transition-all duration-200">
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <!-- Info principal del partido -->
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-1">
                                                <h3 class="text-base font-bold text-gray-800 dark:text-white">
                                                    {{ $partido->equipoLocal->nombre }} 
                                                    <span class="text-primary-600 dark:text-primary-400">vs</span> 
                                                    {{ $partido->equipoVisitante->nombre }}
                                                </h3>
                                                <div class="flex items-center space-x-4 mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ \Carbon\Carbon::parse($partido->fecha)->format('d/m/Y') }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ \Carbon\Carbon::parse($partido->hora)->format('h:i A') }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        </svg>
                                                        {{ $partido->cancha->nombre }}
                                                    </span>
                                                    @if($partido->torneo)
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                                        </svg>
                                                        {{ $partido->torneo->nombre }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Estado y resultado -->
                                            <div class="text-right">
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                    @if($partido->estado === 'Programado') bg-blue-500 text-white
                                                    @elseif($partido->estado === 'En Curso') bg-yellow-500 text-white
                                                    @elseif($partido->estado === 'Finalizado') bg-green-500 text-white
                                                    @elseif($partido->estado === 'Cancelado') bg-red-500 text-white
                                                    @elseif($partido->estado === 'Suspendido') bg-gray-500 text-white @endif">
                                                    {{ $partido->estado }}
                                                </span>
                                                
                                                @if($partido->estado === 'Finalizado')
                                                <div class="mt-2">
                                                    <div class="text-lg font-bold text-gray-800 dark:text-white mb-2">
                                                        {{ $partido->puntos_local ?? '0' }} - {{ $partido->puntos_visitante ?? '0' }}
                                                    </div>
                                                    <a href="{{ route('arbitro.partido.resultados', ['juego' => $partido->id]) }}" 
                                                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-md font-medium transition-colors inline-flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                        </svg>
                                                        Ver Estadísticas
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Acciones -->
                                            @if($partido->estado !== 'Finalizado' && $partido->estado !== 'Cancelado')
                                            <div class="ml-4">
                                                @if($partido->estado === 'Programado')
                                                <button 
                                                    @click="$store.iniciarPartidoModal.open({{ $partido->id }}, {{ $partido->equipo_local_id }}, {{ $partido->equipo_visitante_id }}, '{{ $partido->equipoLocal->nombre }}', '{{ $partido->equipoVisitante->nombre }}')"
                                                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-md font-medium transition-colors"
                                                >
                                                    Iniciar
                                                </button>
                                                @else
                                                <a 
                                                    href="{{ route('arbitro.partidos.ver', ['juego' => $partido->id]) }}" 
                                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-md font-medium transition-colors"
                                                >
                                                    Continuar
                                                </a>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Observaciones si las hay -->
                                @if($partido->estado === 'Finalizado' && $partido->observaciones)
                                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-dark-600">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">Observaciones:</span> {{ $partido->observaciones }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($partidos->hasPages())
                        <div class="mt-6 flex justify-between items-center">
                            @if($partidos->onFirstPage())
                                <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Anterior</span>
                            @else
                                <a href="{{ $partidos->appends(request()->query())->previousPageUrl() }}" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">Anterior</a>
                            @endif

                            <span class="text-gray-700 dark:text-gray-300">
                                Página {{ $partidos->currentPage() }} de {{ $partidos->lastPage() }}
                            </span>

                            @if($partidos->hasMorePages())
                                <a href="{{ $partidos->appends(request()->query())->nextPageUrl() }}" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">Siguiente</a>
                            @else
                                <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Siguiente</span>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Modal para seleccionar jugadores titulares (sin cambios en la funcionalidad) -->
    <div 
    x-show="$store.iniciarPartidoModal.show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
    @click.self="$store.iniciarPartidoModal.close()"
    style="display: none;"
>

    <div class="bg-white dark:bg-dark-800 rounded-xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">

            <!-- Header -->
            <div class="px-8 py-6 border-b border-gray-200 dark:border-dark-700 bg-gradient-to-r from-primary-500 to-primary-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold">Seleccionar Jugadores Titulares</h2>
                        <p class="text-primary-100 mt-1">
                            <span x-text="$store.iniciarPartidoModal.equipoLocalNombre"></span> 
                            vs 
                            <span x-text="$store.iniciarPartidoModal.equipoVisitanteNombre"></span>
                        </p>
                    </div>
                    <button @click="$store.iniciarPartidoModal.close()" class="text-primary-100 hover:text-white transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Contenido -->
            <div class="p-8">
                <!-- Estado de carga -->
                <div x-show="$store.iniciarPartidoModal.isLoading" class="text-center py-12">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-primary-600 mx-auto"></div>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">Cargando jugadores...</p>
                </div>
                
                <!-- Error -->
                <div x-show="$store.iniciarPartidoModal.error && !$store.iniciarPartidoModal.isLoading" class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg p-6 mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-red-700 dark:text-red-400 font-medium" x-text="$store.iniciarPartidoModal.error"></p>
                    </div>
                </div>
                
                <!-- Equipos -->
                <div x-show="!$store.iniciarPartidoModal.isLoading && !$store.iniciarPartidoModal.error" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Equipo Local -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-6 border border-blue-200 dark:border-blue-700">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-blue-800 dark:text-blue-300 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z"></path>
                                </svg>
                                <span x-text="$store.iniciarPartidoModal.equipoLocalNombre"></span>
                            </h3>
                            <div class="text-sm font-semibold px-3 py-1 bg-blue-200 dark:bg-blue-700 text-blue-800 dark:text-blue-200 rounded-full">
                                <span x-text="$store.iniciarPartidoModal.titularesLocal.length"></span>/5 Seleccionados
                            </div>
                        </div>
                        
                        <template x-if="$store.iniciarPartidoModal.jugadoresLocal.length === 0">
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">No hay jugadores disponibles</p>
                            </div>
                        </template>
                        
                        <div class="space-y-3" x-show="$store.iniciarPartidoModal.jugadoresLocal.length > 0">
                            <template x-for="jugador in $store.iniciarPartidoModal.jugadoresLocal" :key="jugador.id">
                                <div 
                                    @click="$store.iniciarPartidoModal.toggleJugadorLocal(jugador.id)"
                                    class="p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:shadow-md transform hover:-translate-y-1"
                                    :class="{
                                        'border-primary-500 bg-primary-50 dark:bg-primary-900/30 shadow-lg': $store.iniciarPartidoModal.titularesLocal.includes(jugador.id),
                                        'border-gray-200 dark:border-dark-600 bg-white dark:bg-dark-700 hover:border-primary-300': !$store.iniciarPartidoModal.titularesLocal.includes(jugador.id)
                                    }"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg"
                                                 x-text="jugador.numero_camiseta">
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 dark:text-white text-lg" x-text="jugador.nombre"></p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium" x-text="jugador.posicion_principal"></p>
                                            </div>
                                        </div>
                                        <div 
                                            x-show="$store.iniciarPartidoModal.titularesLocal.includes(jugador.id)"
                                            class="text-primary-600 dark:text-primary-400"
                                        >
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Equipo Visitante -->
                    <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-xl p-6 border border-red-200 dark:border-red-700">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-red-800 dark:text-red-300 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2v16z"></path>
                                </svg>
                                <span x-text="$store.iniciarPartidoModal.equipoVisitanteNombre"></span>
                            </h3>
                            <div class="text-sm font-semibold px-3 py-1 bg-red-200 dark:bg-red-700 text-red-800 dark:text-red-200 rounded-full">
                                <span x-text="$store.iniciarPartidoModal.titularesVisitante.length"></span>/5 Seleccionados
                            </div>
                        </div>
                        
                        <template x-if="$store.iniciarPartidoModal.jugadoresVisitante.length === 0">
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">No hay jugadores disponibles</p>
                            </div>
                        </template>
                        
                        <div class="space-y-3" x-show="$store.iniciarPartidoModal.jugadoresVisitante.length > 0">
                            <template x-for="jugador in $store.iniciarPartidoModal.jugadoresVisitante" :key="jugador.id">
                                <div 
                                    @click="$store.iniciarPartidoModal.toggleJugadorVisitante(jugador.id)"
                                    class="p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:shadow-md transform hover:-translate-y-1"
                                    :class="{
                                        'border-primary-500 bg-primary-50 dark:bg-primary-900/30 shadow-lg': $store.iniciarPartidoModal.titularesVisitante.includes(jugador.id),
                                        'border-gray-200 dark:border-dark-600 bg-white dark:bg-dark-700 hover:border-primary-300': !$store.iniciarPartidoModal.titularesVisitante.includes(jugador.id)
                                    }"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg"
                                                 x-text="jugador.numero_camiseta">
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 dark:text-white text-lg" x-text="jugador.nombre"></p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium" x-text="jugador.posicion_principal"></p>
                                            </div>
                                        </div>
                                        <div 
                                            x-show="$store.iniciarPartidoModal.titularesVisitante.includes(jugador.id)"
                                            class="text-primary-600 dark:text-primary-400"
                                        >
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div x-show="!$store.iniciarPartidoModal.isLoading && !$store.iniciarPartidoModal.error" class="mt-8 flex justify-between items-center bg-gray-50 dark:bg-dark-900 rounded-xl p-6">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p class="font-medium">Instrucciones:</p>
                        <p>Selecciona hasta 5 jugadores titulares por equipo. Al menos un jugador debe ser seleccionado para iniciar el partido.</p>
                    </div>
                    <div class="flex space-x-4">
                        <button 
                            @click="$store.iniciarPartidoModal.close()"
                            class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg transition-colors duration-200"
                        >
                            Cancelar
                        </button>
                        <button 
                            @click="$store.iniciarPartidoModal.confirmarInicio()"
                            class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors duration-200 flex items-center"
                            :disabled="$store.iniciarPartidoModal.titularesLocal.length === 0 && $store.iniciarPartidoModal.titularesVisitante.length === 0"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Iniciar Partido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection