@extends('layouts.app')

@section('content')

    <main class="relative z-10 py-8" x-data="{
        showForm: false, 
        editMode: false, 
        editingJuego: null,
        tab: 'general',
        showModal: false,
        selectedJuego: null,
        formData: {
            // Información general
            liga_id: '',
            temporada_id: '',
            torneo_id: '',
            fecha: '',
            hora: '',
            cancha_id: '',
            duracion_cuarto: 10,
            duracion_descanso: 5,
            estado: 'Programado',
            observaciones: '',
            activo: '1',
            
            // Asignación de equipos
            equipo_local_id: '',
            equipo_visitante_id: '',
            
            // Árbitros y mesa de control
            arbitro_principal_id: '',
            arbitro_auxiliar_id: '',
            mesa_control_id: ''
        },
        resetForm() {
            this.formData = {
                liga_id: '',
                temporada_id: '',
                torneo_id: '',
                fecha: '',
                hora: '',
                cancha_id: '',
                duracion_cuarto: 10,
                duracion_descanso: 5,
                estado: 'Programado',
                observaciones: '',
                activo: '1',
                equipo_local_id: '',
                equipo_visitante_id: '',
                arbitro_principal_id: '',
                arbitro_auxiliar_id: '',
                mesa_control_id: ''
            };
            this.editMode = false;
            this.editingJuego = null;
            this.tab = 'general';
        },
        openModal(juego) {
            this.selectedJuego = juego;
            this.showModal = true;
            document.body.style.overflow = 'hidden';
        },
        closeModal() {
            this.showModal = false;
            this.selectedJuego = null;
            document.body.style.overflow = 'auto';
        },
        editJuego(juego) {
            console.log('Datos del juego:', juego);
            
            this.formData = {
                // Información general
                liga_id: juego.liga_id || '',
                temporada_id: juego.temporada_id || '',
                torneo_id: juego.torneo_id || '',
                fecha: juego.fecha?.split('T')[0] || '',
                hora: juego.hora || '',
                cancha_id: juego.cancha_id || '',
                duracion_cuarto: juego.duracion_cuarto || 10,
                duracion_descanso: juego.duracion_descanso || 5,
                estado: juego.estado || 'Programado',
                observaciones: juego.observaciones || '',
                activo: juego.activo ? '1' : '0',
                
                // Asignación de equipos
                equipo_local_id: juego.equipo_local_id || '',
                equipo_visitante_id: juego.equipo_visitante_id || '',
                
                // Árbitros y mesa de control
                arbitro_principal_id: juego.arbitro_principal_id || '',
                arbitro_auxiliar_id: juego.arbitro_auxiliar_id || '',
                mesa_control_id: juego.mesa_control_id || ''
            };
            
            console.log('FormData después de asignar:', this.formData);
            
            this.editMode = true;
            this.editingJuego = juego;
            this.showForm = true;
            this.tab = 'general';
        },
        cancelEdit() {
            this.resetForm();
            this.showForm = false;
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
                    <h2 class="text-xl font-bold dark:text-primary-400 text-black-900" x-text="editMode ? 'Editar Juego' : 'Registrar Nuevo Juego'"></h2>
                </div>

                <form :action="editMode ? '/juegos/' + editingJuego.id : '{{ route('juegos.store') }}'" method="POST" class="p-6" enctype="multipart/form-data">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Tabs -->
                    <div class="mb-6 flex space-x-4">
                        <button type="button" @click="tab = 'general'" :class="tab === 'general' ? 'bg-primary-600 text-white' : 'bg-dark-600 text-gray-300'" class="px-4 py-2 rounded-md font-medium">Información General</button>
                        <button type="button" @click="tab = 'equipos'" :class="tab === 'equipos' ? 'bg-primary-600 text-white' : 'bg-dark-600 text-gray-300'" class="px-4 py-2 rounded-md font-medium">Asignación de Equipos</button>
                        <button type="button" @click="tab = 'arbitros'" :class="tab === 'arbitros' ? 'bg-primary-600 text-white' : 'bg-dark-600 text-gray-300'" class="px-4 py-2 rounded-md font-medium">Árbitros y Mesa</button>
                    </div>

                    <!-- TAB: INFORMACIÓN GENERAL -->
                    <div x-show="tab === 'general'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @include('admin.juegos.partials.formulario._general')
                    </div>

                    <!-- TAB: ASIGNACIÓN DE EQUIPOS -->
                    <div x-show="tab === 'equipos'" x-transition x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        @include('admin.juegos.partials.formulario._equipos')
                    </div>

                    <!-- TAB: ÁRBITROS Y MESA DE CONTROL -->
                    <div x-show="tab === 'arbitros'" x-transition x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        @include('admin.juegos.partials.formulario._arbitros')
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
                            x-text="editMode ? 'Actualizar Juego' : 'Registrar Juego'"
                        >
                        </button>
                    </div>
                </form>
            </div>

            <!-- Lista de juegos -->
            <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
                @include('admin.juegos.partials.lista.listado_juegos')
            </div>
            
        </div>

        <!-- Modal para ver detalles del juego -->
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
                
                <!-- Header del modal -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Detalles del Juego</h2>
                    <button @click.stop="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenido del modal -->
                <div class="p-6">
                    <template x-if="selectedJuego">
                        @include('admin.juegos.partials.modal.informacion')
                    </template>
                </div>

                <!-- Footer del modal -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex justify-end space-x-3">
                    <button @click="closeModal()" 
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium transition-colors">
                        Cerrar
                    </button>
                    <button @click="editJuego(selectedJuego); closeModal()" 
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition-colors">
                        Editar Juego
                    </button>
                </div>
            </div>
        </div>
    </main>
@endsection


