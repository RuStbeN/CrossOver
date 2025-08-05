<!-- Sección de Contacto de Emergencia -->
<div class="md:col-span-2 border-t border-dark-700 pt-4 mt-2">
    <h3 class="text-lg font-medium mb-4 dark:text-gray-300 text-dark-800">Contacto de Emergencia</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Nombre contacto emergencia -->
        <div>
            <label for="contacto_emergencia_nombre" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Nombre Completo <span class="text-orange-500 text-lg font-semibold">*</span></label>
            <input type="text" id="contacto_emergencia_nombre" name="contacto_emergencia_nombre" 
                   x-model="formData.contacto_emergencia_nombre" required
                   class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                   placeholder="Nombre del contacto"
                   maxlength="100">
        </div>

        <!-- Teléfono contacto emergencia -->
        <div>
            <label for="contacto_emergencia_telefono" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Teléfono <span class="text-orange-500 text-lg font-semibold">*</span> </label>
            <input type="tel" id="contacto_emergencia_telefono" name="contacto_emergencia_telefono"
                   x-model="formData.contacto_emergencia_telefono" required
                   class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                   placeholder="Número de contacto"
                   pattern="[0-9]{10}"
                   maxlength="10">
        </div>

        <!-- Relación contacto emergencia -->
        <div>
            <label for="contacto_emergencia_relacion" class="block text-sm font-medium mb-1 dark:text-gray-300 text-dark-800">Relación <span class="text-orange-500 text-lg font-semibold">*</span></label>
            <select id="contacto_emergencia_relacion" name="contacto_emergencia_relacion" 
                    x-model="formData.contacto_emergencia_relacion" required
                    class="w-full bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-md px-3 py-2 text-black dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                <option value="">Seleccione relación</option>
                <option value="Padre">Padre</option>
                <option value="Madre">Madre</option>
                <option value="Hermano/a">Hermano/a</option>
                <option value="Cónyuge">Cónyuge</option>
                <option value="Tutor">Tutor</option>
                <option value="Otro">Otro</option>
            </select>
        </div>
    </div>
</div>