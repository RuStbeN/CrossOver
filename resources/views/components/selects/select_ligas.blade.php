<!-- Select de Liga -->
<div>
    <label for="liga_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
        Liga <span class="text-red-400">*</span>
    </label>

    <div class="relative" x-data="{
        isOpen: false,
        searchTerm: '',
        maxVisible: 8,
        showAll: false,
        ligas: {{ $ligas->toJson() }},
        get filteredLigas() {
            if (!this.searchTerm.trim()) {
                return this.showAll ? this.ligas : this.ligas.slice(0, this.maxVisible);
            }
            let filtered = this.ligas.filter(liga => 
                liga.nombre.toLowerCase().includes(this.searchTerm.toLowerCase())
            );
            return filtered; // Mostrar todos los resultados filtrados
        },
        get selectedLigaName() {
            let selected = this.ligas.find(liga => liga.id == formData.liga_id);
            return selected ? selected.nombre : '';
        },
        get hasMoreResults() {
            return !this.searchTerm.trim() && this.ligas.length > this.maxVisible && !this.showAll;
        },
        get placeholderText() {
            if (this.searchTerm) return this.searchTerm;
            if (formData.liga_id && this.selectedLigaName) return this.selectedLigaName;
            return 'Buscar o seleccionar liga...';
        },
        selectLiga(ligaId, ligaNombre) {
            formData.liga_id = ligaId.toString();
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
            formData.liga_id = '';
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
                    x-show="formData.liga_id && !searchTerm"
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
        <input type="hidden" name="liga_id" x-model="formData.liga_id" required>

        <!-- Dropdown -->
        <div 
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 w-full mt-1 bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md shadow-lg max-h-64"
        >
            <!-- Información útil para admin -->
            <template x-if="searchTerm && filteredLigas.length > 0">
                <div class="px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-700 text-xs text-blue-700 dark:text-blue-300">
                    <span x-text="filteredLigas.length"></span> liga<span x-text="filteredLigas.length !== 1 ? 's' : ''"></span> encontrada<span x-text="filteredLigas.length !== 1 ? 's' : ''"></span>
                </div>
            </template>

            <!-- Lista de opciones -->
            <div class="max-h-52 overflow-y-auto">
                <!-- Opción "Sin selección" solo cuando no hay búsqueda -->
                <template x-if="!searchTerm.trim()">
                    <button
                        type="button"
                        @click="selectLiga('', '')"
                        class="w-full text-left px-3 py-2 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-dark-600 transition-colors italic"
                        :class="{ 'bg-gray-100 dark:bg-dark-600': !formData.liga_id }"
                    >
                        Sin selección
                    </button>
                </template>

                <!-- Ligas filtradas -->
                <template x-for="liga in filteredLigas" :key="liga.id">
                    <button
                        type="button"
                        @click="selectLiga(liga.id, liga.nombre)"
                        class="w-full text-left px-3 py-2 text-sm text-black dark:text-white hover:bg-gray-100 dark:hover:bg-dark-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-dark-600 transition-colors"
                        :class="{ 
                            'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-2 border-primary-500': formData.liga_id == liga.id 
                        }"
                    >
                        <span x-text="liga.nombre"></span>
                        <!-- Indicador de selección actual -->
                        <template x-if="formData.liga_id == liga.id">
                            <span class="float-right text-primary-500">
                                <svg class="h-4 w-4 inline" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                        </template>
                    </button>
                </template>

                <!-- Mensaje cuando no hay resultados -->
                <template x-if="filteredLigas.length === 0">
                    <div class="px-3 py-4 text-center">
                        <template x-if="searchTerm.trim()">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-6 w-6 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                No se encontraron ligas con "<strong x-text="searchTerm"></strong>"
                            </div>
                        </template>
                        <template x-if="!searchTerm.trim()">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                No hay ligas disponibles
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Botón "Ver todas" para administradores -->
                <template x-if="hasMoreResults">
                    <div class="border-t border-gray-200 dark:border-dark-600 bg-gray-50 dark:bg-dark-800">
                        <button
                            type="button"
                            @click="showAll = true"
                            class="w-full px-3 py-2 text-sm text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 focus:outline-none focus:bg-primary-50 dark:focus:bg-primary-900/20 transition-colors font-medium"
                        >
                            Ver todas las <span x-text="ligas.length"></span> ligas
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Ayuda contextual para admin -->
        <template x-if="isOpen && !searchTerm.trim() && ligas.length > maxVisible">
            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                💡 Escribe para buscar entre <span x-text="ligas.length"></span> ligas disponibles
            </div>
        </template>
    </div>
</div>