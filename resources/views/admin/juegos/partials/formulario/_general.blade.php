<!-- Liga -->
    <div>
        <label for="liga_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Liga <span class="text-orange-500 text-lg font-semibold">*</span></label>
        <select id="liga_id" name="liga_id" x-model="formData.liga_id" required
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            <option value="">Seleccione una liga</option>
            @foreach($ligas as $liga)
                <option value="{{ $liga->id }}">{{ $liga->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Temporada -->
    <div>
        <label for="temporada_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Temporada </label>
        <select id="temporada_id" name="temporada_id" x-model="formData.temporada_id"
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            <option value="">Seleccione una temporada</option>
            @foreach($temporadas as $temporada)
                <option value="{{ $temporada->id }}">{{ $temporada->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Torneo (Opcional) -->
    <div>
        <label for="torneo_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
            Torneo 
            <span class="text-sm text-gray-500 dark:text-gray-400">(Opcional)</span>
        </label>
        <select id="torneo_id" name="torneo_id" x-model="formData.torneo_id"
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            <option value="">Sin torneo (Juego regular)</option>
            @foreach($torneos as $torneo)
                <option value="{{ $torneo->id }}">
                    {{ $torneo->nombre }} - {{ $torneo->liga->nombre }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Selecciona un torneo si este juego forma parte de un torneo específico
        </p>
    </div>

    <!-- Fecha -->
    <div>
        <label for="fecha" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Fecha <span class="text-orange-500 text-lg font-semibold">*</span></label>
        <input type="date" id="fecha" name="fecha" x-model="formData.fecha" required
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
    </div>

    <!-- Hora -->
    <div>
        <label for="hora" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Hora <span class="text-orange-500 text-lg font-semibold">*</span></label>
        <input type="time" id="hora" name="hora" x-model="formData.hora" required
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
    </div>

    <!-- Cancha -->
    <div>
        <label for="cancha_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Cancha <span class="text-orange-500 text-lg font-semibold">*</span></label>
        <select id="cancha_id" name="cancha_id" x-model="formData.cancha_id" required
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            <option value="">Seleccione una cancha</option>
            @foreach($canchas as $cancha)
                <option value="{{ $cancha->id }}">{{ $cancha->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Estado -->
    <div>
        <label for="estado" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Estado <span class="text-orange-500 text-lg font-semibold">*</span></label>
        <select id="estado" name="estado" x-model="formData.estado" required
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            <option value="Programado">Programado</option>
            <option value="En Curso">En Curso</option>
            <option value="Finalizado">Finalizado</option>
            <option value="Cancelado">Cancelado</option>
            <option value="Suspendido">Suspendido</option>
        </select>
    </div>

    <!-- Duración cuarto -->
    <div>
        <label for="duracion_cuarto" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Duración por cuarto (min) <span class="text-orange-500 text-lg font-semibold">*</span></label>
        <input type="number" id="duracion_cuarto" name="duracion_cuarto" x-model="formData.duracion_cuarto" min="1" required
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
    </div>

    <!-- Duración descanso -->
    <div>
        <label for="duracion_descanso" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Duración descanso (min) <span class="text-orange-500 text-lg font-semibold">*</span></label>
        <input type="number" id="duracion_descanso" name="duracion_descanso" x-model="formData.duracion_descanso" min="1" required
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
    </div>

    <!-- Estado Activo -->
    <div class="col-span-1">
        <label for="activo" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
            Estado <span class="text-orange-500 text-lg font-semibold">*</span>
        </label>
        <select
            id="activo"
            name="activo"
            x-model="formData.activo"
            required
            class="w-full bg-white dark:bg-dark-700 px-3 py-2 border border-gray-300 dark:border-dark-600 rounded-md text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
        >
            <option value="1">Activa</option>
            <option value="0">Inactiva</option>
        </select>
    </div>


    <!-- Observaciones -->
    <div class="mt-4">
        <label for="observaciones" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Observaciones</label>
        <textarea id="observaciones" name="observaciones" x-model="formData.observaciones" rows="3"
                    class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"></textarea>
    </div>

