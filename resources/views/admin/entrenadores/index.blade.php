@extends('layouts.app')

@section('content')

    <!-- Contenido principal -->
    <main class="relative z-10 py-8" x-data="{ 
        showForm: false, 
        editMode: false, 
        editingEntrenador: null,
        showEntrenadorModal: false,
        selectedEntrenador: null,
        formData: {
            nombre: '',
            telefono: '',
            email: '',
            cedula_profesional: '',
            fecha_nacimiento: '',
            experiencia: '',
            activo: true
        },
        resetForm() {
            this.formData = {
                nombre: '',
                telefono: '',
                email: '',
                cedula_profesional: '',
                fecha_nacimiento: '',
                experiencia: '',
                activo: true
            };
            this.editMode = false;
            this.editingEntrenador = null;
        },
         openEntrenadorModal(entrenador) {
            this.selectedEntrenador = entrenador;
            this.showEntrenadorModal = true;
        },
        
        closeEntrenadorModal() {
            this.showEntrenadorModal = false;
            this.selectedEntrenador = null;
        },
        editEntrenador(entrenador) {
            this.formData = {
                nombre: entrenador.nombre,
                telefono: entrenador.telefono,
                email: entrenador.email,
                cedula_profesional: entrenador.cedula_profesional,
                fecha_nacimiento: entrenador.fecha_nacimiento ? entrenador.fecha_nacimiento.split('T')[0] : '',
                experiencia: entrenador.experiencia,
                activo: Boolean(entrenador.activo)
            };
            this.editMode = true;
            this.editingEntrenador = entrenador;
            this.showForm = true;
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
                    <h2 class="text-xl font-bold dark:text-primary-400 text-black-900" x-text="editMode ? 'Editar Entrenador' : 'Registrar Nuevo Entrenador'"></h2>
                </div>

                <form :action="editMode ? `/entrenadores/${editingEntrenador.id}` : '{{ route('entrenadores.store') }}'" method="POST" class="p-6">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div class="flex flex-col">
                            <label for="nombre" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800 h-6">
                                Nombre <span class="text-red-400">*</span>
                            </label>
                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                x-model="formData.nombre"
                                required
                                maxlength="100"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent h-10"
                                placeholder="Nombre completo del entrenador">
                        </div>
                        <!-- Teléfono -->
                        <div class="flex flex-col">
                            <label for="telefono" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800 h-6">
                                Teléfono <span class="text-orange-500 text-lg font-semibold">*</span>
                            </label>
                            <input
                                type="text"
                                id="telefono"
                                name="telefono"
                                x-model="formData.telefono"
                                maxlength="20"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent h-10"
                                placeholder="Número de teléfono">
                        </div>
                        <!-- Email -->
                        <div class="flex flex-col">
                            <label for="email" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800 h-6">
                                Email
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                x-model="formData.email"
                                maxlength="100"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent h-10"
                                placeholder="Correo electrónico">
                        </div>
                        <!-- Cédula Profesional -->
                        <div class="flex flex-col">
                            <label for="cedula_profesional" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800 h-6">
                                Cédula Profesional <span class="text-orange-500 text-lg font-semibold">*</span>
                            </label>
                            <input
                                type="text"
                                id="cedula_profesional"
                                name="cedula_profesional"
                                x-model="formData.cedula_profesional"
                                maxlength="50"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent h-10"
                                placeholder="Número de cédula profesional">
                        </div>
                        <!-- Fecha de Nacimiento -->
                        <div class="flex flex-col" x-data="{
                            abrirCalendario(el) {
                                // Forzar la apertura del datepicker
                                el.showPicker();
                            }
                        }">
                            <label for="fecha_nacimiento" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800 h-6">
                                Fecha de Nacimiento <span class="text-orange-500 text-lg font-semibold">*</span>
                            </label>
                            <input
                                type="date"
                                id="fecha_nacimiento"
                                name="fecha_nacimiento"
                                x-model="formData.fecha_nacimiento"
                                @click="abrirCalendario($el)"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent h-10 cursor-pointer">
                        </div>
                        <!-- Estado Activo -->
                        <div class="flex flex-col">
                            <label for="activo" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800 h-6">
                                Estado <span class="text-orange-500 text-lg font-semibold">*</span>
                            </label>
                            <select
                                id="activo"
                                name="activo"
                                x-model="formData.activo"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent h-10"
                            >
                                <option :value="true">Activo</option>
                                <option :value="false">Inactivo</option>
                            </select>
                        </div>
                        <!-- Experiencia -->
                        <div class="md:col-span-2 flex flex-col">
                            <label for="experiencia" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800 h-6">
                                Experiencia
                            </label>
                            <textarea
                                id="experiencia"
                                name="experiencia"
                                x-model="formData.experiencia"
                                rows="3"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="Años de experiencia, especialidades, etc."
                            ></textarea>
                        </div>
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
                        <button
                            type="submit"
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 rounded-md text-white font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-dark-800"
                            x-text="editMode ? 'Actualizar Entrenador' : 'Registrar Entrenador'"
                        >
                        </button>
                    </div>
                </form>


            </div>


            <!-- Lista de entrenadores -->
            <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
                @include('admin.entrenadores.partials.lista.listado_entrenadores')
            </div>

        </div>

        <!-- Modal para ver detalles del entrenador -->
        <div x-show="showEntrenadorModal" 
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="transition ease-in duration-200" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" 
            @click.self="closeEntrenadorModal()">
            
            <div x-show="showEntrenadorModal" 
                x-transition:enter="transition ease-out duration-300" 
                x-transition:enter-start="opacity-0 transform scale-95" 
                x-transition:enter-end="opacity-100 transform scale-100" 
                x-transition:leave="transition ease-in duration-200" 
                x-transition:leave-start="opacity-100 transform scale-100" 
                x-transition:leave-end="opacity-0 transform scale-95"
                class="bg-white dark:bg-dark-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                
                <!-- Header del modal -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Detalles del Entrenador</h2>
                    <button @click="closeEntrenadorModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Contenido del modal -->
                <div class="p-6">
                    <template x-if="selectedEntrenador">
                         @include('admin.entrenadores.partials.modal.informacion')
                    </template>
                </div>

                <!-- Footer del modal -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex justify-end space-x-3">
                    <button @click="closeEntrenadorModal()" 
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium transition-colors">
                        Cerrar
                    </button>
                    <button @click="editEntrenador(selectedEntrenador); closeEntrenadorModal()" 
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition-colors">
                        Editar Entrenador
                    </button>
                </div>
            </div>
        </div>
        
    </main>

@endsection

