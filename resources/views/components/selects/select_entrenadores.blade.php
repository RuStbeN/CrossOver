<!-- Select de Entrenador -->
<div>
    <label for="entrenador_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
        Entrenador <span class="text-red-400">*</span>
    </label>

    <div class="relative" x-data="{
        isOpen: false,
        searchTerm: '',
        maxVisible: 8,
        showAll: false,
        entrenadores: {{ $entrenadores->toJson() }},
        get filteredEntrenadores() {
            if (!this.searchTerm.trim()) {
                return this.showAll ? this.entrenadores : this.entrenadores.slice(0, this.maxVisible);
            }
            let filtered = this.entrenadores.filter(entrenador => 
                entrenador.nombre.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                (entrenador.email && entrenador.email.toLowerCase().includes(this.searchTerm.toLowerCase())) ||
                (entrenador.cedula_profesional && entrenador.cedula_profesional.toLowerCase().includes(this.searchTerm.toLowerCase()))
            );
            return filtered; // Mostrar todos los resultados filtrados
        },
        get selectedEntrenadorName() {
            let selected = this.entrenadores.find(entrenador => entrenador.id == formData.entrenador_id);
            return selected ? selected.nombre : '';
        },
        get hasMoreResults() {
            return !this.searchTerm.trim() && this.entrenadores.length > this.maxVisible && !this.showAll;
        },
        get placeholderText() {
            if (this.searchTerm) return this.searchTerm;
            if (formData.entrenador_id && this.selectedEntrenadorName) return this.selectedEntrenadorName;
            return 'Buscar o seleccionar entrenador...';
        },
        selectEntrenador(entrenadorId, entrenadorNombre) {
            formData.entrenador_id = entrenadorId.toString();
            this.searchTerm = '';
            this.isOpen = false;
            this.showAll = false;
        },
        openDropdown() {
            this.isOpen = true;
            this.showAll = false;
            // Focus en el input después de abrir
            this.$nextTick(() => {
                this.$refs.searchInput.focus();
            });
        },
        handleInput() {
            if (!this.isOpen) this.isOpen = true;
            this.showAll = false;
        },
        clearSelection() {
            formData.entrenador_id = '';
            this.searchTerm = '';
            this.isOpen = true;
            this.$nextTick(() => {
                this.$refs.searchInput.focus();
            });
        },
        handleKeydown(event) {
            if (event.key === 'Escape') {
                this.isOpen = false;
                this.searchTerm = '';
            }
        }
    }" @click.away="isOpen = false; searchTerm = ''">
        
        <!-- Input principal (combinado búsqueda + select) -->
        <div class="relative">
            <input 
                type="text"
                x-ref="searchInput"
                x-model="searchTerm"
                @input="handleInput()"
                @focus="openDropdown()"
                @keydown="handleKeydown($event)"
                :placeholder="placeholderText"
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md pl-3 pr-20 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                :class="{ 
                    'ring-2 ring-primary-500 border-transparent': isOpen,
                    'text-gray-900 dark:text-white font-medium': formData.entrenador_id && !searchTerm,
                    'text-gray-500 dark:text-gray-400': !formData.entrenador_id && !searchTerm,
                    'text-black dark:text-white': searchTerm || (!formData.entrenador_id && !searchTerm) || (formData.entrenador_id && !searchTerm)
                }"
            >
            
            <div class="absolute inset-y-0 right-0 flex items-center">
                <!-- Botón limpiar (solo si hay selección) -->
                <button 
                    x-show="formData.entrenador_id && !searchTerm"
                    @click="clearSelection()"
                    type="button"
                    class="mr-1 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                    title="Limpiar selección"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <!-- Flecha dropdown -->
                <button 
                    @click="isOpen ? (isOpen = false, searchTerm = '') : openDropdown()"
                    type="button"
                    class="mr-2 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                    title="Abrir opciones"
                >
                    <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Input hidden para el formulario -->
        <input type="hidden" name="entrenador_id" x-model="formData.entrenador_id" required>

        <!-- Dropdown -->
        <div 
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 w-full mt-1 bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md shadow-lg max-h-80"
        >
            <!-- Información útil para admin -->
            <template x-if="searchTerm && filteredEntrenadores.length > 0">
                <div class="px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-700 text-xs text-blue-700 dark:text-blue-300">
                    <span x-text="filteredEntrenadores.length"></span> entrenador<span x-text="filteredEntrenadores.length !== 1 ? 'es' : ''"></span> encontrado<span x-text="filteredEntrenadores.length !== 1 ? 's' : ''"></span>
                </div>
            </template>

            <!-- Lista de opciones -->
            <div class="max-h-64 overflow-y-auto">
                <!-- Opción "Sin selección" solo cuando no hay búsqueda -->
                <template x-if="!searchTerm.trim()">
                    <button
                        type="button"
                        @click="selectEntrenador('', '')"
                        class="w-full text-left px-3 py-2 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-dark-600 transition-colors italic"
                        :class="{ 'bg-gray-100 dark:bg-dark-600': !formData.entrenador_id }"
                    >
                        Sin selección
                    </button>
                </template>

                <!-- Entrenadores filtrados -->
                <template x-for="entrenador in filteredEntrenadores" :key="entrenador.id">
                    <button
                        type="button"
                        @click="selectEntrenador(entrenador.id, entrenador.nombre)"
                        class="w-full text-left px-3 py-2 text-sm text-black dark:text-white hover:bg-gray-100 dark:hover:bg-dark-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-dark-600 transition-colors"
                        :class="{ 
                            'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-2 border-primary-500': formData.entrenador_id == entrenador.id 
                        }"
                    >
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <div class="font-medium" x-text="entrenador.nombre"></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    <template x-if="entrenador.email">
                                        <span x-text="entrenador.email"></span>
                                    </template>
                                    <template x-if="entrenador.cedula_profesional">
                                        <span> • Cédula: <span x-text="entrenador.cedula_profesional"></span></span>
                                    </template>
                                    <template x-if="entrenador.edad">
                                        <span> • <span x-text="entrenador.edad"></span> años</span>
                                    </template>
                                    <template x-if="!entrenador.activo">
                                        <span class="text-red-500 font-medium"> • Inactivo</span>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Indicador de selección actual -->
                            <template x-if="formData.entrenador_id == entrenador.id">
                                <span class="text-primary-500 ml-2">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </template>
                        </div>
                    </button>
                </template>

                <!-- Mensaje cuando no hay resultados -->
                <template x-if="filteredEntrenadores.length === 0">
                    <div class="px-3 py-4 text-center">
                        <template x-if="searchTerm.trim()">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-6 w-6 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                No se encontraron entrenadores con "<strong x-text="searchTerm"></strong>"
                            </div>
                        </template>
                        <template x-if="!searchTerm.trim()">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                No hay entrenadores disponibles
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Botón "Ver todos" para administradores -->
                <template x-if="hasMoreResults">
                    <div class="border-t border-gray-200 dark:border-dark-600 bg-gray-50 dark:bg-dark-800">
                        <button
                            type="button"
                            @click="showAll = true"
                            class="w-full px-3 py-2 text-sm text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 focus:outline-none focus:bg-primary-50 dark:focus:bg-primary-900/20 transition-colors font-medium"
                        >
                            Ver todos los <span x-text="entrenadores.length"></span> entrenadores
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Ayuda contextual para admin -->
        <template x-if="isOpen && !searchTerm.trim() && entrenadores.length > maxVisible">
            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                💡 Escribe para buscar entre <span x-text="entrenadores.length"></span> entrenadores disponibles
            </div>
        </template>
    </div>
</div>