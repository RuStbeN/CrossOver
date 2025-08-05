@extends('layouts.arbitro')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white dark:bg-dark-800 rounded-lg shadow-lg border border-dark-100 dark:border-dark-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-400 dark:border-dark-700 bg-gray-100 dark:bg-dark-900">
            <h2 class="text-xl font-bold text-black dark:text-primary-400">Cambiar Contraseña</h2>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('arbitro.change-password.post') }}">
                @csrf

                {{-- Mensaje de advertencia --}}
                @if(session('warning'))
                    <div class="mb-4 bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-600 text-yellow-700 dark:text-yellow-300 px-4 py-3 rounded relative">
                        <strong>Atención:</strong> {{ session('warning') }}
                    </div>
                @endif

                {{-- Mensaje de éxito --}}
                @if(session('success'))
                    <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative">
                        <strong>Éxito:</strong> {{ session('success') }}
                    </div>
                @endif

                {{-- Mensaje de error general --}}
                @if(session('error'))
                    <div class="mb-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded relative">
                        <strong>Error:</strong> {{ session('error') }}
                    </div>
                @endif

                {{-- Errores de validación --}}
                @if($errors->any())
                    <div class="mb-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded relative">
                        <strong>Errores encontrados:</strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-4">
                    <!-- Contraseña Actual -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Contraseña actual *
                        </label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-dark-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-dark-700 text-gray-800 dark:text-white @error('current_password') border-red-500 @enderror">
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nueva Contraseña -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nueva contraseña *
                        </label>
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               required
                               oninput="checkPasswordStrength()"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-dark-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-dark-700 text-gray-800 dark:text-white @error('new_password') border-red-500 @enderror">
                        
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span>Mínimo 8 caracteres</span>
                                </div>
                                <div id="req-uppercase" class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span>Al menos una mayúscula</span>
                                </div>
                                <div id="req-lowercase" class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span>Al menos una minúscula</span>
                                </div>
                                <div id="req-number" class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span>Al menos un número</span>
                                </div>
                                <div id="req-special" class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span>Al menos un carácter especial</span>
                                </div>
                            </div>
                        </div>

                        @error('new_password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar nueva contraseña -->
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Confirmar nueva contraseña *
                        </label>
                        <input type="password" 
                               id="new_password_confirmation" 
                               name="new_password_confirmation" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-dark-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-dark-700 text-gray-800 dark:text-white">
                    </div>
                </div>

                <!-- Información importante -->
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        <strong>Requisitos de seguridad:</strong><br>
                        • Mínimo 8 caracteres<br>
                        • Al menos una mayúscula (A-Z)<br>
                        • Al menos una minúscula (a-z)<br>
                        • Al menos un número (0-9)<br>
                        • Al menos un carácter especial (@$!%*?&)
                    </p>
                </div>

                <!-- Botón de cambiar contraseña -->
                <div class="mt-6 flex justify-end">
                    <button type="submit" 
                            id="submit-btn"
                            class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        Cambiar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function checkPasswordStrength() {
    const password = document.getElementById('new_password').value;
    const strengthIndicator = document.getElementById('password-strength');
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');
    const submitBtn = document.getElementById('submit-btn');

    // Mostrar indicador si hay texto
    if (password.length > 0) {
        strengthIndicator.classList.remove('hidden');
    } else {
        strengthIndicator.classList.add('hidden');
        submitBtn.disabled = false; // Siempre habilitado
        return;
    }

    // Criterios de validación (solo para información, no para bloquear)
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>@$!%*?&]/.test(password)
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

    if (password.length === 0) {
        strength = 0;
        strengthLabel = '';
        strengthClass = 'bg-gray-200';
    } else if (score === 0) {
        strength = 20;
        strengthLabel = 'Muy débil';
        strengthClass = 'bg-red-500';
    } else if (score <= 2) {
        strength = 40;
        strengthLabel = 'Débil';
        strengthClass = 'bg-red-500';
    } else if (score === 3) {
        strength = 60;
        strengthLabel = 'Regular';
        strengthClass = 'bg-yellow-500';
    } else if (score === 4) {
        strength = 80;
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

    // NUNCA deshabilitar el botón de submit
    submitBtn.disabled = false;
}

function updateRequirement(elementId, isMet) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const icon = element.querySelector('svg');
    
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

// Verificar fortaleza al cargar la página y en cada cambio
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('new_password');
    if (passwordInput) {
        passwordInput.addEventListener('input', checkPasswordStrength);
        checkPasswordStrength();
    }
});
</script>
@endpush
@endsection