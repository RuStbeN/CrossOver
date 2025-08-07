@extends('layouts.app')

@section('content')

    <main class="relative z-10 py-8" x-data="{
        showForm: false,
        editMode: false,
        editingTorneo: null,
        tab: 'general',
        showModal: false,
        activeTab: 'detalles',
        selectedTorneo: null,
        activeView: 'lista', // Nueva variable para controlar la vista
        formData: {
            nombre: '',
            descripcion: '',
            tipo: '',
            liga_id: '',
            temporada_id: '',
            categoria_id: '',
            cancha_id: '',
            fecha_inicio: '',
            fecha_fin: '',
            duracion_cuarto_minutos: 12,
            tiempo_entre_partidos_minutos: 15,
            premio_total: 0.00,
            estado: 'planificado',
            activo: '1',
            puntos_por_victoria: 3,
            puntos_por_empate: 1,
            puntos_por_derrota: 0,
            usa_playoffs: false,
            equipos_playoffs: null,
            esTorneoPorPuntos: false
        },
        resetForm() {
            this.formData = {
                nombre: '',
                descripcion: '',
                tipo: 'round_robin',
                liga_id: '',
                temporada_id: '',
                categoria_id: '',
                cancha_id: '',
                fecha_inicio: '',
                fecha_fin: '',
                duracion_cuarto_minutos: 12,
                tiempo_entre_partidos_minutos: 15,
                premio_total: 0.00,
                estado: 'planificado',
                activo: '1',
                puntos_por_victoria: 3,
                puntos_por_empate: 1,
                puntos_por_derrota: 0,
                usa_playoffs: false,
                equipos_playoffs: null
            };
            this.editMode = false;
            this.editingTorneo = null;
            this.tab = 'general';
        },
        openModal(torneo) {
            console.log('Torneo seleccionado:', torneo);
            this.selectedTorneo = torneo;
            this.showModal = true;
            this.activeTab = 'detalles';
            document.body.style.overflow = 'hidden';
        },
        closeModal() {
            this.showModal = false;
            this.selectedTorneo = null;
            document.body.style.overflow = 'auto';
        },
        editTorneo(torneo) {
            console.log('Datos del torneo:', torneo);
            this.formData = {
                nombre: torneo.nombre || '',
                descripcion: torneo.descripcion || '',
                tipo: torneo.tipo || 'round_robin',
                liga_id: torneo.liga_id || '',
                temporada_id: torneo.temporada_id || '',
                categoria_id: torneo.categoria_id || '',
                cancha_id: torneo.cancha_id || '',
                fecha_inicio: torneo.fecha_inicio?.split('T')[0] || '',
                fecha_fin: torneo.fecha_fin?.split('T')[0] || '',
                duracion_cuarto_minutos: torneo.duracion_cuarto_minutos || 12,
                tiempo_entre_partidos_minutos: torneo.tiempo_entre_partidos_minutos || 15,
                premio_total: torneo.premio_total || 0.00,
                estado: torneo.estado || 'planificado',
                activo: torneo.activo ? '1' : '0',
                puntos_por_victoria: torneo.puntos_por_victoria || 3,
                puntos_por_empate: torneo.puntos_por_empate || 1,
                puntos_por_derrota: torneo.puntos_por_derrota || 0,
                usa_playoffs: torneo.usa_playoffs || false,
                equipos_playoffs: torneo.equipos_playoffs || null
            };
            console.log('FormData después de asignar:', this.formData);
            this.editMode = true;
            this.editingTorneo = torneo;
            this.showForm = true;
            this.tab = 'general';
        },
        cancelEdit() {
            this.resetForm();
            this.showForm = false;
        },
        // Nuevos métodos agregados
        getEquipoNombre(equipoId) {
            const equipo = this.selectedTorneo.equipos.find(e => e.id == equipoId);
            return equipo ? equipo.nombre : 'Equipo Desconocido';
        },
        getGanador(juego) {
            if (juego.estado !== 'Finalizado' || juego.puntos_local === null || juego.puntos_visitante === null) {
                return null;
            }
            return juego.puntos_local > juego.puntos_visitante ? juego.equipo_local_id : juego.equipo_visitante_id;
        },
        getJuegosPorRonda() {
            const rondas = {};
            this.selectedTorneo.juegos.forEach(juego => {
                if (!rondas[juego.ronda]) {
                    rondas[juego.ronda] = [];
                }
                rondas[juego.ronda].push(juego);
            });
            
            // Ordenar juegos dentro de cada ronda por posición
            Object.keys(rondas).forEach(ronda => {
                rondas[ronda].sort((a, b) => a.posicion - b.posicion);
            });
            
            return rondas;
        }
    }">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Formulario de creación/edición -->
            <div
                x-show="showForm"
                x-transition
                class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 mb-8 overflow-hidden"
            >
                <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
                    <h2 class="text-xl font-bold dark:text-primary-400 text-black-900" x-text="editMode ? 'Editar Torneo' : 'Registrar Nuevo Torneo'"></h2>
                </div>

                <form :action="editMode ? '/torneos/' + editingTorneo.id : '{{ route('torneos.store') }}'" method="POST" class="p-6" enctype="multipart/form-data">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                

                    <input type="hidden" 
                        name="usa_playoffs" 
                        :value="formData.tipo === 'por_puntos' ? (formData.usa_playoffs ? '1' : '0') : '0'">

                    <input type="hidden"
                        name="equipos_playoffs"
                        :value="formData.tipo === 'por_puntos' ? formData.equipos_playoffs : ''">


                    <!-- Tabs -->
                    <div class="mb-6 flex space-x-4">
                        <button type="button" @click="tab = 'general'" :class="tab === 'general' ? 'bg-primary-600 text-white' : 'bg-dark-600 text-gray-300'" class="px-4 py-2 rounded-md font-medium">Información General</button>
                        <button type="button" @click="tab = 'asignaciones'" :class="tab === 'asignaciones' ? 'bg-primary-600 text-white' : 'bg-dark-600 text-gray-300'" class="px-4 py-2 rounded-md font-medium">Asignaciones</button>
                        <button type="button" 
                                @click="tab = 'puntos'" 
                                :class="{
                                    'bg-primary-600 text-white': tab === 'puntos',
                                    'bg-dark-600 text-gray-300': tab !== 'puntos',
                                    'hidden': formData.tipo !== 'por_puntos'
                                }" 
                                class="px-4 py-2 rounded-md font-medium">
                            Configuración de Puntos
                        </button>
                    </div>

                    <!-- TAB: INFORMACIÓN GENERAL -->
                    <div x-show="tab === 'general'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @include('admin.torneos.partials.formulario._general')
                    </div>

                    <!-- TAB: Asignaciones -->
                    <div x-show="tab === 'asignaciones'" x-transition x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        @include('admin.torneos.partials.formulario._asignaciones')
                    </div>

                    <!-- TAB: CONFIGURACIÓN DE PUNTOS -->
                    <div x-show="tab === 'puntos' && formData.tipo === 'por_puntos'" x-transition x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        @include('admin.torneos.partials.formulario._puntos')
                    </div>
                    

                    <!-- Botones de acción -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button 
                            type="button"
                            @click="cancelEdit()"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 rounded-md text-white font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-dark-800"
                        >
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 rounded-md text-white font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-dark-800"
                            x-text="editMode ? 'Actualizar Torneo' : 'Registrar Torneo'"
                        >
                        </button>
                    </div>
                </form>
            </div>

            <!-- Lista de torneos -->
            <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
                @include('admin.torneos.partials.lista.listado_torneos')
            </div>
            
        </div>

        <!-- Modal para ver detalles del torneo -->
        <div x-show="showModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50"
            @click.self="closeModal()">
            <div x-show="showModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="bg-white dark:bg-dark-800 rounded-lg shadow-xl w-full max-w-5xl h-[70vh] flex flex-col">
                
                <!-- Header del modal - FIJO -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between flex-shrink-0">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Detalles del Torneo</h2>
                    <button @click="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Pestañas del modal - FIJO -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700 flex-shrink-0">
                    <ul class="flex space-x-6">
                        <li><button @click="activeTab = 'detalles'" :class="{ 'text-blue-500 border-blue-500': activeTab === 'detalles' }" class="text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-blue-500 hover:border-blue-500 transition-colors duration-300">Detalles</button></li>
                        <li><button @click="activeTab = 'juegos'" :class="{ 'text-blue-500 border-blue-500': activeTab === 'juegos' }" class="text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-blue-500 hover:border-blue-500 transition-colors duration-300">Juegos</button></li>
                        <template x-if="selectedTorneo && selectedTorneo.tipo === 'doble_eliminacion'">
                            <li><button @click="activeTab = 'bracket'" :class="{ 'text-blue-500 border-blue-500': activeTab === 'bracket' }" class="text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-blue-500 hover:border-blue-500 transition-colors duration-300">Bracket</button></li>
                        </template>
                        <template x-if="selectedTorneo && selectedTorneo.tipo === 'por_puntos'">
                            <li><button @click="activeTab = 'clasificacion'" :class="{ 'text-blue-500 border-blue-500': activeTab === 'clasificacion' }" class="text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-blue-500 hover:border-blue-500 transition-colors duration-300">Clasificación</button></li>
                        </template>
                    </ul>
                </div>
                
                <!-- Contenido del modal - CON SCROLL -->
                <div class="flex-1 overflow-y-auto px-6 py-3">
                    <template x-if="selectedTorneo">
                        <div class="space-y-4">
                            <div x-show="activeTab === 'detalles'" class="space-y-4">
                                @include('admin.torneos.partials.modal.encabezado')
                                @include('admin.torneos.partials.modal.informacion')
                            </div>
                            <div x-show="activeTab === 'juegos'" class="space-y-4">
                                @include('admin.torneos.partials.modal.lista_juegos')
                            </div>
                            <template x-if="selectedTorneo.tipo === 'doble_eliminacion'">
                                <div x-show="activeTab === 'bracket'" class="space-y-4">
                                    @include('admin.torneos.partials.modal.bracket_juegos')
                                </div>
                            </template>
                            <template x-if="selectedTorneo.tipo === 'por_puntos'">
                                <div x-show="activeTab === 'clasificacion'" class="space-y-4">
                                    @include('admin.torneos.partials.modal.clasificacion')
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
                
                <!-- Footer del modal - FIJO -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex justify-end space-x-3 flex-shrink-0">
                    <button @click="closeModal()" 
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium transition-colors">
                        Cerrar
                    </button>
                    <button @click="editTorneo(selectedTorneo); closeModal()" 
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition-colors">
                        Editar Torneo
                    </button>
                </div>
            </div>
        </div>
        
    </main>
@endsection

