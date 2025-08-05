<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\JugadoresController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\LigaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EntrenadorController;
use App\Http\Controllers\ArbitroController;
use App\Http\Controllers\CanchaController;
use App\Http\Controllers\TemporadaController;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\ArbitroDashboardController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ruta principal - maneja autenticación y redirección
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->tieneRol('administrador')) {
            return redirect()->route('dashboard');
        } elseif ($user->tieneRol('arbitro')) {
            return redirect()->route('arbitro.dashboard');
        } elseif ($user->tieneRol('entrenador')) {
            // Cuando tengas la ruta del entrenador
            return redirect()->route('login'); // temporal
        } elseif ($user->tieneRol('jugador')) {
            // Cuando tengas la ruta del jugador
            return redirect()->route('login'); // temporal
        }
    }
    return redirect()->route('login');
});

// Rutas de autenticación
require __DIR__.'/auth.php';

// ================= RUTAS POR ROLES ================= //

// Admin - Panel de administración
Route::middleware(['auth', 'verified', 'role:administrador'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('jugadores', JugadoresController::class)->middleware(['auth', 'verified']);
    Route::resource('equipos', EquipoController::class)->middleware(['auth', 'verified']);
    Route::resource('ligas', LigaController::class);
    Route::resource('categorias', CategoriaController::class)->middleware(['auth', 'verified']);

    Route::resource('entrenadores', EntrenadorController::class)->middleware(['auth', 'verified']);
    Route::resource('arbitros', ArbitroController::class)->middleware(['auth', 'verified']);;
    Route::resource('canchas', CanchaController::class)->middleware(['auth', 'verified']);
    Route::resource('temporadas', TemporadaController::class)->middleware(['auth', 'verified']);
    Route::resource('juegos', JuegoController::class)->middleware(['auth', 'verified']);
    Route::resource('torneos', TorneoController::class)->middleware(['auth', 'verified']);

    //Ruta especifica para la busqueda de categorias relacionadas con una liga
    Route::get('/admin/categorias/{liga}', [AdminController::class, 'getCategoriasByLiga'])
    ->name('admin.torneos.categorias');
});


// Rutas para árbitros
Route::middleware(['auth', 'verified'])->prefix('arbitro')->name('arbitro.')->group(function () {
    Route::get('/dashboard', [ArbitroDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/change-password', [ArbitroDashboardController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('/change-password', [ArbitroDashboardController::class, 'changePassword'])->name('change-password.post');
    Route::post('/partidos/{juego}/resultado', [ArbitroDashboardController::class, 'updateResult'])->name('partidos.resultado');

    Route::get('/partido', [ArbitroDashboardController::class, 'partido'])->name('partido');

    // Ruta para ver partido (usando {juego} como parámetro)
    Route::get('/partidos/{juego}', [ArbitroDashboardController::class, 'verPartido'])
        ->name('partidos.ver')
        ->where('juego', '[0-9]+');

    Route::get('/partidos/{juego}/jugadores', [ArbitroDashboardController::class, 'getJugadoresPartido']);
    Route::post('/partidos/{juego}/iniciar', [ArbitroDashboardController::class, 'iniciarPartido']);
    // Ruta para iniciar partido oficial
    Route::post('/partidos/{juegoId}/iniciar-oficial', [ArbitroDashboardController::class, 'iniciarPartidoOficial']);
    
    // Ruta existente para actualizar estadísticas
    Route::post('/juegos/{juego}/stats', [ArbitroDashboardController::class, 'updatePlayerStats']);
    
    Route::post('/juegos/{juegoId}/registrar-accion', [ArbitroDashboardController::class, 'registrarAccion']);
    // Ruta para controlar tiempo (si no existe)
    Route::post('/juegos/{juegoId}/tiempo', [ArbitroDashboardController::class, 'controlarTiempo']);

    Route::post('/partidos/{juego}/finalizar', [ArbitroDashboardController::class, 'finalizarPartido'])
        ->name('finalizar.partido');

    Route::get('/partidos/{juego}/resultados', [ArbitroDashboardController::class, 'resultadosPartido'])
        ->name('partido.resultados');
});


// Entrenador - Panel específico
Route::middleware(['auth', 'verified', 'role:entrenador'])->group(function () {
});

// Jugador - Panel específico
Route::middleware(['auth', 'verified', 'role:jugador'])->group(function () {
});


Route::get('/test-error', function () {
    return view('errors.500');
});


// ================= RUTAS API ================= //
Route::middleware(['auth', 'verified'])->prefix('api')->name('api.')->group(function () {
    
    // Rutas para partidos/juegos
    Route::get('/partidos/{juego}/jugadores', [ArbitroDashboardController::class, 'getJugadoresPartido'])
        ->name('partidos.jugadores');
    
    Route::post('/partidos/{juego}/iniciar', [ArbitroDashboardController::class, 'iniciarPartido'])
        ->name('partidos.iniciar');

    // Rutas de acciones básicas
    Route::post('/juegos/{juego}/registrar-accion', [ArbitroDashboardController::class, 'registrarAccion']);
    Route::post('/juegos/{juego}/tiempo', [ArbitroDashboardController::class, 'controlarTiempo']);
    Route::post('/juegos/{juego}/iniciar-oficial', [ArbitroDashboardController::class, 'iniciarPartidoOficial']);

    Route::get('/juegos/{juego}/estado-tiempo', [ArbitroDashboardController::class, 'obtenerEstadoTiempo']);


    // Para estadísticas simples (asistencias, rebotes, etc.)
    Route::post('/juegos/{juego}/estadistica-simple', [ArbitroDashboardController::class, 'registrarEstadisticaSimple']);
    
    // Para estadísticas de tiros (con intentos)
    Route::post('/juegos/{juego}/estadisticas', [ArbitroDashboardController::class, 'updatePlayerStats']);
    
    // Para obtener estadísticas en tiempo real
    Route::get('/juegos/{juego}/estadisticas-tiempo-real', [ArbitroDashboardController::class, 'obtenerEstadisticasJuego']);
    
    // Para corregir estadísticas
    Route::post('/juegos/{juego}/corregir-estadistica', [ArbitroDashboardController::class, 'corregirEstadistica']);


    Route::get('/ligas/{liga}/categorias', [AdminController::class, 'getCategorias']);

    Route::get('/categorias/{categoria}/equipos', function (App\Models\Categoria $categoria) {
        $equipos = $categoria->equipos()
            ->select('id', 'nombre', 'logo_url')
            ->where('activo', true)
            ->get();
        
        return response()->json(['equipos' => $equipos]);
    });
});



// ================= RUTAS COMUNES (perfil) ================= //
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});