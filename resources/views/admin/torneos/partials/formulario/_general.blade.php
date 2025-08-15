<!-- Nombre del Torneo -->
<div>
    <label for="nombre" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Nombre del Torneo <span class="text-orange-500 text-lg font-semibold">*</span></label>
    <input type="text" id="nombre" name="nombre" x-model="formData.nombre"
           class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
           required>
</div>

<!-- Tipo de Torneo -->
<div>
    <label for="tipo" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
        Tipo de Torneo <span class="text-orange-500 text-lg font-semibold">*</span>
    </label>
    <select id="tipo" name="tipo" x-model="formData.tipo"
            class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
            required>
        <option value="" disabled selected hidden>Selecciona un tipo de torneo</option>
        <option value="por_puntos">Por Puntos + Playoffs</option>
        <option value="eliminacion_directa" disabled>Eliminación Directa</option>
        <option value="doble_eliminacion" disabled>Doble Eliminación</option>
        <option value="round_robin" disabled>Liga (Round Robin)</option>
        <option value="grupos_eliminacion" disabled>Grupos + Eliminación</option>
    </select>
</div>


<!-- Fecha de Inicio -->
<div>
    <label for="fecha_inicio" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Fecha de Inicio <span class="text-orange-500 text-lg font-semibold">*</span></label>
    <input type="date" id="fecha_inicio" name="fecha_inicio" x-model="formData.fecha_inicio"
           class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
           required>
</div>

<!-- Fecha de Fin -->
<div>
    <label for="fecha_fin" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Fecha de Fin (Opcional)</label>
    <input type="date" id="fecha_fin" name="fecha_fin" x-model="formData.fecha_fin"
           class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
</div>

<!-- Duración de Cuartos (minutos) -->
<div>
    <label for="duracion_cuarto_minutos" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Duración de Cuartos (minutos) <span class="text-orange-500 text-lg font-semibold">*</span></label>
    <input type="number" id="duracion_cuarto_minutos" name="duracion_cuarto_minutos" x-model="formData.duracion_cuarto_minutos" min="1" max="60"
           class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
           required>
</div>

<!-- Duración del descanso -->
<div>
    <label for="duracion_descanso_minutos" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
        Duración del descanso (min) <span class="text-orange-500 text-lg font-semibold">*</span>
    </label>
    <input 
        id="duracion_descanso_minutos" 
        name="duracion_descanso_minutos" 
        type="number" 
        min="1" 
        max="30"
        x-model="formData.duracion_descanso_minutos"
        class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
        required
    >
</div>

<!-- Duración de Tiempo entre partidos -->
<div>
    <label for="tiempo_entre_partidos_minutos" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Duración de Tiempos entre partidos (minutos) <span class="text-orange-500 text-lg font-semibold">*</span></label>
    <input type="number" id="tiempo_entre_partidos_minutos" name="tiempo_entre_partidos_minutos" x-model="formData.tiempo_entre_partidos_minutos" min="1" max="60"
           class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
           required>
</div>


<!-- Premio Total -->
<div>
    <label for="premio_total" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Premio Total ($)</label>
    <input type="number" id="premio_total" name="premio_total" x-model="formData.premio_total" min="0" step="0.01"
           class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
</div>

<!-- Descripción -->
<div>
    <label for="descripcion" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Descripción</label>
    <textarea id="descripcion" name="descripcion" x-model="formData.descripcion" rows="3"
              class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"></textarea>
</div>