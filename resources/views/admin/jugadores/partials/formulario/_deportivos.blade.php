<!-- Selects de Liga y Categoría (usando el componente creado) -->
@include('components.selects.select_ligas_categorias_equipos')

<!-- Posiciones y Número de camiseta en una misma fila -->
<div class="flex flex-wrap gap-4">
    <!-- Posición Principal -->
    <div class="flex-1 min-w-[200px]">
        <div class="flex items-center h-[42px] mb-1">
            <label for="posicion_principal" class="block text-sm font-medium dark:text-gray-300 text-dark-800">
                Posición Principal <span class="text-orange-500 text-lg font-semibold">*</span>
            </label>
        </div>
        <select id="posicion_principal" name="posicion_principal" x-model="formData.posicion_principal" required
            class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            <option value="">Seleccione una posición</option>
            <option value="Base (PG)">Base (PG)</option>
            <option value="Escolta (SG)">Escolta (SG)</option>
            <option value="Alero (SF)">Alero (SF)</option>
            <option value="Ala-Pívot (PF)">Ala-Pívot (PF)</option>
            <option value="Centro (C)">Centro (C)</option>
        </select>
    </div>

    <!-- Posición Secundaria -->
    <div class="flex-1 min-w-[200px]">
        <div class="flex items-center h-[42px] mb-1">
            <label for="posicion_secundaria" class="block text-sm font-medium dark:text-gray-300 text-dark-800">
                Posición Secundaria
            </label>
        </div>
        <select id="posicion_secundaria" name="posicion_secundaria" x-model="formData.posicion_secundaria"
            class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            <option value="">Ninguna</option>
            <option value="Base (PG)">Base (PG)</option>
            <option value="Escolta (SG)">Escolta (SG)</option>
            <option value="Alero (SF)">Alero (SF)</option>
            <option value="Ala-Pívot (PF)">Ala-Pívot (PF)</option>
            <option value="Centro (C)">Centro (C)</option>
        </select>
    </div>

    <!-- Número de camiseta -->
    <div class="flex-1 min-w-[200px]">
        <div class="flex items-center h-[42px] mb-1">
            <label for="numero_camiseta" class="block text-sm font-medium dark:text-gray-300 text-dark-800">
                Número de Camiseta <span class="text-orange-500 text-lg font-semibold">*</span>
            </label>
        </div>
        <input type="number" id="numero_camiseta" name="numero_camiseta" x-model="formData.numero_camiseta" min="0" max="99" required
            class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
            placeholder="Número">
    </div>
</div>

<!-- Fecha de ingreso, Estado del jugador y Es capitán -->
<div class="flex flex-wrap gap-4">
    <!-- Fecha de ingreso -->
    <div class="flex-1 min-w-[200px]" x-data="{
        abrirCalendario(el) {
            // Forzar la apertura del datepicker
            el.showPicker();
        }
    }">
        <div class="flex items-center h-[42px] mb-1">
            <label for="fecha_ingreso" class="block text-sm font-medium dark:text-gray-300 text-dark-800">
                Fecha de Ingreso <span class="text-orange-500 text-lg font-semibold">*</span>
            </label>
        </div>
        <input 
            type="date" 
            id="fecha_ingreso" 
            name="fecha_ingreso" 
            x-model="formData.fecha_ingreso" 
            @click="abrirCalendario($el)"
            required
            class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition cursor-pointer">
    </div>

    <!-- Estado del jugador -->
    <div class="flex-1 min-w-[200px]">
        <div class="flex items-center h-[42px] mb-1">
            <label for="activo" class="block text-sm font-medium dark:text-gray-300 text-dark-800">
                Estado del Jugador <span class="text-orange-500 text-lg font-semibold">*</span>
            </label>
        </div>
        <select id="activo" name="activo" x-model="formData.activo" required
            class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            <option value="1">Activo</option>
            <option value="0">Inactivo</option>
        </select>
    </div>

    <!-- Checkbox Es capitán -->
    <div class="flex-1 min-w-[200px] flex items-end">
        <div class="flex items-center h-full">
            <input 
                type="checkbox" 
                id="es_capitan" 
                name="es_capitan" 
                x-model="formData.es_capitan"
                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-dark-600 rounded"
            >
            <label for="es_capitan" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                Es capitán del equipo
            </label>
        </div>
    </div>
</div>