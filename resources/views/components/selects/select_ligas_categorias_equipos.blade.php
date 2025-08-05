<div class="flex flex-wrap gap-4 items-start">
    <!-- Select de Liga -->
    <div class="flex-1 min-w-[200px] max-w-[calc(30%-12px)]">
        <label for="liga_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
            Liga <span class="text-orange-500 text-lg font-semibold">*</span>
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
                return filtered;
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
                // Limpiar categoría cuando cambia la liga
                formData.categoria_id = '';
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
                formData.liga_id = '';
                formData.categoria_id = '';
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
                    class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md pl-3 pr-20 py-2 text-black dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="{ 
                        'ring-2 ring-primary-500 border-transparent': isOpen,
                        'text-gray-900 dark:text-white font-medium': formData.liga_id && !searchTerm,
                        'text-gray-500 dark:text-gray-400': !formData.liga_id && !searchTerm 
                    }"
                >
                
                <div class="absolute inset-y-0 right-0 flex items-center">
                    <!-- Botón limpiar -->
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
                <!-- Información útil -->
                <template x-if="searchTerm && filteredLigas.length > 0">
                    <div class="px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-700 text-xs text-blue-700 dark:text-blue-300">
                        <span x-text="filteredLigas.length"></span> liga<span x-text="filteredLigas.length !== 1 ? 's' : ''"></span> encontrada<span x-text="filteredLigas.length !== 1 ? 's' : ''"></span>
                    </div>
                </template>

                <!-- Lista de opciones -->
                <div class="max-h-52 overflow-y-auto">
                    <!-- Opción "Sin selección" -->
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

                    <!-- Botón "Ver todas" -->
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
        </div>
    </div>

    <!-- Select de Categoría -->
    <div class="flex-1 min-w-[200px] max-w-[calc(30%-12px)]">
        <label for="categoria_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
            Categoría <span class="text-orange-500 text-lg font-semibold">*</span>
        </label>

        <div class="relative" x-data="{
            isOpenCat: false,
            searchTermCat: '',
            maxVisibleCat: 8,
            showAllCat: false,
            loadingCategorias: false,
            categorias: [],
            

            init() {
                // Si hay categoría pre-seleccionada pero no está en la lista
                if (formData.categoria_id && !this.categorias.some(c => c.id == formData.categoria_id)) {
                    this.categorias.unshift({
                        id: formData.categoria_id,
                        nombre: formData.categoria_nombre || 'Categoría seleccionada',
                        edad_minima: formData.categoria_edad_minima,
                        edad_maxima: formData.categoria_edad_maxima
                    });
                }
                
                // Cargar categorías si hay liga
                if (formData.liga_id) {
                    this.loadCategorias();
                }
            },


            get filteredCategorias() {
                if (!this.searchTermCat.trim()) {
                    return this.showAllCat ? this.categorias : this.categorias.slice(0, this.maxVisibleCat);
                }
                let filtered = this.categorias.filter(categoria => 
                    categoria.nombre.toLowerCase().includes(this.searchTermCat.toLowerCase())
                );
                return filtered;
            },
            
            get selectedCategoriaName() {
                let selected = this.categorias.find(categoria => categoria.id == formData.categoria_id);
                return selected ? selected.nombre : '';
            },
            
            get hasMoreResultsCat() {
                return !this.searchTermCat.trim() && this.categorias.length > this.maxVisibleCat && !this.showAllCat;
            },
            
            get placeholderTextCat() {
                if (this.loadingCategorias) return 'Cargando categorías...';
                if (this.searchTermCat) return this.searchTermCat;
                if (formData.categoria_id && this.selectedCategoriaName) return this.selectedCategoriaName;
                if (this.categorias.length === 0) return 'No hay categorías disponibles';
                return 'Buscar o seleccionar categoría...';
            },
            
            async loadCategorias() {
                if (!formData.liga_id) {
                    this.categorias = [];
                    return;
                }
                
                this.loadingCategorias = true;
                try {
                    const response = await fetch(`/api/ligas/${formData.liga_id}/categorias`);
                    const data = await response.json();
                    this.categorias = data.categorias || [];
                } catch (error) {
                    console.error('Error cargando categorías:', error);
                    this.categorias = [];
                } finally {
                    this.loadingCategorias = false;
                }
            },
            
            selectCategoria(categoriaId, categoriaNombre) {
                formData.categoria_id = categoriaId.toString();
                this.searchTermCat = '';
                this.isOpenCat = false;
                this.showAllCat = false;
            },
            
            openDropdownCat() {
                if (this.loadingCategorias) return;
                this.isOpenCat = true;
                this.showAllCat = false;
                this.$nextTick(() => {
                    this.$refs.searchInputCat.focus();
                });
            },
            
            handleInputCat() {
                if (!this.isOpenCat) this.isOpenCat = true;
                this.showAllCat = false;
            },
            
            clearSelectionCat() {
                formData.categoria_id = '';
                this.searchTermCat = '';
                this.isOpenCat = true;
                this.$nextTick(() => {
                    this.$refs.searchInputCat.focus();
                });
            },
            
            handleKeydownCat(event) {
                if (event.key === 'Escape') {
                    this.isOpenCat = false;
                    this.searchTermCat = '';
                }
            }
        }" 
        x-ref="categoriaSelect"
        @click.away="isOpenCat = false; searchTermCat = ''"
        x-init="init(); $watch('formData.liga_id', (value) => { 
            if (!value) formData.categoria_id = ''; 
            loadCategorias(); 
        })"
        >
            
            <!-- Input principal categoría -->
            <div class="relative">
                <input 
                    type="text"
                    x-ref="searchInputCat"
                    x-model="searchTermCat"
                    @input="handleInputCat()"
                    @focus="openDropdownCat()"
                    @keydown="handleKeydownCat($event)"
                    :placeholder="placeholderTextCat"
                    :disabled="!formData.liga_id || loadingCategorias"
                    class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md pl-3 pr-20 py-2 text-black dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="{ 
                        'ring-2 ring-primary-500 border-transparent': isOpenCat,
                        'text-gray-900 dark:text-white font-medium': formData.categoria_id && !searchTermCat,
                        'text-gray-500 dark:text-gray-400': !formData.categoria_id && !searchTermCat 
                    }"
                >
                
                <div class="absolute inset-y-0 right-0 flex items-center">
                    <!-- Loading spinner -->
                    <div x-show="loadingCategorias" class="mr-3">
                        <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    
                    <!-- Botón limpiar categoría -->
                    <button 
                        x-show="formData.categoria_id && !searchTermCat && !loadingCategorias"
                        @click="clearSelectionCat()"
                        type="button"
                        class="mr-1 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                        title="Limpiar selección"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    
                    <!-- Flecha dropdown categoría -->
                    <button 
                        @click="isOpenCat ? (isOpenCat = false, searchTermCat = '') : openDropdownCat()"
                        type="button"
                        :disabled="!formData.liga_id || loadingCategorias"
                        class="mr-2 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        title="Abrir opciones"
                    >
                        <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': isOpenCat }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Input hidden para categoría -->
            <input type="hidden" name="categoria_id" x-model="formData.categoria_id">

            <!-- Dropdown categorías -->
            <div 
                x-show="isOpenCat && !loadingCategorias"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute z-40 w-full mt-1 bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md shadow-lg max-h-64"
            >
                <!-- Información útil para categorías -->
                <template x-if="searchTermCat && filteredCategorias.length > 0">
                    <div class="px-3 py-2 bg-green-50 dark:bg-green-900/20 border-b border-green-200 dark:border-green-700 text-xs text-green-700 dark:text-green-300">
                        <span x-text="filteredCategorias.length"></span> categoría<span x-text="filteredCategorias.length !== 1 ? 's' : ''"></span> encontrada<span x-text="filteredCategorias.length !== 1 ? 's' : ''"></span>
                    </div>
                </template>

                <!-- Lista de categorías -->
                <div class="max-h-52 overflow-y-auto">
                    <!-- Opción "Sin selección" para categorías -->
                    <template x-if="!searchTermCat.trim()">
                        <button
                            type="button"
                            @click="selectCategoria('', '')"
                            class="w-full text-left px-3 py-2 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-dark-600 transition-colors italic"
                            :class="{ 'bg-gray-100 dark:bg-dark-600': !formData.categoria_id }"
                        >
                            Sin selección
                        </button>
                    </template>

                    <!-- Categorías filtradas -->
                    <template x-for="categoria in filteredCategorias" :key="categoria.id">
                        <button
                            type="button"
                            @click="selectCategoria(categoria.id, categoria.nombre)"
                            class="w-full text-left px-3 py-2 text-sm text-black dark:text-white hover:bg-gray-100 dark:hover:bg-dark-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-dark-600 transition-colors"
                            :class="{ 
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-2 border-primary-500': formData.categoria_id == categoria.id 
                            }"
                        >
                            <div>
                                <span x-text="categoria.nombre"></span>
                                <template x-if="categoria.edad_minima && categoria.edad_maxima">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                        (<span x-text="categoria.edad_minima"></span>-<span x-text="categoria.edad_maxima"></span> años)
                                    </span>
                                </template>
                            </div>
                            <!-- Indicador de selección actual -->
                            <template x-if="formData.categoria_id == categoria.id">
                                <span class="float-right text-primary-500">
                                    <svg class="h-4 w-4 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </template>
                        </button>
                    </template>

                    <!-- Mensaje cuando no hay resultados en categorías -->
                    <template x-if="filteredCategorias.length === 0">
                        <div class="px-3 py-4 text-center">
                            <template x-if="searchTermCat.trim()">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-6 w-6 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    No se encontraron categorías con "<strong x-text="searchTermCat"></strong>"
                                </div>
                            </template>
                            <template x-if="!searchTermCat.trim()">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-6 w-6 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path>
                                    </svg>
                                    Esta liga no tiene categorías disponibles
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Botón "Ver todas" para categorías -->
                    <template x-if="hasMoreResultsCat">
                        <div class="border-t border-gray-200 dark:border-dark-600 bg-gray-50 dark:bg-dark-800">
                            <button
                                type="button"
                                @click="showAllCat = true"
                                class="w-full px-3 py-2 text-sm text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 focus:outline-none focus:bg-primary-50 dark:focus:bg-primary-900/20 transition-colors font-medium"
                            >
                                Ver todas las <span x-text="categorias.length"></span> categorías
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Nuevo Select de Equipo -->
    <div class="flex-1 min-w-[200px] max-w-[calc(40%-12px)]">
        <label for="equipo_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
            Equipo <span class="text-orange-500 text-lg font-semibold">*</span>
        </label>

        <div class="relative" x-data="{
            isOpenEquipo: false,
            searchTermEquipo: '',
            maxVisibleEquipo: 8,
            showAllEquipo: false,
            loadingEquipos: false,
            equipos: [],
            
            init() {
                // Si hay equipo pre-seleccionado pero no está en la lista
                if (formData.equipo_id && !this.equipos.some(e => e.id == formData.equipo_id)) {
                    this.equipos.unshift({
                        id: formData.equipo_id,
                        nombre: formData.equipo_nombre || 'Equipo seleccionado',
                        logo_url: formData.equipo_logo_url
                    });
                }
                
                // Cargar equipos si hay categoría
                if (formData.categoria_id) {
                    this.loadEquipos();
                }
            },

            get filteredEquipos() {
                if (!this.searchTermEquipo.trim()) {
                    return this.showAllEquipo ? this.equipos : this.equipos.slice(0, this.maxVisibleEquipo);
                }
                let filtered = this.equipos.filter(equipo => 
                    equipo.nombre.toLowerCase().includes(this.searchTermEquipo.toLowerCase())
                );
                return filtered;
            },
            
            get selectedEquipoName() {
                let selected = this.equipos.find(equipo => equipo.id == formData.equipo_id);
                return selected ? selected.nombre : '';
            },
            
            get hasMoreResultsEquipo() {
                return !this.searchTermEquipo.trim() && this.equipos.length > this.maxVisibleEquipo && !this.showAllEquipo;
            },
            
            get placeholderTextEquipo() {
                if (this.loadingEquipos) return 'Cargando equipos...';
                if (this.searchTermEquipo) return this.searchTermEquipo;
                if (formData.equipo_id && this.selectedEquipoName) return this.selectedEquipoName;
                if (this.equipos.length === 0) return 'Selecciona una categoría primero';
                return 'Buscar o seleccionar equipo...';
            },
            
            async loadEquipos() {
                if (!formData.categoria_id) {
                    this.equipos = [];
                    return;
                }
                
                this.loadingEquipos = true;
                try {
                    const response = await fetch(`/api/categorias/${formData.categoria_id}/equipos`);
                    const data = await response.json();
                    this.equipos = data.equipos || [];
                } catch (error) {
                    console.error('Error cargando equipos:', error);
                    this.equipos = [];
                } finally {
                    this.loadingEquipos = false;
                }
            },
            
            selectEquipo(equipoId, equipoNombre) {
                formData.equipo_id = equipoId.toString();
                this.searchTermEquipo = '';
                this.isOpenEquipo = false;
                this.showAllEquipo = false;
            },
            
            openDropdownEquipo() {
                if (this.loadingEquipos) return;
                this.isOpenEquipo = true;
                this.showAllEquipo = false;
                this.$nextTick(() => {
                    this.$refs.searchInputEquipo.focus();
                });
            },
            
            handleInputEquipo() {
                if (!this.isOpenEquipo) this.isOpenEquipo = true;
                this.showAllEquipo = false;
            },
            
            clearSelectionEquipo() {
                formData.equipo_id = '';
                this.searchTermEquipo = '';
                this.isOpenEquipo = true;
                this.$nextTick(() => {
                    this.$refs.searchInputEquipo.focus();
                });
            },
            
            handleKeydownEquipo(event) {
                if (event.key === 'Escape') {
                    this.isOpenEquipo = false;
                    this.searchTermEquipo = '';
                }
            }
        }" 
        x-ref="equipoSelect"
        @click.away="isOpenEquipo = false; searchTermEquipo = ''"
        x-init="init(); $watch('formData.categoria_id', (value) => { 
            if (!value) formData.equipo_id = ''; 
            loadEquipos(); 
        })"
        >
            
            <!-- Input principal equipo -->
            <div class="relative">
                <input 
                    type="text"
                    x-ref="searchInputEquipo"
                    x-model="searchTermEquipo"
                    @input="handleInputEquipo()"
                    @focus="openDropdownEquipo()"
                    @keydown="handleKeydownEquipo($event)"
                    :placeholder="placeholderTextEquipo"
                    :disabled="!formData.categoria_id || loadingEquipos"
                    class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md pl-3 pr-20 py-2 text-black dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="{ 
                        'ring-2 ring-primary-500 border-transparent': isOpenEquipo,
                        'text-gray-900 dark:text-white font-medium': formData.equipo_id && !searchTermEquipo,
                        'text-gray-500 dark:text-gray-400': !formData.equipo_id && !searchTermEquipo 
                    }"
                >
                
                <div class="absolute inset-y-0 right-0 flex items-center">
                    <!-- Loading spinner -->
                    <div x-show="loadingEquipos" class="mr-3">
                        <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    
                    <!-- Botón limpiar equipo -->
                    <button 
                        x-show="formData.equipo_id && !searchTermEquipo && !loadingEquipos"
                        @click="clearSelectionEquipo()"
                        type="button"
                        class="mr-1 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                        title="Limpiar selección"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    
                    <!-- Flecha dropdown equipo -->
                    <button 
                        @click="isOpenEquipo ? (isOpenEquipo = false, searchTermEquipo = '') : openDropdownEquipo()"
                        type="button"
                        :disabled="!formData.categoria_id || loadingEquipos"
                        class="mr-2 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        title="Abrir opciones"
                    >
                        <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': isOpenEquipo }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Input hidden para equipo -->
            <input type="hidden" name="equipo_id" x-model="formData.equipo_id">

            <!-- Dropdown equipos -->
            <div 
                x-show="isOpenEquipo && !loadingEquipos"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute z-30 w-full mt-1 bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md shadow-lg max-h-64"
            >
                <!-- Información útil para equipos -->
                <template x-if="searchTermEquipo && filteredEquipos.length > 0">
                    <div class="px-3 py-2 bg-purple-50 dark:bg-purple-900/20 border-b border-purple-200 dark:border-purple-700 text-xs text-purple-700 dark:text-purple-300">
                        <span x-text="filteredEquipos.length"></span> equipo<span x-text="filteredEquipos.length !== 1 ? 's' : ''"></span> encontrado<span x-text="filteredEquipos.length !== 1 ? 's' : ''"></span>
                    </div>
                </template>

                <!-- Lista de equipos -->
                <div class="max-h-52 overflow-y-auto">
                    <!-- Opción "Sin selección" para equipos -->
                    <template x-if="!searchTermEquipo.trim()">
                        <button
                            type="button"
                            @click="selectEquipo('', '')"
                            class="w-full text-left px-3 py-2 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-dark-600 transition-colors italic"
                            :class="{ 'bg-gray-100 dark:bg-dark-600': !formData.equipo_id }"
                        >
                            Sin selección
                        </button>
                    </template>

                    <!-- Equipos filtrados -->
                    <template x-for="equipo in filteredEquipos" :key="equipo.id">
                        <button
                            type="button"
                            @click="selectEquipo(equipo.id, equipo.nombre)"
                            class="w-full text-left px-3 py-2 text-sm text-black dark:text-white hover:bg-gray-100 dark:hover:bg-dark-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-dark-600 transition-colors flex items-center"
                            :class="{ 
                                'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border-l-2 border-primary-500': formData.equipo_id == equipo.id 
                            }"
                        >
                            <!-- Logo del equipo si existe -->
                            <template x-if="equipo.logo_url">
                                <img :src="equipo.logo_url" class="w-5 h-5 mr-2 rounded-full object-cover">
                            </template>
                            
                            <span x-text="equipo.nombre"></span>
                            
                            <!-- Indicador de selección actual -->
                            <template x-if="formData.equipo_id == equipo.id">
                                <span class="ml-auto text-primary-500">
                                    <svg class="h-4 w-4 inline" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </template>
                        </button>
                    </template>

                    <!-- Mensaje cuando no hay resultados en equipos -->
                    <template x-if="filteredEquipos.length === 0">
                        <div class="px-3 py-4 text-center">
                            <template x-if="searchTermEquipo.trim()">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-6 w-6 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    No se encontraron equipos con "<strong x-text="searchTermEquipo"></strong>"
                                </div>
                            </template>
                            <template x-if="!searchTermEquipo.trim()">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-6 w-6 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7"></path>
                                    </svg>
                                    Esta categoría no tiene equipos disponibles
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Botón "Ver todos" para equipos -->
                    <template x-if="hasMoreResultsEquipo">
                        <div class="border-t border-gray-200 dark:border-dark-600 bg-gray-50 dark:bg-dark-800">
                            <button
                                type="button"
                                @click="showAllEquipo = true"
                                class="w-full px-3 py-2 text-sm text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 focus:outline-none focus:bg-primary-50 dark:focus:bg-primary-900/20 transition-colors font-medium"
                            >
                                Ver todos los <span x-text="equipos.length"></span> equipos
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>