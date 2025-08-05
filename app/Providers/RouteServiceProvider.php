<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Ruta por defecto después del login
     */
    public const HOME = '/dashboard';

    public function boot(): void
    {
        // Aquí puedes cargar tus rutas si quieres
        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }

    /**
     * Redirección personalizada según rol
     */
    public static function homeByRole(): string
    {
        $user = auth()->user();

        if ($user?->hasRole('administrador')) {
            return route('dashboard');
        } elseif ($user?->hasRole('arbitro')) {
            return route('arbitro.dashboard');
        } elseif ($user?->hasRole('entrenador')) {
            return route('entrenador.dashboard');
        } elseif ($user?->hasRole('jugador')) {
            return route('jugador.dashboard');
        }

        return self::HOME;
    }
}
