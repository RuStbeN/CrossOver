@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 space-y-6">
    <!-- Título -->
    <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-gray-200 dark:border-dark-700 p-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Configuración de Perfil</h1>
        <p class="text-gray-600 dark:text-gray-300 mt-2">Actualiza tu información personal y configuración de seguridad</p>
    </div>

    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Información del Perfil -->
    <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-gray-200 dark:border-dark-700">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Información Personal</h2>
            
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nombre completo
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $user->name) }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-dark-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-dark-700 text-gray-800 dark:text-white"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Correo electrónico
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-dark-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-dark-700 text-gray-800 dark:text-white"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Información adicional -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fecha de registro
                        </label>
                        <input type="text" 
                               value="{{ $user->created_at->format('d/m/Y H:i') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-dark-600 rounded-lg bg-gray-100 dark:bg-dark-600 text-gray-800 dark:text-white"
                               readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Última actualización
                        </label>
                        <input type="text" 
                               value="{{ $user->updated_at->format('d/m/Y H:i') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-dark-600 rounded-lg bg-gray-100 dark:bg-dark-600 text-gray-800 dark:text-white"
                               readonly>
                    </div>
                </div>

                <!-- Botón de actualizar -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Actualizar Información
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cambiar Contraseña -->
    <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-gray-200 dark:border-dark-700">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Cambiar Contraseña</h2>
            
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Mantener los datos actuales -->
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">

                <!-- Contraseña actual -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Contraseña actual
                    </label>
                    <input type="password" 
                           id="current_password" 
                           name="current_password"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-dark-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-dark-700 text-gray-800 dark:text-white">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nueva contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nueva contraseña
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           oninput="checkPasswordStrength()"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-dark-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-dark-700 text-gray-800 dark:text-white">
                    
                    <!-- Medidor de seguridad -->
                    <div id="password-strength" class="mt-2 hidden">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Seguridad:</span>
                            <span id="strength-text" class="text-sm font-medium"></span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-dark-600 rounded-full h-2">
                            <div id="strength-bar" class="h-2 rounded-full transition-all duration-300"></div>
                        </div>
                        <div id="password-requirements" class="mt-2 text-xs space-y-1">
                            <div id="req-length" class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Mínimo 8 caracteres</span>
                            </div>
                            <div id="req-uppercase" class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Al menos una mayúscula</span>
                            </div>
                            <div id="req-lowercase" class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Al menos una minúscula</span>
                            </div>
                            <div id="req-number" class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Al menos un número</span>
                            </div>
                            <div id="req-special" class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Al menos un carácter especial</span>
                            </div>
                        </div>
                    </div>

                    @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmar nueva contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Confirmar nueva contraseña
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-dark-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-dark-700 text-gray-800 dark:text-white">
                </div>

                <!-- Botón de cambiar contraseña -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Cambiar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Eliminar Cuenta -->
    <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-red-200 dark:border-red-700">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-red-600 dark:text-red-400 mb-6">Zona de Peligro</h2>
            
            <div class="space-y-4">
                <p class="text-gray-600 dark:text-gray-300">
                    Una vez que elimines tu cuenta, todos los recursos y datos se eliminarán permanentemente. 
                    Antes de eliminar tu cuenta, descarga cualquier dato o información que desees conservar.
                </p>

                <button type="button" 
                        onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    Eliminar Cuenta
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar cuenta -->
    <div id="delete-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                ¿Estás seguro de que quieres eliminar tu cuenta?
            </h3>
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Esta acción no se puede deshacer. Todos tus datos se eliminarán permanentemente.
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('DELETE')

                <div>
                    <label for="delete_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Confirma tu contraseña
                    </label>
                    <input type="password" 
                           id="delete_password" 
                           name="password"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-dark-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-dark-700 text-gray-800 dark:text-white"
                           required>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" 
                            onclick="document.getElementById('delete-modal').classList.add('hidden')"
                            class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Eliminar Cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthIndicator = document.getElementById('password-strength');
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');

    // Mostrar indicador si hay texto
    if (password.length > 0) {
        strengthIndicator.classList.remove('hidden');
    } else {
        strengthIndicator.classList.add('hidden');
        return;
    }

    // Criterios de validación
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };

    // Actualizar indicadores visuales de requisitos
    updateRequirement('req-length', requirements.length);
    updateRequirement('req-uppercase', requirements.uppercase);
    updateRequirement('req-lowercase', requirements.lowercase);
    updateRequirement('req-number', requirements.number);
    updateRequirement('req-special', requirements.special);

    // Calcular puntuación
    const score = Object.values(requirements).filter(Boolean).length;
    let strength = 0;
    let strengthLabel = '';
    let strengthClass = '';

    if (score === 0) {
        strength = 0;
        strengthLabel = 'Muy débil';
        strengthClass = 'bg-red-500';
    } else if (score <= 2) {
        strength = 20;
        strengthLabel = 'Débil';
        strengthClass = 'bg-red-500';
    } else if (score === 3) {
        strength = 40;
        strengthLabel = 'Regular';
        strengthClass = 'bg-yellow-500';
    } else if (score === 4) {
        strength = 70;
        strengthLabel = 'Buena';
        strengthClass = 'bg-blue-500';
    } else if (score === 5) {
        strength = 100;
        strengthLabel = 'Muy fuerte';
        strengthClass = 'bg-green-500';
    }

    // Actualizar barra de progreso
    strengthBar.style.width = strength + '%';
    strengthBar.className = `h-2 rounded-full transition-all duration-300 ${strengthClass}`;
    strengthText.textContent = strengthLabel;
    strengthText.className = `text-sm font-medium ${getTextColor(strengthClass)}`;
}

function updateRequirement(elementId, isMet) {
    const element = document.getElementById(elementId);
    const icon = element.querySelector('svg');
    const text = element.querySelector('span');
    
    if (isMet) {
        element.className = 'flex items-center text-green-600 dark:text-green-400';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
    } else {
        element.className = 'flex items-center text-gray-400 dark:text-gray-500';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
    }
}

function getTextColor(bgClass) {
    switch (bgClass) {
        case 'bg-red-500':
            return 'text-red-600 dark:text-red-400';
        case 'bg-yellow-500':
            return 'text-yellow-600 dark:text-yellow-400';
        case 'bg-blue-500':
            return 'text-blue-600 dark:text-blue-400';
        case 'bg-green-500':
            return 'text-green-600 dark:text-green-400';
        default:
            return 'text-gray-600 dark:text-gray-400';
    }
}
</script>
@endsection