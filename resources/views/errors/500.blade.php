<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error | CrossoverMX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    },
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
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-reverse': 'float-reverse 5s ease-in-out infinite',
                        'pulse-slow': 'pulse 8s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-ball': 'bounce-ball 1s ease-in infinite',
                        'shoot': 'shoot 3s ease-in-out infinite',
                        'dribble': 'dribble 1.5s ease-in infinite',
                        'spin': 'spin 4s linear infinite',
                        'fade-in': 'fade-in 1s ease-out forwards',
                        'player-dunk': 'player-dunk 2.5s ease-in-out infinite',
                        'hoop-shake': 'hoop-shake 2.5s ease-in-out infinite',
                        'ball-spin': 'ball-spin 1s linear infinite',
                    },
                    keyframes: {
                        'float': {
                            '0%, 100%': { transform: 'translateY(0) rotate(0deg)' },
                            '50%': { transform: 'translateY(-20px) rotate(2deg)' },
                        },
                        'float-reverse': {
                            '0%, 100%': { transform: 'translateY(0) rotate(0deg)' },
                            '50%': { transform: 'translateY(15px) rotate(-2deg)' },
                        },
                        'bounce-ball': {
                            '0%, 100%': { transform: 'translateY(0) scale(1)' },
                            '50%': { transform: 'translateY(-50px) scale(0.9, 1.1)' },
                        },
                        'shoot': {
                            '0%': { transform: 'translateY(0) translateX(0) rotate(0deg)' },
                            '50%': { transform: 'translateY(-50px) translateX(30px) rotate(180deg)' },
                            '100%': { transform: 'translateY(0) translateX(0) rotate(360deg)' },
                        },
                        'dribble': {
                            '0%, 100%': { transform: 'translateY(0) scaleY(1)' },
                            '50%': { transform: 'translateY(-30px) scaleY(0.8)' },
                        },
                        'fade-in': {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        'player-dunk': {
                            '0%, 100%': { transform: 'translateY(0) scale(1)' },
                            '30%': { transform: 'translateY(-80px) scale(1.05)' },
                            '50%': { transform: 'translateY(-80px) scale(1.05)' },
                            '70%': { transform: 'translateY(0) scale(1)' },
                        },
                        'hoop-shake': {
                            '0%, 50%, 100%': { transform: 'translateX(0) rotate(0deg)' },
                            '40%': { transform: 'translateX(5px) rotate(2deg)' },
                            '60%': { transform: 'translateX(-5px) rotate(-2deg)' },
                        },
                        'ball-spin': {
                            '0%': { transform: 'rotate(0deg)' },
                            '100%': { transform: 'rotate(360deg)' },
                        }
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .text-gradient {
                @apply bg-clip-text text-transparent;
            }
            .glass-effect {
                backdrop-filter: blur(16px) saturate(180%);
                -webkit-backdrop-filter: blur(16px) saturate(180%);
                background-color: rgba(69, 69, 69, 0.3);
                border: 1px solid rgba(255, 107, 53, 0.2);
            }
            .basketball-pattern {
                background-image: radial-gradient(circle at 25% 25%, transparent 20%, rgba(255, 107, 53, 0.1) 20.5%, rgba(255, 107, 53, 0.1) 21%, transparent 21.5%);
                background-size: 30px 30px;
            }
            .basketball-texture {
                background-image: 
                    radial-gradient(circle at 30% 30%, transparent 20%, rgba(0,0,0,0.1) 20.5%, rgba(0,0,0,0.1) 21%, transparent 21.5%),
                    radial-gradient(circle at 70% 70%, transparent 20%, rgba(0,0,0,0.1) 20.5%, rgba(0,0,0,0.1) 21%, transparent 21.5%),
                    radial-gradient(circle at 50% 20%, transparent 20%, rgba(0,0,0,0.1) 20.5%, rgba(0,0,0,0.1) 21%, transparent 21.5%),
                    radial-gradient(circle at 20% 50%, transparent 20%, rgba(0,0,0,0.1) 20.5%, rgba(0,0,0,0.1) 21%, transparent 21.5%);
                background-size: 100% 100%;
            }
        }
    </style>
</head>
<body class="font-poppins bg-dark-900 overflow-hidden">
    <!-- Background elements -->
    <div class="absolute inset-0 overflow-hidden basketball-pattern">
        <div class="absolute top-10 left-20 w-64 h-64 bg-primary-500 rounded-full filter blur-[90px] opacity-20 mix-blend-multiply animate-pulse-slow"></div>
        <div class="absolute bottom-20 right-20 w-80 h-80 bg-primary-600 rounded-full filter blur-[100px] opacity-20 mix-blend-multiply animate-pulse-slow delay-300"></div>
        <div class="absolute top-1/3 right-1/4 w-40 h-40 bg-primary-400 rounded-full filter blur-[80px] opacity-15 mix-blend-multiply animate-pulse-slow delay-500"></div>
    </div>

    <!-- Basketball court -->
    <div class="absolute inset-0 pointer-events-none">
        <!-- Court lines -->
        <div class="absolute bottom-0 left-0 w-full h-1 bg-primary-500/20"></div>
        <div class="absolute top-1/2 left-0 w-full h-0.5 bg-primary-400/15"></div>
        <div class="absolute top-0 right-1/2 w-0.5 h-full bg-primary-500/10"></div>
        
        <!-- 3-point line -->
        <div class="absolute bottom-0 left-1/2 w-64 h-64 border border-primary-500/20 rounded-full transform -translate-x-1/2" style="clip-path: polygon(0 100%, 100% 100%, 100% 50%, 0 50%)"></div>
        
        <!-- Key area -->
        <div class="absolute bottom-0 left-1/2 w-32 h-48 border border-primary-500/20 transform -translate-x-1/2" style="clip-path: polygon(0 100%, 100% 100%, 100% 50%, 0 50%)"></div>
    </div>

    <!-- Animated basketball player dunking -->
    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-64 h-64 z-0 opacity-20">
        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-32 h-64 animate-player-dunk">
            <!-- Player silhouette -->
            <div class="relative w-full h-full">
                <!-- Head -->
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-8 h-8 bg-dark-700 rounded-full"></div>
                <!-- Body -->
                <div class="absolute top-8 left-1/2 transform -translate-x-1/2 w-6 h-16 bg-dark-600"></div>
                <!-- Arms -->
                <div class="absolute top-8 left-1/2 transform -translate-x-1/2 w-16 h-4 bg-dark-600 rounded-full" style="clip-path: polygon(0% 50%, 100% 50%, 100% 100%, 0% 100%)"></div>
                <!-- Legs -->
                <div class="absolute top-24 left-1/2 transform -translate-x-1/2 w-6 h-16 bg-dark-700 rounded-b-full"></div>
                <!-- Ball in hand -->
                <div class="absolute top-4 left-1/2 transform -translate-x-1/2 w-6 h-6 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full basketball-texture animate-ball-spin"></div>
            </div>
        </div>
        
        <!-- Basketball hoop -->
        <div class="absolute top-20 right-1/2 transform translate-x-1/2 w-24 h-24 animate-hoop-shake">
            <svg viewBox="0 0 100 100" class="w-full h-full text-primary-500 opacity-70" fill="currentColor">
                <rect x="10" y="30" width="80" height="4" rx="2"/>
                <rect x="15" y="34" width="70" height="2" rx="1"/>
                <path d="M20 36 L20 50 L25 55 L75 55 L80 50 L80 36" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
        </div>
    </div>

    <!-- Main content -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative z-10 w-full max-w-2xl">
            <!-- Card with glass effect -->
            <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden transition-all duration-500 hover:shadow-primary-500/20 animate-fade-in">
                <div class="p-12 text-center">
                    
                    <!-- Animated Basketball -->
                    <div class="relative mx-auto mb-8 w-32 h-32">
                        <!-- Balón con gradiente naranja (usando tus colores primary) -->
                        <div class="w-full h-full bg-gradient-to-br from-primary-500 to-primary-700 rounded-full shadow-2xl animate-bounce-ball relative overflow-hidden">
                            
                            <!-- Línea central horizontal (gruesa) -->
                            <div class="absolute top-1/2 left-0 w-full h-1.5 bg-dark-800 opacity-80 transform -translate-y-1/2"></div>
                            
                            <!-- Línea central vertical (gruesa) -->
                            <div class="absolute top-0 left-1/2 w-1.5 h-full bg-dark-800 opacity-80 transform -translate-x-1/2"></div>
                            
                            <!-- Líneas curvas (simulando las "costuras" del balón) -->
                            <div class="absolute top-0 left-0 w-full h-full">
                                <!-- Curva superior izquierda -->
                                <div class="absolute top-1/4 left-1/4 w-1/2 h-1/2 border-2 border-dark-800 opacity-70 rounded-full transform -translate-x-1/2 -translate-y-1/2"></div>
                                
                                <!-- Curva inferior derecha -->
                                <div class="absolute bottom-1/4 right-1/4 w-1/2 h-1/2 border-2 border-dark-800 opacity-70 rounded-full transform translate-x-1/2 translate-y-1/2"></div>
                                
                                <!-- Curva superior derecha (espejo) -->
                                <div class="absolute top-1/4 right-1/4 w-1/2 h-1/2 border-2 border-dark-800 opacity-70 rounded-full transform translate-x-1/2 -translate-y-1/2"></div>
                                
                                <!-- Curva inferior izquierda (espejo) -->
                                <div class="absolute bottom-1/4 left-1/4 w-1/2 h-1/2 border-2 border-dark-800 opacity-70 rounded-full transform -translate-x-1/2 translate-y-1/2"></div>
                            </div>
                        </div>
                        
                        <!-- Sombra animada -->
                        <div class="absolute -bottom-4 left-1/2 w-24 h-6 bg-dark-800/30 rounded-full filter blur-sm transform -translate-x-1/2 animate-pulse-slow"></div>
                    </div>

                    <!-- Logo and header -->
                    <div class="mb-8">
                        <h1 class="text-5xl font-bold text-gradient bg-gradient-to-r from-primary-400 via-primary-500 to-primary-600 mb-4">
                            CrossoverMX
                        </h1>
                        <p class="text-gray-300 text-lg">Basketball League Management</p>
                    </div>

                    <!-- Error message -->
                    <div class="mb-8">
                        <div class="text-8xl font-bold text-primary-500 mb-4 opacity-80">500</div>
                        <h2 class="text-3xl font-bold text-white mb-4">
                            ¡Ups! Problema técnico en la cancha
                        </h2>
                        <p class="text-gray-300 text-lg mb-2">
                            Estamos experimentando algunas dificultades técnicas.
                        </p>
                        <p class="text-gray-400 text-base">
                            Nuestro equipo técnico está trabajando para resolver el problema.
                        </p>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <button onclick="location.reload()" 
                            class="group relative flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-800 shadow-lg transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2 group-hover:animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Intentar nuevamente
                        </button>
                        
                        <button onclick="window.history.back()" 
                            class="group relative flex items-center justify-center px-8 py-3 border border-primary-500 text-base font-medium rounded-lg text-primary-400 bg-transparent hover:bg-primary-500/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-800 transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Regresar
                        </button>
                    </div>

                    <!-- Additional info -->
                    <div class="mt-8 pt-6 border-t border-dark-600">
                        <p class="text-gray-400 text-sm">
                            Código de error: <span class="text-primary-400 font-mono">BKT-500</span>
                        </p>
                        <p class="text-gray-500 text-xs mt-2">
                            Si el problema persiste, contacta al soporte técnico
                        </p>
                    </div>
                </div>
            </div>

            <!-- Floating decoration elements -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary-500/20 rounded-full filter blur-xl opacity-70 animate-float"></div>
            <div class="absolute -bottom-5 -left-5 w-24 h-24 bg-primary-400/20 rounded-full filter blur-xl opacity-70 animate-float-reverse"></div>
        </div>
    </div>

    <!-- Auto-refresh script -->
    <script>
        // Auto-refresh después de 30 segundos
        setTimeout(() => {
            const refreshBtn = document.querySelector('button[onclick="location.reload()"]');
            if (refreshBtn) {
                refreshBtn.click();
            }
        }, 30000);

        // Efecto de partículas de basketball
        function createParticle() {
            const particle = document.createElement('div');
            particle.className = 'absolute w-2 h-2 bg-primary-500/30 rounded-full animate-float';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 3 + 's';
            document.body.appendChild(particle);
            
            setTimeout(() => {
                particle.remove();
            }, 6000);
        }

        // Crear partículas cada 3 segundos
        setInterval(createParticle, 3000);
    </script>
</body>
</html>