<div x-data="{
    isOpen: false,
    searchTerm: '',
    selectedCanchas: {{ json_encode($selectedCanchasWithPivot ?? []) }}, // Cambio importante aquí
    canchas: {{ json_encode($canchas->map(function($cancha) {
        return [
            'id' => $cancha->id,
            'nombre' => $cancha->nombre,
            'tipo_superficie' => $cancha->tipo_superficie,
            'techada' => $cancha->techada,
            'iluminacion' => $cancha->iluminacion,
            'tarifa_por_hora' => $cancha->tarifa_por_hora
        ];
    })) }},
    init() {
        // Si hay canchas pre-seleccionadas, las marcamos como seleccionadas
        if (this.selectedCanchas.length > 0) {
            // Asegurarnos de que tenemos objetos completos con datos pivot
            this.selectedCanchas = this.selectedCanchas.map(selected => {
                const found = this.canchas.find(c => c.id == selected.id);
                return {
                    ...(found || {id: selected.id, nombre: 'Cancha no encontrada'}),
                    pivot: selected.pivot || {es_principal: 0, orden_prioridad: 0}
                };
            });
        }
    },
    get filteredCanchas() {
        if (!this.searchTerm.trim()) {
            return this.canchas.filter(cancha => 
                !this.selectedCanchas.some(sc => sc.id === cancha.id)
            );
        }
        return this.canchas.filter(cancha => 
            !this.selectedCanchas.some(sc => sc.id === cancha.id) &&
            (
                cancha.nombre.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                (cancha.tipo_superficie && cancha.tipo_superficie.toLowerCase().includes(this.searchTerm.toLowerCase()))
            )
        );
    },
    addCancha(cancha) {
        if (!this.selectedCanchas.some(sc => sc.id === cancha.id)) {
            this.selectedCanchas.push({
                ...cancha,
                pivot: {
                    es_principal: 0,
                    orden_prioridad: this.selectedCanchas.length + 1
                }
            });
            this.searchTerm = '';
            this.$nextTick(() => this.$refs.search.focus());
        }
    },
    removeCancha(index) {
        this.selectedCanchas.splice(index, 1);
        // Reordenar las prioridades
        this.selectedCanchas.forEach((cancha, idx) => {
            cancha.pivot.orden_prioridad = idx + 1;
        });
    },
    togglePrincipal(index) {
        this.selectedCanchas.forEach((cancha, i) => {
            cancha.pivot.es_principal = (i === index) ? 1 : 0;
        });
    },
    moveUp(index) {
        if (index > 0) {
            const temp = this.selectedCanchas[index];
            this.selectedCanchas[index] = this.selectedCanchas[index - 1];
            this.selectedCanchas[index - 1] = temp;
            
            // Actualizar prioridades
            this.selectedCanchas.forEach((cancha, idx) => {
                cancha.pivot.orden_prioridad = idx + 1;
            });
        }
    },
    moveDown(index) {
        if (index < this.selectedCanchas.length - 1) {
            const temp = this.selectedCanchas[index];
            this.selectedCanchas[index] = this.selectedCanchas[index + 1];
            this.selectedCanchas[index + 1] = temp;
            
            // Actualizar prioridades
            this.selectedCanchas.forEach((cancha, idx) => {
                cancha.pivot.orden_prioridad = idx + 1;
            });
        }
    },
    toggleDropdown() {
        this.isOpen = !this.isOpen;
        if (this.isOpen) this.$nextTick(() => this.$refs.search.focus());
    }
}" 
@click.away="isOpen = false"
class="relative">
    
    <!-- Etiqueta -->
    <label class="block text-sm font-medium mb-1 dark:text-gray-300 text-gray-800">
        Canchas <span class="text-red-400">*</span>
    </label>
    
    <!-- Contenedor de tags + input -->
    <div @click="toggleDropdown()" 
         class="min-h-10 bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 cursor-text flex flex-wrap gap-2" 
         :class="{ 'ring-2 ring-primary-500 border-transparent': isOpen }">
        
        <!-- Tags de canchas seleccionadas -->
        <template x-for="(cancha, index) in selectedCanchas" :key="cancha.id">
            <div class="flex items-center bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-200 rounded-full px-3 py-1 text-sm">
                <span x-text="cancha.nombre" class="dark:text-white"></span>
                <span x-show="cancha.pivot.es_principal == 1" class="ml-1 text-yellow-500">★</span>
                <button @click.stop="removeCancha(index)" type="button" class="ml-1 text-primary-500 hover:text-primary-700 dark:text-primary-300 dark:hover:text-primary-100">
                    &times;
                </button>
            </div>
        </template>
        
        <!-- Input de búsqueda -->
        <input x-ref="search" 
               x-model="searchTerm" 
               @click.stop="isOpen = true" 
               @keydown.escape="isOpen = false" 
               class="flex-grow bg-transparent outline-none min-w-20 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400" 
               placeholder="Buscar canchas...">
    </div>
    
    <!-- Inputs hidden para el formulario -->
    <template x-for="(cancha, index) in selectedCanchas" :key="'hidden-'+cancha.id">
        <input type="hidden" :name="'canchas['+index+'][id]'" :value="cancha.id">
        <input type="hidden" :name="'canchas['+index+'][es_principal]'" :value="cancha.pivot.es_principal">
        <input type="hidden" :name="'canchas['+index+'][orden_prioridad]'" :value="cancha.pivot.orden_prioridad">
    </template>
    
    <!-- Dropdown de opciones -->
    <div x-show="isOpen" 
         @click.stop 
         x-transition 
         class="absolute z-10 w-full mt-1 bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md shadow-lg max-h-60 overflow-y-auto">
        
        <!-- Resultados de búsqueda -->
        <template x-for="cancha in filteredCanchas" :key="cancha.id">
            <div @click="addCancha(cancha)" 
                 class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-dark-600 cursor-pointer flex items-center justify-between">
                <div>
                    <span class="text-gray-900 dark:text-white" x-text="cancha.nombre"></span>
                    <div class="flex gap-2 mt-1">
                        <span class="text-xs text-gray-500 dark:text-gray-300" x-text="cancha.tipo_superficie"></span>
                        <template x-if="cancha.techada">
                            <span class="text-xs text-blue-600 dark:text-blue-300">Techada</span>
                        </template>
                        <template x-if="cancha.iluminacion">
                            <span class="text-xs text-yellow-600 dark:text-yellow-300">Iluminación</span>
                        </template>
                    </div>
                </div>
                <span class="text-sm text-gray-500 dark:text-gray-300" x-text="'$' + cancha.tarifa_por_hora"></span>
            </div>
        </template>
        
        <!-- Mensaje sin resultados -->
        <div x-show="filteredCanchas.length === 0" class="px-3 py-2 text-gray-500 dark:text-gray-400 text-sm">
            No se encontraron canchas
        </div>
    </div>
    
    <!-- Panel de gestión de canchas seleccionadas -->
    <div x-show="selectedCanchas.length > 0" class="mt-4 bg-gray-50 dark:bg-dark-800 rounded-lg p-4">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Gestión de Canchas Seleccionadas</h4>
        
        <div class="space-y-3">
            <template x-for="(cancha, index) in selectedCanchas" :key="'manage-'+cancha.id">
                <div class="flex items-center justify-between bg-white dark:bg-dark-700 p-3 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <span class="text-gray-900 dark:text-white font-medium" x-text="cancha.nombre"></span>
                        <span x-show="cancha.pivot.es_principal == 1" class="ml-2 text-yellow-500">(Principal)</span>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button @click="togglePrincipal(index)" type="button" class="p-1 text-gray-500 hover:text-yellow-500" :class="{'text-yellow-500': cancha.pivot.es_principal == 1}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </button>
                        
                        <button @click="moveUp(index)" type="button" class="p-1 text-gray-500 hover:text-primary-500" :disabled="index === 0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        </button>
                        
                        <button @click="moveDown(index)" type="button" class="p-1 text-gray-500 hover:text-primary-500" :disabled="index === selectedCanchas.length - 1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="'Prioridad: ' + cancha.pivot.orden_prioridad"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>