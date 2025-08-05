@extends('layouts.app')

@section('content')

    <!-- Contenido principal -->
    <main class="relative z-10 py-8" x-data="{ 
        showForm: false, 
        editMode: false, 
        editingEquipo: null,
        showEquipoModal: false,
        selectedEquipo: null,
        activeEquipoTab: 'detalles',
        formData: {
            nombre: '',
            categoria_id: '',
            liga_id: '',
            entrenador_id: '',
            logo_url: null,
            fecha_fundacion: '',
            color_primario: '#ff6b35',
            color_secundario: '#e65020',
            activo: '1'
        },
        resetForm() {
            this.formData = {
                nombre: '',
                categoria_id: '',
                liga_id: '',
                entrenador_id: '',
                logo_url: null,
                fecha_fundacion: '',
                color_primario: '#ff6b35',
                color_secundario: '#e65020',
                activo: '1'
            };
            this.editMode = false;
            this.editingEquipo = null;
        },
        openEquipoModal(equipo) {
            this.selectedEquipo = equipo;
            this.activeEquipoTab = 'detalles';
            this.showEquipoModal = true;
        },
        closeEquipoModal() {
            this.showEquipoModal = false;
            this.selectedEquipo = null;
        },
        editEquipo(equipo) {
            this.formData = {
                nombre: equipo.nombre,
                categoria_id: equipo.categoria_id,
                liga_id: equipo.liga_id,
                entrenador_id: equipo.entrenador_id || '',
                logo_url: null,
                fecha_fundacion: equipo.fecha_fundacion?.split('T')[0] || '',
                color_primario: equipo.color_primario || '#ff6b35',
                color_secundario: equipo.color_secundario || '#e65020',
                activo: equipo.activo ? '1' : '0'
            };
            this.editMode = true;
            this.editingEquipo = equipo;
            this.showForm = true;

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
            if (equipo.logo_url) {
                this.$nextTick(() => {
                    const fotoPreview = this.$refs.fotoPreview;
                    if (fotoPreview) {
                        fotoPreview.src = `/storage/${equipo.logo_url}`;
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
                        <h2 class="text-xl font-bold dark:text-primary-400 text-black-900" x-text="editMode ? 'Editar Equipo' : 'Registrar Nuevo Equipo'"></h2>
                    </div>

                    <form :action="editMode ? `/equipos/${editingEquipo.id}` : '{{ route('equipos.store') }}'" method="POST" class="p-6" enctype="multipart/form-data">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Nombre del equipo -->
                            <div>
                                <label for="nombre" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Nombre del Equipo <span class="text-red-400">*</span></label>
                                <input type="text" id="nombre" name="nombre" x-model="formData.nombre" required
                                    class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                    placeholder="Ingrese el nombre del equipo">
                                @error('nombre')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Entrenador -->
                            @include('components.selects.select_entrenadores')

                            <!-- Selects de Liga y Categoría (usando el componente creado) -->
                            @include('components.selects.select_ligas_categorias')


                            <!-- Fecha de Fundación -->
                            <div x-data="{
                                abrirCalendario(el) {
                                    // Forzar la apertura del datepicker
                                    el.showPicker();
                                }
                            }">
                                <label for="fecha_fundacion" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Fecha de Fundación</label>
                                <input
                                    type="date"
                                    id="fecha_fundacion"
                                    name="fecha_fundacion"
                                    x-model="formData.fecha_fundacion"
                                    @click="abrirCalendario($el)"
                                    class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition cursor-pointer">
                            </div>

                            <!-- Colores (en línea) -->
                            <div class="flex gap-4 mb-4">
                                <!-- Color Primario -->
                                <div class="flex-1">
                                    <label for="color_primario" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Color Primario <span class="text-red-400">*</span></label>
                                    <input type="color" id="color_primario" name="color_primario" x-model="formData.color_primario"
                                        class="w-full h-10 bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                    @error('color_primario')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Color Secundario -->
                                <div class="flex-1">
                                    <label for="color_secundario" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Color Secundario <span class="text-red-400">*</span></label>
                                    <input type="color" id="color_secundario" name="color_secundario" x-model="formData.color_secundario"
                                        class="w-full h-10 bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                    @error('color_secundario')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Estado del Equipo (en su propia línea) -->
                            <div>
                                <label for="activo" class="block text-sm font-medium text-gray-900 dark:text-gray-300 mb-1 cursor-pointer select-none">
                                    Estado del Equipo <span class="text-red-400">*</span>
                                </label>
                                <select
                                    id="activo"
                                    name="activo"
                                    x-model="formData.activo"
                                    class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                >
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                                @error('activo')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Logo -->
                            <div>
                                <label for="logo_url" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                                    Logo del Equipo
                                </label>
                                <input type="file" id="logo_url" name="logo_url" accept="image/jpeg,image/png,image/jpg,image"
                                    class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                
                                <!-- Información de requisitos -->
                                <div class="mt-2 p-3 bg-gray-50 dark:bg-dark-600 rounded-md border border-gray-200 dark:border-dark-500">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-xs text-gray-600 dark:text-gray-400">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                            </svg>
                                            <span><strong>Formatos:</strong> JPEG, PNG, JPG</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12z" clip-rule="evenodd"/>
                                            </svg>
                                            <span><strong>Dimensiones:</strong> 200x200px recomendado</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            <span><strong>Tamaño máximo:</strong> 2MB</span>
                                        </div>
                                    </div>
                                </div>
                                
                                @error('logo_url')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                                
                                <template x-if="editMode && editingEquipo.logo_url">
                                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-200 dark:border-blue-800">
                                        <span class="text-sm text-blue-700 dark:text-blue-300 font-medium">Logo actual:</span>
                                        <div class="mt-2">
                                            <img :src="`/storage/${editingEquipo.logo_url}`" 
                                                :alt="`Logo ${editingEquipo.nombre}`"
                                                class="h-24 w-24 object-cover rounded-lg border-2 border-blue-200 dark:border-blue-700 shadow-sm">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

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
                                x-text="editMode ? 'Actualizar Equipo' : 'Registrar Equipo'"
                            >
                            </button>
                        </div>
                    </form> 
                </div>


                <!-- Lista de equipos -->
                <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
                    @include('admin.equipos.partials.lista.listado_equipos')
                </div>

            </div>


            <!-- Modal para ver detalles del equipo -->
            <div x-show="showEquipoModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
                @click.self="closeEquipoModal()">

                <div x-show="showEquipoModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="bg-white dark:bg-dark-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">

                    <!-- Header del modal -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Detalles del Equipo</h2>
                        <button @click="closeEquipoModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Pestañas del modal -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700">
                        <ul class="flex space-x-6">
                            <li>
                                <button 
                                    @click="activeEquipoTab = 'detalles'" 
                                    :class="{ 'text-blue-500 border-blue-500': activeEquipoTab === 'detalles' }" 
                                    class="text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-blue-500 hover:border-blue-500 transition-colors duration-300"
                                >
                                    Detalles
                                </button>
                            </li>
                            <li>
                                <button 
                                    @click="activeEquipoTab = 'jugadores'" 
                                    :class="{ 'text-blue-500 border-blue-500': activeEquipoTab === 'jugadores' }" 
                                    class="text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-blue-500 hover:border-blue-500 transition-colors duration-300"
                                >
                                    Jugadores
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Contenido del modal -->
                    <div class="p-6">
                        <template x-if="selectedEquipo">
                            <div class="space-y-6">
                                <!-- Pestaña de detalles -->
                                <div x-show="activeEquipoTab === 'detalles'" class="space-y-6">
                                    @include('admin.equipos.partials.modal.informacion')
                                </div>
                                
                                <!-- Pestaña de jugadores -->
                                <div x-show="activeEquipoTab === 'jugadores'" class="space-y-6">
                                    @include('admin.equipos.partials.modal.jugadores')
                                </div>

                            </div>
                        </template>
                    </div>

                    <!-- Footer del modal -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex justify-end space-x-3">
                        <button @click="closeEquipoModal()" 
                                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium transition-colors">
                            Cerrar
                        </button>
                        <button @click="editEquipo(selectedEquipo); closeEquipoModal()" 
                                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition-colors">
                            Editar Equipo
                        </button>
                    </div>
                </div>
            </div>
        </main>

@endsection

