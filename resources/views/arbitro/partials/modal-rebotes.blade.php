<!-- Modal para Rebotes -->
    <div id="modalRebotes" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-dark-800 rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Tipo de Rebote</h3>
                <div class="space-y-3">
                    <button onclick="registrarEstadistica(modalData.jugadorId, modalData.tipoEquipo, 'rebote_defensivo')" 
                            class="w-full bg-blue-500 text-white p-3 rounded">Rebote Defensivo</button>
                    <button onclick="registrarEstadistica(modalData.jugadorId, modalData.tipoEquipo, 'rebote_ofensivo')" 
                            class="w-full bg-orange-500 text-white p-3 rounded">Rebote Ofensivo</button>
                </div>
                <button onclick="cerrarModal('modalRebotes')" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
            </div>
        </div>
    </div>