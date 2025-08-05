@extends('layouts.app')

@section('content')

    <main class="relative z-10 py-8" x-data="{ 
        showForm: false, 
        editMode: false, 
        editingLiga: null,
        formData: {
            nombre: '',
            descripcion: '',
            activo: '1'
        },
        resetForm() {
            this.formData = {
                nombre: '',
                descripcion: '',
                activo: '1'
            };
            this.editMode = false;
            this.editingLiga = null;
        },
        editLiga(liga) {
            this.formData = {
                nombre: liga.nombre,
                descripcion: liga.descripcion || '',
                activo: liga.activo ? '1' : '0'
            };
            this.editMode = true;
            this.editingLiga = liga;
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
                    <h2 class="text-xl font-bold dark:text-primary-400 text-black-900" x-text="editMode ? 'Editar Liga' : 'Registrar Nueva Liga'"></h2>
                </div>

                <form :action="editMode ? `/ligas/${editingLiga.id}` : '{{ route('ligas.store') }}'" method="POST" class="p-6">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre de la Liga -->
                        <div class="col-span-1 flex flex-col">
                            <label for="nombre" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                Nombre de la Liga <span class="text-red-400">*</span>
                            </label>
                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                x-model="formData.nombre"
                                required
                                maxlength="100"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="Ingresa el nombre de la liga"
                            >
                        </div>

                        <!-- Estado Activo -->
                        <div class="col-span-1 flex flex-col">
                            <label for="activo" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                <span>Estado</span> <span class="text-orange-500">*</span>
                            </label>
                            <select
                                id="activo"
                                name="activo"
                                x-model="formData.activo"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="1">Activa</option>
                                <option value="0">Inactiva</option>
                            </select>
                        </div>

                        <!-- Descripción -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="descripcion" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                Descripción
                            </label>
                            <textarea
                                id="descripcion"
                                name="descripcion"
                                x-model="formData.descripcion"
                                rows="4"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="Descripción de la liga (opcional)"
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
                        <button type="submit"
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 rounded-md text-white font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-dark-800"
                            x-text="editMode ? 'Actualizar Liga' : 'Registrar Liga'"
                        >
                        </button>
                    </div>
                </form>
            </div>


            <!-- Lista de ligas -->
            <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
                @include('admin.ligas.partials.lista.listado_ligas')
            </div>
            
        </div>
    </main>

@endsection


