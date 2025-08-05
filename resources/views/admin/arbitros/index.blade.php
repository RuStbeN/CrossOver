@extends('layouts.app')

@section('content')

    <main class="relative z-10 py-8" x-data="{ 
        showForm: false, 
        editMode: false, 
        editingArbitro: null,
        formData: {
            nombre: '',
            edad: '',
            direccion: '',
            telefono: '',
            correo: '',
            activo: '1'
        },
        resetForm() {
            this.formData = {
                nombre: '',
                edad: '',
                direccion: '',
                telefono: '',
                correo: '',
                activo: '1'
            };
            this.editMode = false;
            this.editingArbitro = null;
        },
        editArbitro(arbitro) {
            this.formData = {
                nombre: arbitro.nombre,
                edad: arbitro.edad || '',
                direccion: arbitro.direccion || '',
                telefono: arbitro.telefono || '',
                correo: arbitro.correo || '',
                activo: arbitro.activo ? '1' : '0'
            };
            this.editMode = true;
            this.editingArbitro = arbitro;
            this.showForm = true;
        },
        cancelEdit() {
            this.resetForm();
            this.showForm = false;
        }
    }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Notificación de Contraseña Temporal -->
        @if(session('password_temporal'))
            <div 
                x-data="{ show: true }" 
                x-show="show" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-90"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-90"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
            >
                <div class="bg-white dark:bg-dark-800 rounded-lg shadow-2xl border border-dark-100 dark:border-dark-700 max-w-md w-full mx-4 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700 bg-green-50 dark:bg-green-900/20">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-green-800 dark:text-green-300">
                                    ¡Árbitro Registrado Exitosamente!
                                </h3>
                                <p class="text-sm text-green-700 dark:text-green-400 mt-1">
                                    {{ session('arbitro_nombre') }} ha sido registrado como árbitro
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.96-.833-2.73 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                                        Contraseña Temporal
                                    </h4>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-1">
                                        Esta es la contraseña temporal para el árbitro. Será visible solo por esta ocasión.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-dark-700 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña:</p>
                                    <p class="text-lg font-mono font-bold text-gray-900 dark:text-white mt-1" id="password-text">
                                        {{ session('password_temporal') }}
                                    </p>
                                </div>
                                <button 
                                    type="button"
                                    onclick="copyPassword()"
                                    class="ml-4 px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                >
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    Copiar
                                </button>
                            </div>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                        Instrucciones importantes:
                                    </h4>
                                    <ul class="text-sm text-blue-700 dark:text-blue-400 mt-1 space-y-1">
                                        <li>• El árbitro debe iniciar sesión con su correo y esta contraseña temporal</li>
                                        <li>• Al primer ingreso, deberá cambiar la contraseña obligatoriamente</li>
                                        <li>• Solo podrá ver los partidos donde esté asignado</li>
                                        <li>• Guarda esta contraseña en un lugar seguro</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-dark-900 border-t border-gray-200 dark:border-dark-700">
                        <div class="flex justify-end">
                            <button 
                                type="button"
                                @click="show = false"
                                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                            >
                                Entendido
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formulario de creación/edición -->
        <div
            x-show="showForm"
            x-transition
            class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 mb-8 overflow-hidden"
        >
            <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900 flex items-center justify-between">
                <h2 class="text-xl font-bold dark:text-primary-400 text-black-900" x-text="editMode ? 'Editar Árbitro' : 'Registrar Nuevo Árbitro'"></h2>
            </div>

            <form :action="editMode ? `/arbitros/${editingArbitro.id}` : '{{ route('arbitros.store') }}'" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre del Árbitro -->
                    <div class="col-span-1">
                        <label for="nombre" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Nombre del Árbitro <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            x-model="formData.nombre"
                            required
                            maxlength="150"
                            class="w-full h-10 bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Ingresa el nombre completo"
                        >
                    </div>

                    <!-- Edad -->
                    <div class="col-span-1">
                        <label for="edad" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Edad <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="edad"
                            name="edad"
                            x-model="formData.edad"
                            required
                            min="18"
                            max="100"
                            class="w-full h-10 bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Edad del árbitro"
                        >
                    </div>

                    <!-- Teléfono -->
                    <div class="col-span-1">
                        <label for="telefono" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Teléfono
                        </label>
                        <input
                            type="tel"
                            id="telefono"
                            name="telefono"
                            x-model="formData.telefono"
                            maxlength="20"
                            class="w-full h-10 bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Número de teléfono"
                        >
                    </div>

                    <!-- Correo -->
                    <div class="col-span-1">
                        <label for="correo" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Correo Electrónico <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            x-model="formData.correo"
                            required
                            maxlength="100"
                            class="w-full h-10 bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="correo@ejemplo.com"
                        >
                    </div>

                    <!-- Foto del Árbitro -->
                    <div class="col-span-1">
                        <label for="foto" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Foto del Árbitro
                        </label>
                        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/jpg,image"
                            class="w-full h-10 bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                        
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
                                    <span><strong>Dimensiones:</strong> 400x600px recomendado</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <span><strong>Tamaño máximo:</strong> 2MB</span>
                                </div>
                            </div>
                        </div>
                        
                        <template x-if="editMode && editingArbitro.foto">
                            <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-200 dark:border-blue-800">
                                <span class="text-sm text-blue-700 dark:text-blue-300 font-medium">Foto actual:</span>
                                <div class="mt-2">
                                    <img :src="'/storage/' + editingArbitro.foto" 
                                        class="h-24 w-24 object-cover rounded-lg border-2 border-blue-200 dark:border-blue-700 shadow-sm">
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Estado Activo -->
                    <div class="col-span-1">
                        <label for="activo" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="activo"
                            name="activo"
                            x-model="formData.activo"
                            required
                            class="w-full h-10 bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                    <!-- Dirección -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="direccion" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
                            Dirección
                        </label>
                        <textarea
                            id="direccion"
                            name="direccion"
                            x-model="formData.direccion"
                            rows="3"
                            maxlength="255"
                            class="w-full min-h-[7.5rem] bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Dirección del árbitro (opcional)"
                        ></textarea>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="mt-6 flex justify-end space-x-3">
                    <button 
                        type="button"
                        @click="cancelEdit()"
                        class="px-4 py-2 h-10 bg-gray-600 hover:bg-gray-700 rounded-md text-white font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-dark-800"
                    >
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 h-10 bg-primary-600 hover:bg-primary-700 rounded-md text-white font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-dark-800"
                        x-text="editMode ? 'Actualizar Árbitro' : 'Registrar Árbitro'"
                    >
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de árbitros -->
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
            @include('admin.arbitros.partials.lista.listado_arbitros')
        </div>
    </div>
    </main>

@endsection

