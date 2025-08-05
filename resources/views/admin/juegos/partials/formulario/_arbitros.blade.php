
    <!-- Árbitro Principal -->
    <div>
        <label for="arbitro_principal_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Árbitro Principal <span class="text-orange-500 text-lg font-semibold">*</span></label>
        <select id="arbitro_principal_id" name="arbitro_principal_id" x-model="formData.arbitro_principal_id" required
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            <option value="">Seleccione un árbitro</option>
            @foreach($arbitros as $arbitro)
                <option value="{{ $arbitro->id }}">{{ $arbitro->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Árbitro Auxiliar -->
    <div>
        <label for="arbitro_auxiliar_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Árbitro Auxiliar </label>
        <select id="arbitro_auxiliar_id" name="arbitro_auxiliar_id" x-model="formData.arbitro_auxiliar_id"
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            <option value="">Seleccione un árbitro</option>
            @foreach($arbitros as $arbitro)
                <option value="{{ $arbitro->id }}">{{ $arbitro->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Mesa de Control -->
    <div>
        <label for="mesa_control_id" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Mesa de Control <span class="text-orange-500 text-lg font-semibold">*</span></label>
        <select id="mesa_control_id" name="mesa_control_id" x-model="formData.mesa_control_id" required
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
            <option value="">Seleccione un árbitro</option>
            @foreach($arbitros as $arbitro)
                <option value="{{ $arbitro->id }}">{{ $arbitro->nombre }}</option>
            @endforeach
        </select>
    </div>
