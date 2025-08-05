<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Redirect;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TorneoPuntosService::class, function ($app) {
            return new TorneoPuntosService();
        });
    }

    public static function homeByRole(): string
    {
        $user = auth()->user();

        if (!$user) {
            return '/login';
        }

        if ($user->hasRole('administrador')) {
            return route('dashboard');
        }

        if ($user->hasRole('arbitro')) {
            return route('arbitro.dashboard');
        }

        if ($user->hasRole('entrenador')) {
            return '/entrenador/dashboard';
        }

        if ($user->hasRole('jugador')) {
            return '/jugador/dashboard';
        }

        return '/';
    }

    
    public function boot(): void
    {
        // Caché en desarrollo
        if (app()->environment('local')) {
            config(['cache.default' => 'array']);
        }

        // Redirección mejorada después de login
        Redirect::macro('intendedByRole', function ($default = '/') {
            if (auth()->check()) {
                $user = auth()->user();
                
                // Siempre redirigir al dashboard apropiado, ignorar intended
                if ($user->tieneRol('administrador')) {
                    return redirect()->route('dashboard');
                } elseif ($user->tieneRol('arbitro')) {
                    return redirect()->route('arbitro.dashboard');
                } elseif ($user->tieneRol('entrenador')) {
                    return redirect()->route('login'); // temporal
                } elseif ($user->tieneRol('jugador')) {
                    return redirect()->route('login'); // temporal
                }
            }

            return redirect()->route('login');
        });
    }

}
