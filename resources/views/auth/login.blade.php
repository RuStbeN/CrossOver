<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Crossover Mx')</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">

    <!-- Carga los estilos mediante Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>
<body class="font-poppins bg-dark-900 overflow-hidden">

    <!-- Background elements container -->
    <div class="absolute inset-0 overflow-hidden">

    </div>


    <!-- Main content -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative z-10 w-full max-w-md">
            <!-- Card with glass effect -->
            <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden transition-all duration-500 hover:shadow-primary-500/20">
                <div class="p-10">

                    <!-- Logo and header -->
                    <div class="text-center mb-10">
                        <!-- Logo container -->
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg overflow-hidden">
                            <!-- Imagen del logo -->
                            <img src="{{ asset('logo.png') }}" alt="Logo CrossoverMX" class="w-full h-full object-contain">
                        </div>
                        <h1 class="text-4xl font-bold mb-2 bg-gradient-to-r from-primary-400 via-primary-500 to-primary-600 bg-clip-text text-transparent">
                            CrossoverMX
                        </h1>
                        <p class="text-gray-300">Basketball League Management</p>
                        <p class="text-gray-400 text-sm mt-1">Accede a tu cuenta</p>
                    </div>

                    <!-- Login form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf
                        @error('email')
                            <div class="mb-4 p-4 rounded-lg text-white bg-gradient-to-r from-primary-500 to-primary-600 shadow-lg">
                                {{ $message }}
                            </div>
                        @enderror
                        <!-- Email field -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Correo electrónico</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required 
                                    class="block w-full pl-10 pr-3 py-3 bg-dark-800/70 border border-dark-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200"
                                    placeholder="tu@email.com">
                            </div>
                        </div>

                        <!-- Password field -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="password" class="block text-sm font-medium text-gray-300">Contraseña</label>
                                <a href="#" class="text-xs text-primary-400 hover:text-primary-300 transition-colors">¿Olvidaste tu contraseña?</a>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input id="password" name="password" type="password" autocomplete="current-password" required 
                                    class="block w-full pl-10 pr-3 py-3 bg-dark-800/70 border border-dark-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <!-- Remember me -->
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" 
                                class="h-4 w-4 bg-dark-800 border-dark-600 rounded focus:ring-primary-500 text-primary-600 transition duration-200">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-300">Recuérdame</label>
                        </div>

                        <!-- Submit button -->
                        <div>
                            <button type="submit" 
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 focus:ring-offset-dark-800 shadow-lg transition-all duration-300 transform hover:scale-[1.02]">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-primary-200 group-hover:text-primary-100 transition duration-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                Iniciar sesión
                            </button>
                        </div>
                    </form>

                    <!-- Social login -->
                    <div class="mt-8">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-dark-600"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-dark-900 text-gray-400">Visita nuestras redes</span>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-3 gap-3">
                            <div>
                                <a href="#" class="w-full inline-flex justify-center py-2 px-4 bg-dark-800/70 rounded-lg border border-dark-600 hover:bg-dark-700/50 hover:border-primary-500 transition duration-200">
                                    <svg class="w-5 h-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            </div>

                            <div>
                                <a href="#" class="w-full inline-flex justify-center py-2 px-4 bg-dark-800/70 rounded-lg border border-dark-600 hover:bg-dark-700/50 hover:border-primary-500 transition duration-200">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" aria-hidden="true">
                                        <defs>
                                            <linearGradient id="instagram-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                                <stop offset="0%" stop-color="#833AB4" />
                                                <stop offset="50%" stop-color="#FD1D1D" />
                                                <stop offset="100%" stop-color="#FCB045" />
                                            </linearGradient>
                                        </defs>
                                        <path 
                                            fill="url(#instagram-gradient)" 
                                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"
                                        />
                                    </svg>
                                </a>
                            </div>

                            <div>
                                <a href="#" class="w-full inline-flex justify-center py-2 px-4 bg-dark-800/70 rounded-lg border border-dark-600 hover:bg-dark-700/50 hover:border-primary-500 transition duration-200">
                                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating decoration elements around card -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary-500/20 rounded-full filter blur-xl opacity-70 animate-login-float"></div>
            <div class="absolute -bottom-5 -left-5 w-24 h-24 bg-primary-400/20 rounded-full filter blur-xl opacity-70 animate-login-float-reverse"></div>
        </div>
    </div>

    @stack('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.querySelector('.absolute.inset-0.overflow-hidden');
    if (!container) return;

    // Configuración de estilos con tamaños más grandes
    const config = {
        elements: [
            {
                type: 'blur-circle', // Fondos difuminados grandes (aumentados)
                classes: 'rounded-full filter blur-[90px] opacity-20 mix-blend-multiply animate-login-pulse',
                sizes: [120, 160, 200], // w/h en px (antes: 64-80)
                colors: ['bg-primary-400', 'bg-primary-500', 'bg-primary-600']
            },
            {
                type: 'square', // Cuadrados grandes
                classes: 'bg-primary-500/10 rounded-lg rotate-12 animate-login-float',
                sizes: [32, 40, 48, 64], // w/h en px (antes: 12-20)
                colors: ['bg-primary-400/10', 'bg-primary-500/10', 'bg-primary-600/10']
            },
            {
                type: 'circle', // Círculos grandes
                classes: 'bg-white/5 rounded-full animate-login-float-reverse',
                sizes: [24, 32, 40, 48], // w/h en px (antes: 10-18)
                colors: ['bg-white/5', 'bg-primary-300/10', 'bg-primary-400/12']
            },
            {
                type: 'rotating-bar', // Barras más largas
                classes: 'bg-primary-400/20 rounded-full animate-login-rotate',
                sizes: [80, 100, 120], // width en px (antes: 8-16)
                colors: ['bg-primary-400/20', 'bg-primary-500/20']
            }
        ],
        animations: [
            'animate-login-float',
            'animate-login-float-reverse',
            'animate-login-float-slow',
            'animate-login-drift'
        ],
        delays: [0, 300, 500, 700, 1000, 1500]
    };

    const generateElement = (type) => {
        const elementConfig = config.elements.find(e => e.type === type);
        if (!elementConfig) return;

        const size = elementConfig.sizes[Math.floor(Math.random() * elementConfig.sizes.length)];
        const color = elementConfig.colors[Math.floor(Math.random() * elementConfig.colors.length)];
        const delay = config.delays[Math.floor(Math.random() * config.delays.length)];
        const animation = type === 'rotating-bar' 
            ? 'animate-login-rotate' 
            : config.animations[Math.floor(Math.random() * config.animations.length)];

        const element = document.createElement('div');
        element.className = `login-element absolute ${color} ${elementConfig.classes} ${animation} delay-${delay}`;

        if (type === 'rotating-bar') {
            element.style.width = `${size}px`;
            element.style.height = '2px';
        } else {
            element.style.width = `${size}px`;
            element.style.height = `${size}px`;
        }

        // Posición aleatoria evitando el centro (área del formulario)
        let left, top;
        do {
            left = Math.random() * 90;
            top = Math.random() * 90;
        } while (left > 30 && left < 70 && top > 30 && top < 70);

        element.style.left = `${left}%`;
        element.style.top = `${top}%`;

        container.appendChild(element);
    };

    // Generación de elementos (cantidades ajustadas)
    for (let i = 0; i < 4; i++) generateElement('blur-circle');   // 4 fondos grandes
    for (let i = 0; i < 8; i++) generateElement('square');        // 8 cuadrados grandes
    for (let i = 0; i < 8; i++) generateElement('circle');        // 8 círculos grandes
    for (let i = 0; i < 3; i++) generateElement('rotating-bar');  // 3 barras largas

    // Movimiento suave cada 20 segundos (opcional)
    setInterval(() => {
        document.querySelectorAll('.login-element').forEach(el => {
            const newLeft = Math.random() * 90;
            const newTop = Math.random() * 90;
            el.style.transition = 'left 20s ease, top 20s ease';
            el.style.left = `${newLeft}%`;
            el.style.top = `${newTop}%`;
        });
    }, 20000);

    //console.log('✨ Elementos decorativos grandes generados');
});
</script>

</body>
</html>