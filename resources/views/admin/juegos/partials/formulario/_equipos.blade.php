
    <!-- Equipo Local -->
    <div>
        <label for="equipo_local_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Equipo Local <span class="text-orange-500 text-lg font-semibold">*</span></label>
        <select id="equipo_local_id" name="equipo_local_id" x-model="formData.equipo_local_id" 
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            <option value="">Seleccione un equipo</option>
            @foreach($equipos as $equipo)
                <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Equipo Visitante -->
    <div>
        <label for="equipo_visitante_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Equipo Visitante <span class="text-orange-500 text-lg font-semibold">*</span></label>
        <select id="equipo_visitante_id" name="equipo_visitante_id" x-model="formData.equipo_visitante_id" 
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            <option value="">Seleccione un equipo</option>
            @foreach($equipos as $equipo)
                <option value="{{ $equipo->id }}">{{ $equipo->nombre }}</option>
            @endforeach
        </select>
    </div>
