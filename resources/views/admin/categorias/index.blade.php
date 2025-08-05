@extends('layouts.app')

@section('content')

    <main class="relative z-10 py-8" x-data="{ 
        showForm: false, 
        editMode: false, 
        editingCategoria: null,
        formData: {
            nombre: '',
            liga_id: '',
            edad_minima: '',
            edad_maxima: '',
            activo: '1'
        },
        resetForm() {
            this.formData = {
                nombre: '',
                liga_id: '',
                edad_minima: '',
                edad_maxima: '',
                activo: '1'
            };
            this.editMode = false;
            this.editingCategoria = null;
        },
        editCategoria(categoria) {
            const ligas = {{ $ligas->toJson() }};
            const categoriaCompleta = {
                ...categoria,
                liga: categoria.liga || ligas.find(l => l.id == categoria.liga_id)
            };
            
            this.formData = {
                nombre: categoria.nombre,
                liga_id: categoria.liga_id.toString(),
                edad_minima: categoria.edad_minima.toString(),
                edad_maxima: categoria.edad_maxima.toString(),
                activo: categoria.activo.toString()
            };
            this.editMode = true;
            this.editingCategoria = categoriaCompleta;
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
                class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 mb-8"
            >
                <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
                    <h2 class="text-xl font-bold dark:text-primary-400 text-black-900" x-text="editMode ? 'Editar Categoría' : 'Registrar Nueva Categoría'"></h2>
                </div>

                <form :action="editMode ? `/categorias/${editingCategoria.id}` : '{{ route('categorias.store') }}'" method="POST" class="p-6">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Nombre de la categoría -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                Nombre de la Categoría <span class="text-red-400">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="nombre" 
                                name="nombre" 
                                x-model="formData.nombre"
                                required
                                maxlength="100"
                                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                placeholder="Ingrese el nombre de la categoría">
                        </div>

                        <!-- Selects de Ligas -->
                        @include('components.selects.select_ligas')

                        <!-- Grupo de edades y estado -->
                        <div class="md:col-span-2">
                            <div class="flex flex-wrap gap-4 items-start">
                                <!-- Grupo de edades (mitad de ancho) -->
                                <div class="flex-1 min-w-[200px]">
                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- Edad Mínima -->
                                        <div>
                                            <label for="edad_minima" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                                Edad Mínima <span class="text-red-400">*</span>
                                            </label>
                                            <input 
                                                type="number" 
                                                id="edad_minima" 
                                                name="edad_minima" 
                                                x-model="formData.edad_minima"
                                                required 
                                                min="0"
                                                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                                placeholder="Mín">
                                        </div>

                                        <!-- Edad Máxima -->
                                        <div>
                                            <label for="edad_maxima" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                                Edad Máxima <span class="text-red-400">*</span>
                                            </label>
                                            <input 
                                                type="number" 
                                                id="edad_maxima" 
                                                name="edad_maxima" 
                                                x-model="formData.edad_maxima"
                                                required 
                                                min="0"
                                                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                                placeholder="Máx">
                                        </div>
                                    </div>
                                </div>

                                <!-- Estado (ancho original) -->
                                <div class="flex-1 min-w-[200px]">
                                    <label for="activo" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                        Estado <span class="text-orange-500">*</span>
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
                            </div>
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
                            x-text="editMode ? 'Actualizar Categoría' : 'Registrar Categoría'"
                        >
                        </button>
                    </div>
                    
                </form>
            </div>


            <!-- Lista de categorías -->
            <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
                @include('admin.categorias.partials.lista.listado_categorias')
            </div>

        </div>
    </main>

@endsection

