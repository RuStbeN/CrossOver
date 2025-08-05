<nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-dark-900 shadow-sm border-b border-gray-200 dark:border-dark-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            
            <!-- Logo + Texto -->
            <div class="flex items-center">
                <a href="{{ route('arbitro.dashboard') }}" class="flex items-center">
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
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Árbitro Crossover MX</h1>
                    </div>
                </a>
            </div>

            <!-- Menú Principal simplificado -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('arbitro.dashboard') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-800 dark:text-white hover:text-primary-500 dark:hover:text-primary-400 transition-colors">
                    Mis Partidos
                </a>
                
                <a href="{{ route('arbitro.change-password') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-800 dark:text-white hover:text-primary-500 dark:hover:text-primary-400 transition-colors">
                    Cambiar Contraseña
                </a>
            </div>

            <!-- Dark Mode Toggle -->
            <div class="flex items-center ml-4">
                <button 
                    @click="toggleDarkMode()"
                    class="p-2 rounded-full focus:outline-none bg-gray-200 dark:bg-gray-700"
                >
                    <svg x-show="!darkMode" class="w-5 h-5" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="5" fill="#FFD700" stroke="#FFA500" stroke-width="1.5"/>
                        <g fill="none" stroke="#FFA500" stroke-width="1.5" stroke-linecap="round">
                            <path d="M12 3v2m0 14v2m9-9h-2M4 12H2m17.07-6.93l-1.41 1.41M6.34 17.66l-1.41 1.41m14.14 0l-1.41-1.41M6.34 6.34l-1.41-1.41" />
                            <path d="M12 5.5v1m0 11v1m7.5-7.5h-1m-13 0h-1m14.5-4.5l-.7.7M7.2 16.8l-.7.7m11.3 0l-.7-.7M7.2 7.2l-.7-.7" opacity="0.7"/>
                        </g>
                        <circle cx="12" cy="12" r="2" fill="#FFEE58"/>
                    </svg>
                    
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