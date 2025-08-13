<!DOCTYPE html>
<html lang="es" x-data="{ 
    darkMode: localStorage.getItem('darkMode') === 'true',
    notifications: [],
    
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        document.documentElement.classList.toggle('dark', this.darkMode);
        this.updateParticles();
    },
    
    updateParticles() {
        if (window.updateParticlesTheme) {
            window.updateParticlesTheme(this.darkMode);
        }
    },
    
    addNotification(notification) {
        this.notifications.push(notification);
        setTimeout(() => this.removeNotification(notification.id), 3000);
    },
    
    removeNotification(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    },
    
    showNotification(type, message) {
        this.addNotification({
            id: Date.now(),
            type: type,
            message: message
        });
    }
}" 
x-init="
    // Inicializar el modo oscuro al cargar la página
    document.documentElement.classList.toggle('dark', darkMode);
    
    // Exponer función globalmente para el sistema de notificaciones
    window.showNotification = (type, message) => {
        $data.showNotification(type, message);
    };
    
    // Inicializar partículas
    $nextTick(() => {
        if (window.updateParticlesTheme) {
            window.updateParticlesTheme($data.darkMode);
        }
    });
" 
:class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Árbitro - Crossover Mx')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    
    {{-- Meta tags para el sistema de notificaciones --}}
    @if($errors->any())
        <meta name="laravel-errors" content="{{ json_encode($errors->all()) }}">
    @endif
    
    @if(session('success'))
        <meta name="laravel-success" content="{{ session('success') }}">
    @endif
    
    @if(session('error'))
        <meta name="laravel-error" content="{{ session('error') }}">
    @endif
    
    @if(session('warning'))
        <meta name="laravel-warning" content="{{ session('warning') }}">
    @endif
    
    @if(session('info'))
        <meta name="laravel-info" content="{{ session('info') }}">
    @endif
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Tailwind y JS principal -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @stack('head')
</head>
<body class="bg-gray-200 dark:bg-dark-900 min-h-screen font-poppins">
    
    <!-- Partículas de fondo -->
    <div id="particles-container" class="fixed inset-0 overflow-hidden -z-10 bg-gray-200 dark:bg-dark-900">
        <!-- Partículas generadas por JS -->
    </div>

    <!-- Sistema de notificaciones -->
    <div class="fixed top-4 right-4 space-y-3 z-[9999]" 
        x-show="notifications.length > 0">
        <template x-for="notification in notifications" :key="notification.id">
            <div x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-6"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform translate-x-6"
                class="px-6 py-4 rounded-lg shadow-lg text-white cursor-pointer backdrop-blur-sm"
                :class="{
                    'bg-green-500/90 border border-green-400': notification.type === 'success',
                    'bg-red-500/90 border border-red-400': notification.type === 'error',
                    'bg-blue-500/90 border border-blue-400': notification.type === 'info',
                    'bg-yellow-500/90 border border-yellow-400': notification.type === 'warning'
                }"
                @click="removeNotification(notification.id)"
                x-text="notification.message">
            </div>
        </template>
    </div>

    <!-- Navbar específico para árbitros -->
    @include('components.nav.arbitro')

    <!-- Contenido principal -->
    <main class="min-h-screen pt-16">
        @yield('content')
    </main>

    <!-- Scripts adicionales de las vistas -->
    @stack('scripts')

    <!-- Script de inicialización de partículas (actualizado) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('particles-container');
            
            // Detectar modo oscuro desde localStorage
            const isDarkMode = localStorage.getItem('darkMode') === 'true';
            
            // Colores según modo
            const darkModeColors = ['bg-primary-400', 'bg-primary-500', 'bg-primary-600'];
            const lightModeColors = ['bg-blue-400', 'bg-blue-300', 'bg-blue-200'];
            let colors = isDarkMode ? darkModeColors : lightModeColors;
            
            // Animaciones disponibles
            const animations = ['animate-particle-float', 'animate-particle-drift', 'animate-pulse-slow'];
            
            // Generar partículas principales
            for (let i = 0; i < 80; i++) {
                const particle = document.createElement('div');
                const size = Math.random() * 8 + 2;
                const color = colors[Math.floor(Math.random() * colors.length)];
                const animation = animations[Math.floor(Math.random() * animations.length)];
                const delay = Math.random() * 15000;
                const opacity = (Math.random() * 0.4 + 0.3).toFixed(2);
                
                particle.className = `particle absolute ${color} ${animation} rounded-full`;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                particle.style.animationDelay = `${delay}ms`;
                particle.style.opacity = opacity;
                
                container.appendChild(particle);
            }
            
            // Generar partículas adicionales más pequeñas
            for (let i = 0; i < 40; i++) {
                const particle = document.createElement('div');
                const size = Math.random() * 4 + 1;
                const animation = 'animate-pulse-slow';
                const delay = Math.random() * 20000;
                const opacity = (Math.random() * 0.2 + 0.1).toFixed(2);
                
                particle.className = `particle absolute bg-white/20 ${animation} rounded-full`;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                particle.style.animationDelay = `${delay}ms`;
                particle.style.opacity = opacity;
                
                container.appendChild(particle);
            }
            
            //console.log('✅ Partículas de fondo inicializadas correctamente para árbitro');
        });

        // Función global para actualizar partículas cuando cambie el tema
        function updateParticlesTheme(isDark) {
            const particles = document.querySelectorAll('.particle');
            const darkColors = ['bg-primary-400', 'bg-primary-500', 'bg-primary-600'];
            const lightColors = ['bg-blue-400', 'bg-blue-300', 'bg-blue-200'];
            const allColors = [...darkColors, ...lightColors];
            
            particles.forEach(particle => {
                // Remover todos los colores existentes
                allColors.forEach(color => particle.classList.remove(color));
                
                // Skip partículas blancas
                if (particle.classList.contains('bg-white/20')) return;
                
                // Aplicar nuevos colores
                const newColors = isDark ? darkColors : lightColors;
                const randomColor = newColors[Math.floor(Math.random() * newColors.length)];
                particle.classList.add(randomColor);
            });
        }

        // Exponer función globalmente
        window.updateParticlesTheme = updateParticlesTheme;

        // Procesar notificaciones de Laravel al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // Procesar errores de validación
            const errorsTag = document.querySelector('meta[name="laravel-errors"]');
            if (errorsTag) {
                try {
                    const errors = JSON.parse(errorsTag.content);
                    errors.forEach(error => {
                        if (window.showNotification) {
                            window.showNotification('error', error);
                        }
                    });
                } catch (e) {
                    console.error('Error al procesar errores de Laravel:', e);
                }
            }

            // Procesar mensajes de éxito
            const successTag = document.querySelector('meta[name="laravel-success"]');
            if (successTag && window.showNotification) {
                window.showNotification('success', successTag.content);
            }

            // Procesar mensajes de error
            const errorTag = document.querySelector('meta[name="laravel-error"]');
            if (errorTag && window.showNotification) {
                window.showNotification('error', errorTag.content);
            }

            // Procesar mensajes de advertencia
            const warningTag = document.querySelector('meta[name="laravel-warning"]');
            if (warningTag && window.showNotification) {
                window.showNotification('warning', warningTag.content);
            }

            // Procesar mensajes de información
            const infoTag = document.querySelector('meta[name="laravel-info"]');
            if (infoTag && window.showNotification) {
                window.showNotification('info', infoTag.content);
            }
        });
    </script>
</body>
</html>