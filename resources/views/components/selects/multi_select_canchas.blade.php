<div x-data="{
    isOpen: false,
    searchTerm: '',
    selectedCanchas: [],
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
            this.selectedCanchas.push({...cancha});
            this.searchTerm = '';
            this.$nextTick(() => this.$refs.search.focus());
        }
    },
    removeCancha(index) {
        this.selectedCanchas.splice(index, 1);
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
    
    <!-- Input hidden para el formulario -->
    <input type="hidden" name="canchas_ids" :value="selectedCanchas.map(c => c.id).join(',')">
    
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
</div>