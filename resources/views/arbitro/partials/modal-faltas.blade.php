<!-- Modal para Faltas -->
    <div id="modalFaltas" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-dark-800 rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Tipo de Falta</h3>
                <div class="space-y-3">
                    <button onclick="registrarEstadistica(modalData.jugadorId, modalData.tipoEquipo, 'falta_personal')" 
                            class="w-full bg-yellow-500 text-white p-3 rounded">Falta Personal</button>
                    <button onclick="registrarEstadistica(modalData.jugadorId, modalData.tipoEquipo, 'falta_tecnica')" 
                            class="w-full bg-red-500 text-white p-3 rounded">Falta Técnica</button>
                    <button onclick="registrarEstadistica(modalData.jugadorId, modalData.tipoEquipo, 'falta_descalificante')" 
                            class="w-full bg-red-700 text-white p-3 rounded">Falta Descalificante</button>
                </div>
                <button onclick="cerrarModal('modalFaltas')" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            </div>
        </div>
    </div>