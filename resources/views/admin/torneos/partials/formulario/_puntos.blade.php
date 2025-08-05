<!-- Puntos por Victoria -->
<div>
    <label for="puntos_por_victoria" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Puntos por Victoria</label>
    <input type="number" id="puntos_por_victoria" name="puntos_por_victoria" x-model="formData.puntos_por_victoria" min="0" max="10"
           class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
           required>
</div>

<!-- Puntos por Empate -->
<div>
    <label for="puntos_por_empate" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Puntos por Empate</label>
    <input type="number" id="puntos_por_empate" name="puntos_por_empate" x-model="formData.puntos_por_empate" min="0" max="10"
           class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
           required>
</div>

<!-- Puntos por Derrota -->
<div>
    <label for="puntos_por_derrota" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Puntos por Derrota</label>
    <input type="number" id="puntos_por_derrota" name="puntos_por_derrota" x-model="formData.puntos_por_derrota" min="0" max="10"
           class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
           required>
</div>

<!-- Configuración de Playoffs (debajo de Puntos por Derrota) -->
<div class="mt-4 flex flex-col sm:flex-row sm:items-center gap-4">
    <!-- Input hidden para asegurar que siempre se envíe un valor -->
    <input type="hidden" name="usa_playoffs" value="0">
    
    <!-- Checkbox para habilitar playoffs -->
    <div class="flex items-center">
        <input 
            type="checkbox" 
            id="usa_playoffs" 
            name="usa_playoffs" 
            x-model="formData.usa_playoffs"
            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-dark-600 rounded"
            value="1"
            @change="formData.usa_playoffs = $event.target.checked ? '1' : '0'"
        >
        <label for="usa_playoffs" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Habilitar Playoffs</label>
    </div>

    <!-- Select de equipos (aparece al lado en pantallas grandes, debajo en móviles) -->
    <div x-show="formData.usa_playoffs == '1'" x-transition class="w-full sm:w-auto">
        <select 
            id="equipos_playoffs" 
            name="equipos_playoffs" 
            x-model="formData.equipos_playoffs"
            class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
            x-bind:required="formData.usa_playoffs == '1'"
        >
            <option value="">Seleccione cantidad</option>
            <option value="2">2 equipos</option>
            <option value="4">4 equipos</option>
            <option value="6">6 equipos</option>
            <option value="8">8 equipos</option>
        </select>
    </div>
</div>