<!-- Modal para Faltas -->
<div id="modalFaltas" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden z-50 transition-all duration-300">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl w-full max-w-lg transform transition-all duration-300 scale-95 opacity-0 modal-content border border-gray-200 dark:border-dark-600">
            
            <!-- Header con icono -->
            <div class="relative bg-gradient-to-r from-yellow-500 to-red-600 p-6 rounded-t-2xl text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Registrar Falta</h3>
                            <p class="text-white/80 text-sm">Selecciona el tipo de falta</p>
                        </div>
                    </div>
                    <button onclick="cerrarModal('modalFaltas')" class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-full transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Efecto decorativo -->
                <div class="absolute -bottom-1 left-0 w-full h-4 bg-gradient-to-b from-transparent to-white dark:to-dark-800"></div>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4">
                <!-- Falta Personal -->
                <button onclick="registrarEstadistica(modalData.jugadorId, modalData.tipoEquipo, 'falta_personal')" 
                        class="group w-full bg-gradient-to-r from-yellow-400 to-yellow-600 hover:from-yellow-500 hover:to-yellow-700 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-300 hover:scale-[1.02] border-2 border-yellow-300 hover:border-yellow-400 relative overflow-hidden">
                    <!-- Efecto de brillo -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-yellow-200 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="font-bold text-lg">Falta Personal</p>
                                <p class="text-yellow-100 text-sm">Contacto ilegal con oponente</p>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-200 group-hover:translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>

                <!-- Falta Técnica -->
                <button onclick="registrarEstadistica(modalData.jugadorId, modalData.tipoEquipo, 'falta_tecnica')" 
                        class="group w-full bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-300 hover:scale-[1.02] border-2 border-orange-400 hover:border-red-500 relative overflow-hidden">
                    <!-- Efecto de brillo -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-orange-200 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="font-bold text-lg">Falta Técnica</p>
                                <p class="text-orange-100 text-sm">Conducta antideportiva</p>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-200 group-hover:translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>

                <!-- Falta Descalificante -->
                <button onclick="registrarEstadistica(modalData.jugadorId, modalData.tipoEquipo, 'falta_descalificante')" 
                        class="group w-full bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 text-white p-4 rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-300 hover:scale-[1.02] border-2 border-red-500 hover:border-red-600 relative overflow-hidden">
                    <!-- Efecto de brillo -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-red-200 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="font-bold text-lg">Falta Descalificante</p>
                                <p class="text-red-100 text-sm">Expulsión del partido</p>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-200 group-hover:translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>
            </div>

            <!-- Footer -->
            <div class="px-6 pb-6">
                <button onclick="cerrarModal('modalFaltas')" 
                        class="w-full bg-gray-100 dark:bg-dark-700 hover:bg-gray-200 dark:hover:bg-dark-600 text-gray-800 dark:text-gray-200 px-6 py-3 rounded-xl font-medium transition-all duration-200 border border-gray-300 dark:border-dark-600 hover:border-gray-400 dark:hover:border-dark-500">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Animación de entrada del modal */
    #modalFaltas:not(.hidden) .modal-content {
        animation: modalEnter 0.3s ease-out forwards;
    }

    @keyframes modalEnter {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    /* Efecto de backdrop blur */
    #modalFaltas:not(.hidden) {
        backdrop-filter: blur(4px);
    }
</style>