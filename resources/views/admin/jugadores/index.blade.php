@extends('layouts.app')

@section('content')

    <main class="relative z-10 py-8" x-data="{
        showForm: false, 
        editMode: false, 
        editingJugador: null,
        tab: 'personales',
        showModal: false,
        selectedJugador: null,
        formData: {
            // Datos personales
            nombre: '',
            fecha_nacimiento: '',
            rfc: '',
            edad: '',
            sexo: '',
            email: '',
            foto_url: null,
            telefono: '',
            direccion: '',
            
            // Datos deportivos (jugador)
            liga_id: '',
            categoria_id: '',
            estado_fisico: '',
            activo: '1',
            
            // Datos de relación equipo_jugadores
            equipo_id: '',
            posicion_principal: '',
            posicion_secundaria: '',
            numero_camiseta: '',
            fecha_ingreso: '',
            es_capitan: '1',
            temporada_id: '',
            torneo_id: '',
            
            // Contacto emergencia
            contacto_emergencia_nombre: '',
            contacto_emergencia_relacion: '',
            contacto_emergencia_telefono: ''
        },
        resetForm() {
            this.formData = {
                nombre: '',
                fecha_nacimiento: '',
                rfc: '',
                edad: '',
                sexo: '',
                email: '',
                foto_url: null,
                telefono: '',
                direccion: '',
                
                // Datos deportivos (jugador)
                liga_id: '',
                categoria_id: '',
                estado_fisico: '',
                activo: '1',
                
                // Datos de relación equipo_jugadores
                equipo_id: '',
                posicion_principal: '',
                posicion_secundaria: '',
                numero_camiseta: '',
                fecha_ingreso: '',
                es_capitan: '1',
                temporada_id: '',
                torneo_id: '',
                
                contacto_emergencia_nombre: '',
                contacto_emergencia_relacion: '',
                contacto_emergencia_telefono: ''
            };
            this.editMode = false;
            this.editingJugador = null;
            this.tab = 'personales';
        },
        openModal(jugador) {
            this.selectedJugador = jugador;
            this.showModal = true;
            document.body.style.overflow = 'hidden';
        },
        closeModal() {
            this.showModal = false;
            this.selectedJugador = null;
            document.body.style.overflow = 'auto';
        },
        editJugador(jugador) {
            console.log('Datos del jugador:', jugador);
            
            // Obtener la relación actual del jugador con el equipo
            let relacionEquipo = jugador.equipos_actual || {};
            
            this.formData = {
                // Datos personales
                nombre: jugador.nombre || '',
                fecha_nacimiento: jugador.fecha_nacimiento?.split('T')[0] || '',
                rfc: jugador.rfc || '',
                edad: jugador.edad || '',
                sexo: jugador.sexo || '',
                email: jugador.email || '',
                telefono: jugador.telefono || '',
                direccion: jugador.direccion || '',
                
                // Datos deportivos (jugador)
                liga_id: jugador.liga_id || '',
                categoria_id: jugador.categoria_id || (relacionEquipo.categoria_id || ''),
                estado_fisico: jugador.estado_fisico || '',
                activo: jugador.activo ? '1' : '0',
                
                // Datos de relación equipo_jugadores
                equipo_id: relacionEquipo.equipo_id || '',
                posicion_principal: relacionEquipo.posicion_principal || '',
                posicion_secundaria: relacionEquipo.posicion_secundaria || '',
                numero_camiseta: relacionEquipo.numero_camiseta || '',
                fecha_ingreso: relacionEquipo.fecha_ingreso?.split('T')[0] || '',
                es_capitan: jugador.equipos_actual?.es_capitan || false,
                temporada_id: relacionEquipo.temporada_id || '',
                torneo_id: relacionEquipo.torneo_id || '',
                
                // Contacto emergencia
                contacto_emergencia_nombre: jugador.contacto_emergencia_nombre || '',
                contacto_emergencia_relacion: jugador.contacto_emergencia_relacion || '',
                contacto_emergencia_telefono: jugador.contacto_emergencia_telefono || '',
                
                // Foto
                foto_url: null
            };
            
            this.editMode = true;
            this.editingJugador = jugador;
            this.showForm = true;
            this.tab = 'personales';
            
            // Cargar las categorías después de un pequeño delay para asegurar que el componente está renderizado
            this.$nextTick(() => {
                if (this.formData.liga_id) {
                    // Acceder al componente de categoría (modificado para Alpine v3)
                    const categoriaComponent = this.$refs.categoriaSelect;
                    if (categoriaComponent && typeof categoriaComponent.loadCategorias === 'function') {
                        categoriaComponent.loadCategorias().then(() => {
                            // Si la categoría no está en las cargadas, la añadimos manualmente
                            if (this.formData.categoria_id && !categoriaComponent.categorias.some(c => c.id == this.formData.categoria_id)) {
                                categoriaComponent.categorias.unshift({
                                    id: this.formData.categoria_id,
                                    nombre: this.formData.categoria_nombre,
                                    edad_minima: this.formData.categoria_edad_minima,
                                    edad_maxima: this.formData.categoria_edad_maxima
                                });
                            }
                        });
                    }
                }
            });

            // Mostrar preview de foto
            if (jugador.foto_url) {
                this.$nextTick(() => {
                    const fotoPreview = this.$refs.fotoPreview;
                    if (fotoPreview) {
                        fotoPreview.src = `/storage/${jugador.foto_url}`;
                    }
                });
            }
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
                    <h2 class="text-xl font-bold dark:text-primary-400 text-black-900" x-text="editMode ? 'Editar Jugador' : 'Registrar Nuevo Jugador'"></h2>
                </div>

                <form :action="editMode ? '/jugadores/' + editingJugador.id : '{{ route('jugadores.store') }}'" method="POST" class="p-6" enctype="multipart/form-data">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Tabs -->
                    <div class="mb-6 flex space-x-4">
                        <button type="button" @click="tab = 'personales'" :class="tab === 'personales' ? 'bg-primary-600 text-white' : 'bg-dark-600 text-gray-300'" class="px-4 py-2 rounded-md font-medium">Datos Personales</button>
                        <button type="button" @click="tab = 'deportivos'" :class="tab === 'deportivos' ? 'bg-primary-600 text-white' : 'bg-dark-600 text-gray-300'" class="px-4 py-2 rounded-md font-medium">Datos Deportivos</button>
                        <button type="button" @click="tab = 'emergencia'" :class="tab === 'emergencia' ? 'bg-primary-600 text-white' : 'bg-dark-600 text-gray-300'" class="px-4 py-2 rounded-md font-medium">Contacto Emergencia</button>
                    </div>

                    <!-- TAB: PERSONALES -->
                    <div x-show="tab === 'personales'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @include('admin.jugadores.partials.formulario._personales')
                    </div>

                    <!-- TAB: DEPORTIVOS -->
                    <div x-show="tab === 'deportivos'" x-transition x-cloak class="grid grid-cols-1 md:grid-cols-1 gap-6 mt-6">
                        @include('admin.jugadores.partials.formulario._deportivos')
                    </div>

                    <!-- TAB: CONTACTO EMERGENCIA -->
                    <div x-show="tab === 'emergencia'" x-transition x-cloak class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        @include('admin.jugadores.partials.formulario._emergencia')
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
                            x-text="editMode ? 'Actualizar Jugador' : 'Registrar Jugador'"
                        >
                        </button>
                    </div>
                </form>
            </div>


            <!-- Lista de jugadores -->
            <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
                @include('admin.jugadores.partials.lista.listado_jugadores')
            </div>
            
        </div>

        <!-- Modal para ver detalles del jugador -->
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" 
             @click.self="closeModal()">
            
            <div x-show="showModal" 
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 transform scale-95" 
                 x-transition:enter-end="opacity-100 transform scale-100" 
                 x-transition:leave="transition ease-in duration-200" 
                 x-transition:leave-start="opacity-100 transform scale-100" 
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="bg-white dark:bg-dark-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                
                <!-- Header del modal -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Detalles del Jugador</h2>
                    <button @click="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenido del modal -->
                <div class="p-6">
                    <template x-if="selectedJugador">
                        @include('admin.jugadores.partials.modal.informacion')
                    </template>
                </div>

                <!-- Footer del modal -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex justify-end space-x-3">
                    <button @click="closeModal()" 
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium transition-colors">
                        Cerrar
                    </button>
                    <button @click="editJugador(selectedJugador); closeModal()" 
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition-colors">
                        Editar Jugador
                    </button>
                </div>
            </div>
        </div>
        
    </main>
@endsection


{{-- Ya no necesitas @push('scripts') para notificaciones básicas --}}

{{-- Si quieres configuración específica para la calculadora --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar con configuración personalizada si es necesario
        if (document.querySelector('#fecha_nacimiento')) {
            window.App.initComponent('calculadora-edad', {
                minAge: 18, // Edad mínima personalizada
                maxAge: 65,  // Edad máxima personalizada
                mostrarMensajesConsola: false // Para ocultar mensajes de consola
            });
        }
    });
</script>
@endpush