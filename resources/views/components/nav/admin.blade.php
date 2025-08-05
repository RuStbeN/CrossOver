<nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-dark-900 shadow-sm border-b border-gray-200 dark:border-dark-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            
            <!-- Logo + Texto -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center overflow-hidden">
                            <img 
                                src="{{ asset('logo.png') }}" 
                                alt="Logo" 
                                class="w-full h-full object-contain"
                            >
                        </div>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Crossover MX</h1>
                    </div>
                </a>
            </div>

            <!-- Menú Principal -->
            <div class="hidden md:flex items-center space-x-1">

                <!-- Registros -->
                <div class="relative group">
                    <button class="px-4 py-2 text-sm font-medium text-gray-800 dark:text-white hover:text-primary-500 dark:hover:text-primary-400 transition-colors flex items-center">
                        Registros
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute left-0 mt-2 w-56 bg-white dark:bg-dark-800 rounded-lg shadow-xl border border-gray-200 dark:border-dark-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-30 transform group-hover:translate-y-0 -translate-y-1">
                        <!-- Jugadores -->
                        <a href="{{ route('jugadores.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-dark-700 border-b border-gray-100 dark:border-dark-700 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                Jugadores
                            </div>
                        </a>

                        <!-- Entrenadores -->
                        <a href="{{ route('entrenadores.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-dark-700 border-b border-gray-100 dark:border-dark-700 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                Entrenadores
                            </div>
                        </a>
                        
                        <!-- Equipos -->
                        <a href="{{ route('equipos.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-dark-700 border-b border-gray-100 dark:border-dark-700 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                Equipos
                            </div>
                        </a>
                        
                        <!-- Ligas -->
                        <a href="{{ route('ligas.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-dark-700 border-b border-gray-100 dark:border-dark-700 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Ligas
                            </div>
                        </a>
                        
                        <!-- Categorías -->
                        <a href="{{ route('categorias.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-dark-700 border-b border-gray-100 dark:border-dark-700 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                                </svg>
                                Categorías
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Gestor deportivo -->
                <div class="relative group">
                    <button class="px-4 py-2 text-sm font-medium text-gray-800 dark:text-white hover:text-primary-500 dark:hover:text-primary-400 transition-colors flex items-center">
                        Gestor deportivo
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute left-0 mt-2 w-56 bg-white dark:bg-dark-800 rounded-lg shadow-xl border border-gray-200 dark:border-dark-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-30 transform group-hover:translate-y-0 -translate-y-1">
                        <!-- Árbitros -->
                        <a href="{{ route('arbitros.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-dark-700 border-b border-gray-100 dark:border-dark-700 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                Árbitros
                            </div>
                        </a>
                        
                        <!-- Canchas -->
                        <a href="{{ route('canchas.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-dark-700 border-b border-gray-100 dark:border-dark-700 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Canchas
                            </div>
                        </a>
                        
                        <!-- Temporadas -->
                        <a href="{{ route('temporadas.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-dark-700 border-b border-gray-100 dark:border-dark-700 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Temporadas
                            </div>
                        </a>
                        
                        <!-- Torneos -->
                        <a href="{{ route('torneos.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-dark-700 border-b border-gray-100 dark:border-dark-700 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                </svg>
                                Torneos
                            </div>
                        </a>
                        
                        <!-- Juegos -->
                        <a href="{{ route('juegos.index') }}"  class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-orange-50 dark:hover:bg-dark-700 transition-colors duration-150">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Juegos
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- Expediente clínico -->
                <div class="relative group">
                    <button class="px-4 py-2 text-sm font-medium text-gray-800 dark:text-white hover:text-primary-500 dark:hover:text-primary-400 transition-colors flex items-center">
                        Expediente clínico
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>

            </div>

            <!-- Dark Mode Toggle -->
            <div class="flex items-center ml-4">
                <button 
                    @click="toggleDarkMode()"
                    class="p-2 rounded-full focus:outline-none bg-gray-200 dark:bg-gray-700"
                >
                    <!-- Sol amarillo vibrante con rayos ondulados -->
                    <svg x-show="!darkMode" class="w-5 h-5" viewBox="0 0 24 24">
                        <!-- Fondo del sol (amarillo brillante) -->
                        <circle cx="12" cy="12" r="5" fill="#FFD700" stroke="#FFA500" stroke-width="1.5"/>
                        
                        <!-- Rayos largos ondulados -->
                        <g fill="none" stroke="#FFA500" stroke-width="1.5" stroke-linecap="round">
                            <path d="M12 3v2m0 14v2m9-9h-2M4 12H2m17.07-6.93l-1.41 1.41M6.34 17.66l-1.41 1.41m14.14 0l-1.41-1.41M6.34 6.34l-1.41-1.41" />
                            
                            <!-- Rayos decorativos adicionales (más cortos) -->
                            <path d="M12 5.5v1m0 11v1m7.5-7.5h-1m-13 0h-1m14.5-4.5l-.7.7M7.2 16.8l-.7.7m11.3 0l-.7-.7M7.2 7.2l-.7-.7" opacity="0.7"/>
                        </g>
                        
                        <!-- Destello central -->
                        <circle cx="12" cy="12" r="2" fill="#FFEE58"/>
                    </svg>
                    
                    <!-- Icono de luna (se mantiene igual) -->
                    <svg x-show="darkMode" class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>
            </div>

            <!-- Perfil de Usuario -->
            <div class="flex items-center">
                <div class="relative group">
                    <button class="flex items-center space-x-2 focus:outline-none">
                        <span class="text-sm font-medium text-gray-800 dark:text-white">
                            {{ Auth::user()->name }}
                        </span>
                        <div class="w-8 h-8 rounded-full bg-primary-500 dark:bg-primary-600 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </button>

                    <!-- Menú Desplegable -->
                    <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-gray-200 dark:border-dark-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-dark-700">
                            Mi perfil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-dark-700">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>