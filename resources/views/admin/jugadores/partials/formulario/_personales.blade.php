<!-- Nombres -->
<div>
    <label for="nombre" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Nombre Completo <span class="text-orange-500 text-lg font-semibold">*</span></label>
    <input type="text" id="nombre" name="nombre" x-model="formData.nombre" required
        class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
        placeholder="Ingrese el nombre completo">
</div>

<!-- RFC -->
<div>
    <label for="rfc" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">RFC <span class="text-orange-500 text-lg font-semibold">*</span></label>
    <input type="text" id="rfc" name="rfc" x-model="formData.rfc" maxlength="13" required
        class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
        placeholder="Ingrese el RFC">
</div>


<div class="flex flex-wrap gap-4" id="ageCalculatorContainer">
    <!-- Fecha de nacimiento -->
    <div class="flex-1 min-w-[200px]">
        <label for="fecha_nacimiento" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
            Fecha de Nacimiento <span class="text-orange-500 text-lg font-semibold">*</span>
        </label>
        <div class="relative">
            <input 
                type="date" 
                id="fecha_nacimiento" 
                name="fecha_nacimiento" 
                x-model="formData.fecha_nacimiento" 
                required
                class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition cursor-pointer">
        </div>
    </div>

    <!-- Edad -->
    <div class="flex-1 min-w-[200px]">
        <label for="edad" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
            Edad <span class="text-orange-500 text-lg font-semibold">*</span>
        </label>
        <input 
            type="number" 
            id="edad" 
            name="edad" 
            x-model="formData.edad" 
            min="16" 
            max="60" 
            required
            class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
            placeholder="Edad">
    </div>
</div>


<!-- Sexo -->
<div>
    <label for="sexo" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Sexo <span class="text-orange-500 text-lg font-semibold">*</span></label>
    <select id="sexo" name="sexo" x-model="formData.sexo" required
        class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
        <option value="">Seleccione una opción</option>
        <option value="Masculino">Masculino</option>
        <option value="Femenino">Femenino</option>
        <option value="Otro">Otro</option>
    </select>
</div>

<!-- Teléfono -->
<div>
    <label for="telefono" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Teléfono</label>
    <input type="tel" id="telefono" name="telefono" x-model="formData.telefono"
        class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
        placeholder="Número de contacto">
</div>

<!-- Email -->
<div>
    <label for="email" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Email <span class="text-orange-500 text-lg font-semibold">*</span></label>
    <input type="email" id="email" name="email" x-model="formData.email" required
        class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
        placeholder="Correo electrónico">
</div>

<!-- Dirección -->
<div>
    <label for="direccion" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Dirección</label>
    <textarea id="direccion" name="direccion" rows="2" x-model="formData.direccion"
        class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
        placeholder="Ingrese la dirección"></textarea>
</div>

<!-- Estado físico -->
<div>
    <label for="estado_fisico" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Estado Físico <span class="text-orange-500 text-lg font-semibold">*</span></label>
    <select id="estado_fisico" name="estado_fisico" x-model="formData.estado_fisico" required
        class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
        <option value="Óptimo">Óptimo</option>
        <option value="Regular">Regular</option>
        <option value="Lesionado">Lesionado</option>
    </select>
</div>

<!-- Foto -->
<div>
    <label for="foto_url" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">
        Foto del Jugador
    </label>
    <input type="file" id="foto_url" name="foto_url" accept="image/jpeg,image/png,image/jpg,image"
        class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
    
    <!-- Información de requisitos -->
    <div class="mt-2 p-3 bg-gray-50 dark:bg-dark-600 rounded-md border border-gray-200 dark:border-dark-500">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-xs text-gray-600 dark:text-gray-400">
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                </svg>
                <span><strong>Formatos:</strong> JPEG, PNG, JPG</span>
            </div>
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12z" clip-rule="evenodd"/>
                </svg>
                <span><strong>Dimensiones:</strong> 400x600px recomendado</span>
            </div>
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                <span><strong>Tamaño máximo:</strong> 2MB</span>
            </div>
        </div>
    </div>
    
    <template x-if="editMode && editingJugador.foto_url">
        <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-200 dark:border-blue-800">
            <span class="text-sm text-blue-700 dark:text-blue-300 font-medium">Foto actual:</span>
            <div class="mt-2">
                <img :src="'/storage/' + editingJugador.foto_url" 
                     class="h-24 w-24 object-cover rounded-lg border-2 border-blue-200 dark:border-blue-700 shadow-sm">
            </div>
        </div>
    </template>
</div>
