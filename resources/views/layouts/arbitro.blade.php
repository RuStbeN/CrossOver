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
        const particles = document.querySelectorAll('.particle');
        particles.forEach(particle => {
            if (this.darkMode) {
                particle.classList.remove('bg-blue-400', 'bg-blue-300');
                particle.classList.add('bg-primary-400', 'bg-primary-500');
            } else {
                particle.classList.remove('bg-primary-400', 'bg-primary-500');
                particle.classList.add('bg-blue-400', 'bg-blue-300');
            }
        });
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
    $watch('darkMode', val => {
        localStorage.setItem('darkMode', val);
        document.documentElement.classList.toggle('dark', val);
        updateParticles();
    });
    updateParticles();
    
    // Exponer función globalmente
    window.showNotification = (type, message) => {
        showNotification(type, message);
    };
" 
:class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Árbitro - Crossover Mx')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'primary': {
                            '50': '#fff8f5',
                            '100': '#fff0e5',
                            '200': '#ffd9bf',
                            '300': '#ffb58a',
                            '400': '#ff8a54',
                            '500': '#ff6b35',
                            '600': '#e65020',
                            '700': '#bf3a15',
                            '800': '#992b12',
                            '900': '#7a220e',
                        },
                        'dark': {
                            '50': '#f6f6f6',
                            '100': '#e7e7e7',
                            '200': '#d1d1d1',
                            '300': '#b0b0b0',
                            '400': '#888888',
                            '500': '#6d6d6d',
                            '600': '#5d5d5d',
                            '700': '#4f4f4f',
                            '800': '#454545',
                            '900': '#292929',
                        },
                        blue: {
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                        },
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 8s ease-in-out infinite',
                        'float-reverse': 'float-reverse 7s ease-in-out infinite',
                        'float-slow': 'float-slow 12s ease-in-out infinite',
                    },
                    keyframes: {
                        'float': {
                            '0%, 100%': { transform: 'translateY(0) translateX(0)' },
                            '50%': { transform: 'translateY(-100px) translateX(50px)' },
                        },
                        'float-reverse': {
                            '0%, 100%': { transform: 'translateY(0) translateX(0)' },
                            '50%': { transform: 'translateY(100px) translateX(-50px)' },
                        },
                        'float-slow': {
                            '0%, 100%': { transform: 'translateY(0) translateX(0) scale(1)' },
                            '33%': { transform: 'translateY(-80px) translateX(40px) scale(1.1)' },
                            '66%': { transform: 'translateY(60px) translateX(-30px) scale(0.9)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Fuentes y Alpine JS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js" defer></script>

    <!-- Estilos personalizados -->
    <style type="text/tailwindcss">
        @layer utilities {
            .text-gradient { @apply bg-clip-text text-transparent; }
            .glass-effect {
                backdrop-filter: blur(16px) saturate(180%);
                -webkit-backdrop-filter: blur(16px) saturate(180%);
                background-color: rgba(255, 255, 255, 0.8);
                border: 1px solid rgba(0, 0, 0, 0.1);
            }
            .dark .glass-effect {
                background-color: rgba(30, 41, 59, 0.8);
                border: 1px solid rgba(255, 107, 53, 0.3);
            }
            .particle {
                @apply absolute rounded-full pointer-events-none;
            }
        }
    </style>
    @stack('head')
</head>

<body class="bg-gray-200 dark:bg-dark-900 min-h-screen">
    <!-- Partículas de fondo -->
    <div id="particles-container" class="fixed inset-0 overflow-hidden -z-10 bg-gray-200 dark:bg-dark-900">
        <!-- Partículas generadas por JS -->
    </div>

    <!-- Notificaciones globales -->
    <div class="fixed top-4 right-4 space-y-3 z-[9999]" 
        x-show="notifications.length > 0">
        <template x-for="notification in notifications" :key="notification.id">
            <div x-transition
                class="px-6 py-4 rounded-md shadow-lg text-white cursor-pointer"
                :class="{
                    'bg-green-500': notification.type === 'success',
                    'bg-red-500': notification.type === 'error'
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

    @stack('scripts')

    <!-- Script de inicialización de partículas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('particles-container');
            const isDarkMode = localStorage.getItem('darkMode') === 'true';
            
            // Colores según modo
            const colors = isDarkMode 
                ? ['bg-primary-400', 'bg-primary-500', 'bg-primary-600']
                : ['bg-blue-400', 'bg-blue-300', 'bg-blue-200'];

            // Generar partículas
            for (let i = 0; i < 100; i++) {
                const particle = document.createElement('div');
                const size = `${Math.random() * 0.5 + 0.25}rem`;
                const color = colors[Math.floor(Math.random() * colors.length)];
                
                particle.className = `particle ${color} animate-float opacity-70`;
                particle.style.width = size;
                particle.style.height = size;
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                particle.style.animationDelay = `${Math.random() * 10}s`;
                
                container.appendChild(particle);
            }
        });
    </script>
</body>
</html>