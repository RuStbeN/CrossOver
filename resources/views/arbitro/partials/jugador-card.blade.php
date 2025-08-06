<style>
    /* Contenedor principal optimizado */
    .jugadores-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
        padding: 0.5rem;
    }

    /* Tarjeta rediseñada - Soporte dark mode */
    .jugador-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.2s ease;
        border: 1px solid #e5e7eb;
    }

    .dark .jugador-card {
        background: #1e293b;
        border-color: #334155;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    .jugador-card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    .dark .jugador-card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }

    /* Header compacto - Dark mode */
    .card-header {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .dark .card-header {
        background: #334155;
        border-color: #475569;
    }

    /* Foto de jugador */
    .player-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 0.75rem;
        object-fit: cover;
        border: 2px solid #e5e7eb;
    }

    .dark .player-avatar {
        border-color: #475569;
    }

    /* Info jugador - Dark mode */
    .player-info {
        flex-grow: 1;
        min-width: 0;
    }

    .player-name {
        font-weight: 600;
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #111827;
    }

    .dark .player-name {
        color: #f8fafc;
    }

    .player-number {
        font-size: 0.75rem;
        color: #6b7280;
    }

    .dark .player-number {
        color: #94a3b8;
    }

    /* Puntos - Dark mode */
    .player-points {
        text-align: center;
        min-width: 50px;
    }

    .points-value {
        font-weight: 700;
        font-size: 1.25rem;
        line-height: 1;
        color: #111827;
    }

    .dark .points-value {
        color: #f8fafc;
    }

    .points-label {
        font-size: 0.65rem;
        color: #6b7280;
        text-transform: uppercase;
    }

    .dark .points-label {
        color: #94a3b8;
    }

    /* Sección de botones - Dark mode */
    .actions-section {
        padding: 0.75rem;
    }

    /* Grupos de botones */
    .button-group {
        display: grid;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    /* Botones principales - Dark mode */
    .action-button {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        height: 100%;
        min-height: 50px;
    }

    .action-button:active {
        transform: scale(0.97);
    }

    /* Botones de anotación */
    .btn-score {
        color: white;
        font-size: 1.1rem;
    }

    .btn-1pt {
        background: #10b981;
    }

    .btn-1pt:hover {
        background: #059669;
    }

    .btn-2pt {
        background: #2563eb;
    }

    .btn-2pt:hover {
        background: #1d4ed8;
    }

    .btn-3pt {
        background: #7c3aed;
    }

    .btn-3pt:hover {
        background: #6d28d9;
    }

    /* Botones de tiros fallados */
    .btn-miss-1 {
        background: #f43f5e;
        color: white;
        font-size: 0.9rem;
    }

    .btn-miss-1:hover {
        background: #e11d48;
    }

    .btn-miss-2 {
        background: #dc2626;
        color: white;
        font-size: 0.9rem;
    }

    .btn-miss-2:hover {
        background: #b91c1c;
    }

    .btn-miss-3 {
        background: #991b1b;
        color: white;
        font-size: 0.9rem;
    }

    .btn-miss-3:hover {
        background: #7f1d1d;
    }

    /* Botón de falta */
    .btn-foul {
        background: #f59e0b;
        color: white;
    }

    .btn-foul:hover {
        background: #d97706;
    }

    .dark .btn-foul {
        background: #d97706;
    }

    .dark .btn-foul:hover {
        background: #b45309;
    }

    /* Botones secundarios - Dark mode */
    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
        font-size: 0.85rem;
        padding: 0.5rem;
    }

    .btn-secondary:hover {
        background: #d1d5db;
    }

    .dark .btn-secondary {
        background: #475569;
        color: #f8fafc;
    }

    .dark .btn-secondary:hover {
        background: #334155;
    }

    /* Indicadores de equipo */
    .team-local {
        border-left: 4px solid #3b82f6;
    }

    .dark .team-local {
        border-left: 4px solid #60a5fa;
    }

    .team-visitante {
        border-left: 4px solid #ef4444;
    }

    .dark .team-visitante {
        border-left: 4px solid #f87171;
    }

    /* Diseño responsive */
    @media (max-width: 768px) {
        .jugadores-container {
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        }
        
        .action-button {
            min-height: 45px;
            font-size: 0.95rem;
        }
    }

    /* Efecto de actualización de puntos */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .points-updated {
        animation: pulse 0.3s ease;
    }
</style>

<div class="jugador-card {{ $tipoEquipo === 'Local' ? 'team-local' : 'team-visitante' }}" data-jugador-id="{{ $alineacion->jugador_id ?? $jugador->id }}">
    <!-- Encabezado compacto -->
    <div class="card-header">
        @if(($alineacion->jugador->foto_url ?? $jugador->foto_url))
            <img src="{{ asset('storage/'.($alineacion->jugador->foto_url ?? $jugador->foto_url)) }}" 
                 class="player-avatar">
        @else
            <div class="player-avatar flex items-center justify-center {{ $tipoEquipo === 'Local' ? 'bg-blue-600' : 'bg-red-600' }} text-white font-bold">
                {{ substr(($alineacion->jugador->nombre ?? $jugador->nombre), 0, 1) }}
            </div>
        @endif
        
        <div class="player-info">
            <div class="player-name">{{ $alineacion->jugador->nombre ?? $jugador->nombre }}</div>
            <div class="player-number">#{{ $alineacion->numero_camiseta ?? $jugador->numero_camiseta }}</div>
        </div>
        
        <div class="player-points">
            <div class="points-value puntos-jugador">{{ $alineacion->puntos ?? 0 }}</div>
            <div class="points-label">PTS</div>
        </div>
    </div>

    @if($estado === 'En Curso')
    <!-- Sección de acciones -->
    <div class="actions-section">
        <!-- Botones principales de anotación -->
        <div class="button-group grid-cols-3">
            <button onclick="registrarTiroDirecto({{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}', 'tiro_libre', true)" 
                    class="action-button btn-score btn-1pt">
                +1 Punto
            </button>
            <button onclick="registrarTiroDirecto({{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}', '2pts', true)" 
                    class="action-button btn-score btn-2pt">
                +2 Puntos
            </button>
            <button onclick="registrarTiroDirecto({{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}', '3pts', true)" 
                    class="action-button btn-score btn-3pt">
                +3 Puntos
            </button>
        </div>

        <!-- Botones de faltas -->
        <div class="button-group">
            <button onclick="abrirModalFaltas({{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}')" 
                    class="action-button btn-foul">
                Registrar Falta
            </button>
        </div>

        <!-- Botones de tiros fallados (1, 2 y 3 puntos) -->
        <div class="button-group grid-cols-3">
            <button onclick="registrarTiroDirecto({{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}', 'tiro_libre', false)" 
                    class="action-button btn-miss-1">
                1 Pto Fallado
            </button>
            <button onclick="registrarTiroDirecto({{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}', '2pts', false)" 
                    class="action-button btn-miss-2">
                2 Ptos Fallados
            </button>
            <button onclick="registrarTiroDirecto({{ $alineacion->id ?? $jugador->id }}, '{{ $tipoEquipo }}', '3pts', false)" 
                    class="action-button btn-miss-3">
                3 Ptos Fallados
            </button>
        </div>

        
    </div>
    @else
    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
        <p class="text-sm">Partido no iniciado</p>
    </div>
    @endif
</div>