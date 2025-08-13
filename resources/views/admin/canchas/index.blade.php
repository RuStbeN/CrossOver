@extends('layouts.app')

@section('content')

    <main class="relative z-10 py-8" x-data="{ 
        showForm: false, 
        editMode: false, 
        editingCancha: null,
        formData: {
            nombre: '',
            direccion: '',
            capacidad: '',
            tipo_superficie: '',
            techada: '',
            iluminacion: '',
            equipamiento: '',
            tarifa_por_hora: '',
            activo: '1'
        },
        resetForm() {
            this.formData = {
                nombre: '',
                direccion: '',
                capacidad: '',
                tipo_superficie: '',
                techada: '',
                iluminacion: '',
                equipamiento: '',
                tarifa_por_hora: '',
                activo: '1'
            };
            this.editMode = false;
            this.editingCancha = null;
        },
        editCancha(cancha) {
            this.formData = {
                nombre: cancha.nombre,
                direccion: cancha.direccion,
                capacidad: cancha.capacidad,
                tipo_superficie: cancha.tipo_superficie,
                techada: cancha.techada ? '1' : '0',
                iluminacion: cancha.iluminacion ? '1' : '0',
                equipamiento: cancha.equipamiento,
                tarifa_por_hora: cancha.tarifa_por_hora,
                activo: cancha.activo ? '1' : '0'
            };
            this.editMode = true;
            this.editingCancha = cancha;
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
                <h2 class="text-xl font-bold dark:text-primary-400 text-black-900" x-text="editMode ? 'Editar Cancha' : 'Registrar Nueva Cancha'"></h2>
            </div>

            <form :action="editMode ? `/canchas/${editingCancha.id}` : '{{ route('canchas.store') }}'" method="POST" class="p-6">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre de la Cancha -->
                    <div class="col-span-1">
                        <label for="nombre" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Nombre <span class="text-red-400">*</span>
                        </label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            x-model="formData.nombre"
                            required
                            maxlength="100"
                            class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Nombre de la cancha"
                        >
                    </div>

                    <!-- Dirección -->
                    <div class="col-span-1">
                        <label for="direccion" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Dirección
                        </label>
                        <input
                            type="text"
                            id="direccion"
                            name="direccion"
                            x-model="formData.direccion"
                            class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Dirección de la cancha"
                        >
                    </div>

                    <!-- Capacidad y Tipo de Superficie (Primera fila de 2 campos) -->
                    <div class="col-span-1 flex gap-6">
                        <!-- Capacidad -->
                        <div class="flex-1">
                            <label for="capacidad" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                Capacidad
                            </label>
                            <input
                                type="number"
                                id="capacidad"
                                name="capacidad"
                                x-model="formData.capacidad"
                                min="0"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                placeholder="Número de espectadores"
                            >
                        </div>

                        <!-- Tipo de Superficie -->
                        <div class="flex-1">
                            <label for="tipo_superficie" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                Tipo de Superficie <span class="text-red-400">*</span>
                            </label>
                            <select
                                id="tipo_superficie"
                                name="tipo_superficie"
                                x-model="formData.tipo_superficie"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="Sintética">Sintética</option>
                                <option value="Natural">Natural</option>
                                <option value="Cemento">Cemento</option>
                                <option value="Parquet">Parquet</option>
                                <option value="Duela">Duela</option>
                                <option value="Otros">Otros</option>
                            </select>
                        </div>
                    </div>

                    <!-- Techada e Iluminación (Segunda fila de 2 campos) -->
                    <div class="col-span-1 flex gap-6">
                        <!-- Techada -->
                        <div class="flex-1">
                            <label for="techada" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                Techada <span class="text-red-400">*</span>
                            </label>
                            <select
                                id="techada"
                                name="techada"
                                x-model="formData.techada"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <!-- Iluminación -->
                        <div class="flex-1">
                            <label for="iluminacion" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                Con iluminación <span class="text-red-400">*</span>
                            </label>
                            <select
                                id="iluminacion"
                                name="iluminacion"
                                x-model="formData.iluminacion"
                                class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            >
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tarifa por hora -->
                    <div class="col-span-1">
                        <label for="tarifa_por_hora" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Tarifa por hora
                        </label>
                        <input
                            type="number"
                            id="tarifa_por_hora"
                            name="tarifa_por_hora"
                            x-model="formData.tarifa_por_hora"
                            min="0"
                            step="0.01"
                            class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Precio por hora de uso"
                        >
                    </div>

                    <!-- Estado Activo -->
                    <div class="col-span-1">
                        <label for="activo" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Estado <span class="text-orange-500 text-lg font-semibold">*</span>
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

                    <!-- Equipamiento -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="equipamiento" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Equipamiento
                        </label>
                        <textarea
                            id="equipamiento"
                            name="equipamiento"
                            x-model="formData.equipamiento"
                            rows="3"
                            class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Descripción del equipamiento disponible (vestuarios, baños, etc.)"
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
                        x-text="editMode ? 'Actualizar Cancha' : 'Registrar Cancha'"
                    >
                    </button>
                </div>
            </form>
            
        </div>


        <!-- Lista de canchas -->
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
            @include('admin.canchas.partials.lista.listado_canchas')
        </div>

    </div>
    </main>

@endsection


