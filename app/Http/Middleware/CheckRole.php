<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $rol): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->tieneRol($rol)) {
            // NUEVA LÓGICA: Redirigir según el rol del usuario
            $user = auth()->user();
            
            if ($user->tieneRol('administrador')) {
                return redirect()->route('dashboard')->with('error', 'No tienes acceso a esa sección');
            } elseif ($user->tieneRol('arbitro')) {
                return redirect()->route('arbitro.dashboard')->with('error', 'No tienes acceso a esa sección');
            } elseif ($user->tieneRol('entrenador')) {
                return redirect()->route('login')->with('error', 'No tienes acceso a esa sección');
            } elseif ($user->tieneRol('jugador')) {
                return redirect()->route('login')->with('error', 'No tienes acceso a esa sección');
            }
            
            // Fallback si no tiene ningún rol conocido
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        return $next($request);
    }
}