@php
    $esEspejo = ($espejo ?? false) || ($tipoEquipo === 'Visitante');
@endphp

<div class="w-[380px] h-[160px] bg-white dark:bg-dark-700 border border-gray-300 dark:border-dark-600 rounded-lg shadow-md flex flex-col overflow-hidden jugador-card {{ $tipoEquipo === 'Local' ? 'team-local' : 'team-visitante' }}"
     data-jugador-id="{{ $alineacion->jugador_id ?? $jugador->id }}">
    
    <!-- Encabezado -->
    <div class="flex items-center justify-between px-2 py-1 bg-gray-100 dark:bg-dark-600 border-b border-gray-300 dark:border-dark-600 h-10 gap-2">
        <div class="flex items-center gap-2 text-sm text-black dark:text-white">
            <span class="flex items-center gap-1">
                <i class="fas fa-basketball-ball text-orange-500"></i>
                <span class="puntos-jugador">{{ $alineacion->puntos ?? 0 }}</span>
            </span>
            <span class="flex items-center gap-1">
                <i class="fas fa-exclamation-triangle text-red-500"></i> 0
            </span>
        </div>

        <div class="flex-1 min-w-0">
            <button class="truncate w-full text-sm h-[30px] border border-gray-300 px-2 rounded bg-gray-100 hover:bg-gray-200 dark:bg-dark-700 dark:border-dark-500 dark:hover:bg-dark-600 text-black dark:text-white transition-all boton-nombre">
                {{ $alineacion->jugador->nombre ?? $jugador->nombre }}
            </button>
        </div>

        <button onclick="registrarFaltaYAnimar(this, {{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}')" 
                class="text-sm h-[30px] w-[30px] flex items-center justify-center border border-gray-300 rounded bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-600 dark:border-yellow-500 dark:hover:bg-yellow-500 text-yellow-900 dark:text-white transition-all boton-falta"
                title="Registrar falta">
            <i class="fas fa-hand-paper text-sm"></i>
        </button>

        <div class="flex items-center gap-1 text-sm text-black dark:text-white">
            <i class="far fa-clock"></i> 00:00
        </div>
    </div>

    <!-- Cuerpo -->
    <div class="flex {{ $esEspejo ? 'flex-row-reverse' : 'flex-row' }} flex-grow p-2">
        <!-- Círculo con número de camiseta -->
        <div class="flex justify-center items-center w-[50px]">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-2xl {{ $tipoEquipo === 'Local' ? 'bg-blue-600' : 'bg-red-600' }}">
                {{ $alineacion->numero_camiseta ?? $jugador->numero_camiseta }}
            </div>
        </div>

        <!-- Botones -->
        <div class="flex-1 flex flex-col gap-1 px-2">
            <div class="flex gap-1 flex-1">
                <button onclick="registrarConAnimacion(this, {{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}', 'tiro_libre', true)" 
                        class="flex-1 text-sm h-[26px] border border-gray-300 rounded bg-green-100 hover:bg-green-200 dark:bg-green-900 dark:border-green-800 text-black dark:text-white transition-all boton-punto-positivo">+1</button>
                <button onclick="registrarConAnimacion(this, {{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}', '2pts', true)" 
                        class="flex-1 text-sm h-[26px] border border-gray-300 rounded bg-green-100 hover:bg-green-200 dark:bg-green-900 dark:border-green-800 text-black dark:text-white transition-all boton-punto-positivo">+2</button>
                <button onclick="registrarConAnimacion(this, {{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}', '3pts', true)" 
                        class="flex-1 text-sm h-[26px] border border-gray-300 rounded bg-green-100 hover:bg-green-200 dark:bg-green-900 dark:border-green-800 text-black dark:text-white transition-all boton-punto-positivo">+3</button>
            </div>

            <div class="flex gap-1 flex-1">
                <button onclick="registrarConAnimacion(this, {{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}', 'tiro_libre', false)" 
                        class="flex-1 text-sm h-[26px] border border-gray-300 rounded bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:border-red-800 text-black dark:text-white transition-all boton-punto-negativo">-1</button>
                <button onclick="registrarConAnimacion(this, {{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}', '2pts', false)" 
                        class="flex-1 text-sm h-[26px] border border-gray-300 rounded bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:border-red-800 text-black dark:text-white transition-all boton-punto-negativo">-2</button>
                <button onclick="registrarConAnimacion(this, {{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}', '3pts', false)" 
                        class="flex-1 text-sm h-[26px] border border-gray-300 rounded bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:border-red-800 text-black dark:text-white transition-all boton-punto-negativo">-3</button>
            </div>
        </div>
    </div>
</div>

<script>
    function registrarConAnimacion(boton, id, tipoEquipo, tipoTiro, esExito) {
        animarBoton(boton);
        registrarTiroDirecto(id, tipoEquipo, tipoTiro, esExito);
    }

    function registrarFaltaYAnimar(boton, id, tipoEquipo) {
        animarBoton(boton);
        abrirModalFaltas(id, tipoEquipo);
    }

    function animarBoton(boton) {
        let clase = '';
        if (boton.classList.contains('boton-falta')) clase = 'ring-yellow-400';
        else if (boton.classList.contains('boton-punto-positivo')) clase = 'ring-green-400';
        else if (boton.classList.contains('boton-punto-negativo')) clase = 'ring-red-400';
        else if (boton.classList.contains('boton-nombre')) clase = 'ring-blue-400';

        if (clase) {
            boton.classList.add('ring-2', clase);
            setTimeout(() => {
                boton.classList.remove('ring-2', clase);
            }, 500);
        }
    }
</script>