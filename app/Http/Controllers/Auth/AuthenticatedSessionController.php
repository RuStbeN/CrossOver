<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Providers\RouteServiceProvider;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            usleep(rand(500000, 1500000)); // 0.5-1.5 segundos de retraso
            
            return redirect()->route('login')
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Credenciales incorrectas. Por favor verifica tus datos.',
                ]);
        }

        $request->session()->regenerate();
        
        // NUEVA LÓGICA: Verificar si el usuario puede acceder a la URL intended
        $user = Auth::user();
        $intended = session('url.intended');
        
        // Si hay una URL intended, verificar si el usuario tiene acceso
        if ($intended && !$this->userCanAccessUrl($user, $intended)) {
            // Limpiar la intended URL si no tiene acceso
            session()->forget('url.intended');
        }
        
        // Redirigir al dashboard apropiado según el rol
        return redirect($this->getDefaultRedirect($user));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Verificar si el usuario puede acceder a una URL específica
     */
    private function userCanAccessUrl($user, $url): bool
    {
        // Extraer la ruta de la URL
        $path = parse_url($url, PHP_URL_PATH);
        
        // Definir rutas por rol
        $adminRoutes = ['/dashboard', '/jugadores', '/equipos', '/ligas', '/categorias', 
                       '/entrenadores', '/arbitros', '/canchas', '/temporadas', '/juegos', '/torneos'];
        $arbitroRoutes = ['/arbitro'];
        $entrenadorRoutes = ['/entrenador'];
        $jugadorRoutes = ['/jugador'];
        
        // Verificar acceso según el rol
        if ($user->tieneRol('administrador')) {
            // Admin puede acceder a rutas de admin
            foreach ($adminRoutes as $route) {
                if (str_starts_with($path, $route)) {
                    return true;
                }
            }
        } elseif ($user->tieneRol('arbitro')) {
            // Árbitro solo puede acceder a rutas de árbitro
            foreach ($arbitroRoutes as $route) {
                if (str_starts_with($path, $route)) {
                    return true;
                }
            }
        } elseif ($user->tieneRol('entrenador')) {
            // Entrenador solo puede acceder a rutas de entrenador
            foreach ($entrenadorRoutes as $route) {
                if (str_starts_with($path, $route)) {
                    return true;
                }
            }
        } elseif ($user->tieneRol('jugador')) {
            // Jugador solo puede acceder a rutas de jugador
            foreach ($jugadorRoutes as $route) {
                if (str_starts_with($path, $route)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Obtener la redirección por defecto según el rol del usuario
     */
    private function getDefaultRedirect($user): string
    {
        if ($user->tieneRol('administrador')) {
            return route('dashboard');
        } elseif ($user->tieneRol('arbitro')) {
            return route('arbitro.dashboard');
        } elseif ($user->tieneRol('entrenador')) {
            // Cambiar cuando tengas la ruta del entrenador
            return route('login');
        } elseif ($user->tieneRol('jugador')) {
            // Cambiar cuando tengas la ruta del jugador
            return route('login');
        }
        
        // Fallback si no tiene ningún rol
        return route('login');
    }
}