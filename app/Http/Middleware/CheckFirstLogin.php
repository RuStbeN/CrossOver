<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckFirstLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            \Log::debug('Middleware CheckFirstLogin', [
                'user_id' => $user->id,
                'must_change' => $user->must_change_password,
                'pass_changed_at' => $user->password_changed_at,
                'is_arbitro' => $user->hasRole('arbitro'),
                'request_path' => $request->path()
            ]);

            if ($user->hasRole('arbitro') && $this->shouldChangePassword($user)) {
                if (!$request->is('arbitro/change-password*') && !$request->is('logout')) {
                    \Log::debug('Redirigiendo a cambio de contraseña');
                    return redirect()->route('arbitro.change-password')
                        ->with('warning', 'Por seguridad, debes cambiar tu contraseña antes de continuar.');
                }
            }
        }

        return $next($request);
    }

    private function shouldChangePassword($user)
    {
        // Si debe cambiar contraseña (campo explícito)
        if ($user->must_change_password) {
            return true;
        }

        // Si nunca ha cambiado la contraseña
        if (empty($user->password_changed_at)) {
            return true;
        }

        // Si tiene contraseña temporal (patrón opcional)
        if (preg_match('/^arbitro_/', $user->password)) {
            return true;
        }

        return false;
    }
}