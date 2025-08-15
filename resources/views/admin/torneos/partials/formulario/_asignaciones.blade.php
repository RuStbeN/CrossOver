<!-- Selects temporadas-->
@include('components.selects.select_temporadas')


<!-- Multi Selects de canchas (usando el componente creado) -->
@include('components.selects.multi_select_canchas')


<!-- Selects de Liga y Categoría (usando el componente creado) -->
@include('components.selects.select_ligas_categorias')


<!-- Estado -->
<div>
    <label for="estado" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Estado <span class="text-orange-500 text-lg font-semibold">*</span></label>
    <select id="estado" name="estado" x-model="formData.estado"
            class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
            required>
        <option value="Programado">Programado</option>
        <option value="En Curso">En Curso</option>
        <option value="Finalizado">Finalizado</option>
        <option value="Cancelado">Cancelado</option>
        <option value="Suspendido">Suspendido</option>
    </select>
</div>

<!-- Estado Activo -->
<div>
    <label for="activo" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
        Torneo Activo <span class="text-orange-500 text-lg font-semibold">*</span>
    </label>
    <select
        id="activo"
        name="activo"
        x-model="formData.activo"
        class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
        required
    >
        <option value="1">Activo</option>
        <option value="0">Inactivo</option>
    </select>
</div>