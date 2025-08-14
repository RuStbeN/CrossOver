<!-- Select de Temporada Simplificado -->
<div>
    <label for="temporada_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
        Temporada <span class="text-red-400">*</span>
    </label>

    <div class="relative" x-data="{
        isOpen: false,
        searchTerm: '',
        maxVisible: 8,
        showAll: false,
        temporadas: {{ $temporadas->toJson() }},
        get filteredTemporadas() {
            if (!this.searchTerm.trim()) {
                return this.showAll ? this.temporadas : this.temporadas.slice(0, this.maxVisible);
            }
            let filtered = this.temporadas.filter(temporada => 
                temporada.nombre.toLowerCase().includes(this.searchTerm.toLowerCase())
            );
            return filtered;
        },
        get selectedTemporadaName() {
            let selected = this.temporadas.find(temporada => temporada.id == formData.temporada_id);
            return selected ? selected.nombre : '';
        },
        get hasMoreResults() {
            return !this.searchTerm.trim() && this.temporadas.length > this.maxVisible && !this.showAll;
        },
        get placeholderText() {
            if (this.searchTerm) return this.searchTerm;
            if (formData.temporada_id && this.selectedTemporadaName) return this.selectedTemporadaName;
            return 'Buscar o seleccionar temporada...';
        },
        selectTemporada(temporadaId, temporadaNombre) {
            formData.temporada_id = temporadaId.toString();
            this.searchTerm = '';
            this.isOpen = false;
            this.showAll = false;
        },
        openDropdown() {
            this.isOpen = true;
            this.showAll = false;
            this.$nextTick(() => {
                this.$refs.searchInput.focus();
            });
        },
        handleInput() {
            if (!this.isOpen) this.isOpen = true;
            this.showAll = false;
        },
        clearSelection() {
            formData.temporada_id = '';
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
        },
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }
    }" @click.away="isOpen = false; searchTerm = ''">
        
        <!-- Input principal -->
        <div class="relative">
            <input 
                type="text"
                x-ref="searchInput"
                x-model="searchTerm"
                @input="handleInput()"
                @focus="openDropdown()"
                @keydown="handleKeydown($event)"
                :placeholder="placeholderText"
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md pl-3 pr-20 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                :class="{ 
                    'ring-2 ring-primary-500 border-transparent': isOpen,
                    'font-medium': formData.temporada_id && !searchTerm
                }"
            >
            
            <div class="absolute inset-y-0 right-0 flex items-center">
                <!-- Botón limpiar -->
                <button 
                    x-show="formData.temporada_id && !searchTerm"
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
        <input type="hidden" name="temporada_id" x-model="formData.temporada_id" required>

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
            <!-- Información de resultados -->
            <template x-if="searchTerm && filteredTemporadas.length > 0">
                <div class="px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-700 text-xs text-blue-700 dark:text-blue-300">
                    <span x-text="filteredTemporadas.length"></span> temporada<span x-text="filteredTemporadas.length !== 1 ? 's' : ''"></span> encontrada<span x-text="filteredTemporadas.length !== 1 ? 's' : ''"></span>
                </div>
            </template>

            <!-- Lista de opciones -->
            <div class="max-h-52 overflow-y-auto">
                <!-- Opción "Sin selección" -->
                <template x-if="!searchTerm.trim()">
                    <button
                        type="button"
                        @click="selectTemporada('', '')"
                        class="w-full text-left px-3 py-2 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-dark-600 transition-colors italic"
                        :class="{ 'bg-gray-100 dark:bg-dark-600': !formData.temporada_id }"
                    >
                        Sin selección
                    </button>
                </template>

                <!-- Temporadas filtradas -->
                <template x-for="temporada in filteredTemporadas" :key="temporada.id">
                    <button
                        type="button"
                        @click="selectTemporada(temporada.id, temporada.nombre)"
                        class="w-full text-left px-3 py-2 text-sm text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-dark-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-dark-600 transition-colors"
                        :class="{ 
                            'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-2 border-primary-500': formData.temporada_id == temporada.id 
                        }"
                    >
                        <div>
                            <div class="font-medium" x-text="temporada.nombre"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <span x-text="formatDate(temporada.fecha_inicio)"></span> - 
                                <span x-text="formatDate(temporada.fecha_fin)"></span>
                            </div>
                        </div>
                    </button>
                </template>

                <!-- Mensaje cuando no hay resultados -->
                <template x-if="filteredTemporadas.length === 0">
                    <div class="px-3 py-4 text-center">
                        <template x-if="searchTerm.trim()">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-6 w-6 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                No se encontraron temporadas con "<strong x-text="searchTerm"></strong>"
                            </div>
                        </template>
                        <template x-if="!searchTerm.trim()">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                No hay temporadas disponibles
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Botón "Ver todas" -->
                <template x-if="hasMoreResults">
                    <div class="border-t border-gray-200 dark:border-dark-600 bg-gray-50 dark:bg-dark-800">
                        <button
                            type="button"
                            @click="showAll = true"
                            class="w-full px-3 py-2 text-sm text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 focus:outline-none focus:bg-primary-50 dark:focus:bg-primary-900/20 transition-colors font-medium"
                        >
                            Ver todas las <span x-text="temporadas.length"></span> temporadas
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Ayuda contextual -->
        <template x-if="isOpen && !searchTerm.trim() && temporadas.length > maxVisible">
            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                💡 Escribe para buscar entre <span x-text="temporadas.length"></span> temporadas disponibles
            </div>
        </template>
    </div>
</div>