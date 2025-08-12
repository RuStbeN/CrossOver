<?php

namespace App\Http\Controllers;

use App\Models\Arbitro;
use App\Models\Juego;
use App\Models\Equipo;
use App\Models\EquipoJugador;
use App\Models\Jugador;
use App\Models\JuegoAlineacion; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\ResultadosPorPuntos;


class ArbitroDashboardController extends Controller
{
    /**
     * Dashboard del árbitro
     */
    public function dashboard()
    {
        $user = Auth::user();
        $arbitro = Arbitro::where('user_id', $user->id)->first();

        if (!$arbitro) {
            return redirect()->route('login')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // Verificar si debe cambiar contraseña
        if ($this->shouldChangePassword($user)) {
            return redirect()->route('arbitro.change-password')
                ->with('warning', 'Debes cambiar tu contraseña antes de continuar.');
        }

        // Obtener solo los partidos donde este árbitro sea el de mesa de control
        $partidos = Juego::where('mesa_control_id', $arbitro->id)
            ->with(['equipoLocal', 'equipoVisitante', 'cancha', 'torneo'])
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        // Estadísticas básicas (solo como mesa de control)
        $totalPartidos = Juego::where('mesa_control_id', $arbitro->id)->count();
        $partidosCompletados = Juego::where('mesa_control_id', $arbitro->id)
            ->where('estado', 'Finalizado')
            ->count();
        $partidosPendientes = Juego::where('mesa_control_id', $arbitro->id)
            ->whereIn('estado', ['Programado', 'En Curso'])
            ->count();

        return view('arbitro.dashboard', compact(
            'arbitro', 
            'partidos', 
            'totalPartidos', 
            'partidosCompletados', 
            'partidosPendientes'
        ));
    }


    /**
     * Mostrar formulario para cambiar contraseña
     */
    public function showChangePasswordForm()
    {
        return view('arbitro.change-password');
    }

    /**
     * Cambiar contraseña (versión menos estricta)
     */
    public function changePassword(Request $request)
    {
        // Validación simplificada
        $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                'min:4', // Puedes ajustar este mínimo a lo que prefieras
                'confirmed',
                'different:current_password',
            ],
        ], [
            'new_password.different' => 'La nueva contraseña debe ser diferente a la actual.',
            'new_password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        $user = Auth::user();

        // Verificar contraseña actual
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'La contraseña actual es incorrecta.'])
                ->withInput();
        }

        try {
            $user->update([
                'password' => Hash::make($request->new_password),
                'password_changed_at' => now(),
                'must_change_password' => false,
            ]);

            // Actualizar la instancia del usuario en la sesión
            Auth::setUser($user->fresh());

            Log::info('Contraseña cambiada por árbitro', ['user_id' => $user->id]);

            // Limpiar sesión de advertencias
            Session::forget('warning');
            Session::forget('must_change_password');

            return redirect()->route('arbitro.dashboard')
                ->with('success', 'Contraseña cambiada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al cambiar contraseña: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Ocurrió un error al cambiar la contraseña. Intenta nuevamente.')
                ->withInput();
        }
    }

    /**
     * Verificar si el usuario debe cambiar contraseña
     */
    private function shouldChangePassword($user)
    {
        // Si tiene un campo must_change_password
        if (isset($user->must_change_password) && $user->must_change_password) {
            return true;
        }

        // Si nunca ha cambiado la contraseña
        if (empty($user->password_changed_at)) {
            return true;
        }

        // Si la contraseña es muy antigua (opcional)
        // if ($user->password_changed_at < now()->subMonths(6)) {
        //     return true;
        // }

        return false;
    }

    /**
     * Actualizar resultado del partido
     */
    public function updateResult(Request $request, Juego $juego)
    {
        $user = Auth::user();
        $arbitro = Arbitro::where('user_id', $user->id)->firstOrFail();

        // Verificar que el partido esté asignado a este árbitro como mesa de control
        if ($juego->mesa_control_id !== $arbitro->id) {
            return redirect()->back()->with('error', 'No tienes permisos para actualizar este partido.');
        }

        $validated = $request->validate([
            'puntos_local' => 'required|integer|min:0',
            'puntos_visitante' => 'required|integer|min:0',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        try {
            $juego->update([
                'puntos_local' => $validated['puntos_local'],
                'puntos_visitante' => $validated['puntos_visitante'],
                'observaciones' => $validated['observaciones'],
                'estado' => 'Finalizado',
                'updated_by' => $user->id,
            ]);

            Log::info('Resultado actualizado por árbitro de mesa de control', [
                'juego_id' => $juego->id,
                'arbitro_id' => $arbitro->id
            ]);

            return redirect()->route('arbitro.dashboard')
                ->with('success', 'Resultado actualizado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar resultado: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Ocurrió un error al actualizar el resultado. Intenta nuevamente.');
        }
    }

    public function getJugadoresPartido($juegoId)
    {
        try {
            // Cargar el juego con relaciones de jugadores activos (sin usar select personalizado)
            $juego = Juego::with([
                'equipoLocal.jugadores' => function ($query) {
                    $query->wherePivot('activo', true);
                },
                'equipoVisitante.jugadores' => function ($query) {
                    $query->wherePivot('activo', true);
                }
            ])->findOrFail($juegoId);

            // Verificar permisos del árbitro
            $arbitro = Arbitro::where('user_id', Auth::id())->first();
            if (!$arbitro) {
                return response()->json(['error' => 'No se encontró el perfil de árbitro'], 403);
            }

            $tienePermiso = (
                $juego->arbitro_principal_id === $arbitro->id ||
                $juego->arbitro_auxiliar_id === $arbitro->id ||
                $juego->mesa_control_id === $arbitro->id
            );

            if (!$tienePermiso) {
                return response()->json(['error' => 'No tienes permisos para ver este juego'], 403);
            }

            // Mapear jugadores locales y visitantes
            $jugadoresLocal = $juego->equipoLocal->jugadores->map(function ($jugador) {
                return [
                    'id' => $jugador->id,
                    'nombre' => $jugador->nombre,
                    'numero_camiseta' => $jugador->pivot->numero_camiseta ?? 'N/A',
                    'posicion_principal' => $jugador->pivot->posicion_principal ?? 'N/A',
                ];
            });

            $jugadoresVisitante = $juego->equipoVisitante->jugadores->map(function ($jugador) {
                return [
                    'id' => $jugador->id,
                    'nombre' => $jugador->nombre,
                    'numero_camiseta' => $jugador->pivot->numero_camiseta ?? 'N/A',
                    'posicion_principal' => $jugador->pivot->posicion_principal ?? 'N/A',
                ];
            });

            return response()->json([
                'local' => $jugadoresLocal,
                'visitante' => $jugadoresVisitante,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener jugadores del juego: ' . $e->getMessage(), [
                'juego_id' => $juegoId,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error al cargar los jugadores: ' . $e->getMessage()], 500);
        }
    }

    public function iniciarPartido(Request $request, $juegoId)
    {
        $juego = Juego::findOrFail($juegoId);

        // Validar estado del juego - solo verificar que esté programado
        if ($juego->estado !== 'Programado') {
            return response()->json(['error' => 'El juego no puede ser iniciado en su estado actual'], 400);
        }

        // Verificar permisos del árbitro
        $arbitro = Arbitro::where('user_id', Auth::id())->first();
        if (!$arbitro) {
            return response()->json(['error' => 'No se encontró el perfil de árbitro'], 403);
        }

        $tienePermiso = (
            $juego->arbitro_principal_id === $arbitro->id ||
            $juego->arbitro_auxiliar_id === $arbitro->id ||
            $juego->mesa_control_id === $arbitro->id
        );

        if (!$tienePermiso) {
            return response()->json(['error' => 'No tienes permisos para iniciar este juego'], 403);
        }

        // Validar los jugadores seleccionados usando la relación correcta
        $validated = $request->validate([
            'titulares_local' => 'sometimes|array|max:5',
            'titulares_local.*' => 'exists:jugadores,id',
            'titulares_visitante' => 'sometimes|array|max:5',
            'titulares_visitante.*' => 'exists:jugadores,id',
        ]);

        // Solo redirigir a la vista del partido con los datos
        $queryParams = http_build_query([
            'juego' => $juego->id,
            'titulares_local' => implode(',', $validated['titulares_local'] ?? []),
            'titulares_visitante' => implode(',', $validated['titulares_visitante'] ?? [])
        ]);

        return response()->json([
            'message' => 'Redirigiendo al partido',
            'redirect_url' => "/arbitro/partido?{$queryParams}"
        ]);
    }

    public function partido(Request $request)
    {
        $user = Auth::user();
        $arbitro = Arbitro::where('user_id', $user->id)->first();

        if (!$arbitro) {
            return redirect()->route('login')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $juegoId = $request->get('juego');
        $juego = Juego::where('id', $juegoId)
            ->where(function($query) use ($arbitro) {
                $query->where('arbitro_principal_id', $arbitro->id)
                    ->orWhere('arbitro_auxiliar_id', $arbitro->id)
                    ->orWhere('mesa_control_id', $arbitro->id);
            })
            ->with([
                'equipoLocal',
                'equipoVisitante', 
                'cancha', 
                'torneo',
                'juegoAlineaciones.jugador' => function($query) {
                    $query->select('id', 'nombre', 'foto_url');
                }
            ])
            ->firstOrFail();

        // Separar alineaciones por equipo
        $alineacionesLocal = $juego->juegoAlineaciones
            ->where('tipo_equipo', 'Local')
            ->sortByDesc('es_titular');

        $alineacionesVisitante = $juego->juegoAlineaciones
            ->where('tipo_equipo', 'Visitante')
            ->sortByDesc('es_titular');

        // Procesar jugadores seleccionados
        $titularesLocalSeleccionados = collect();
        $titularesVisitanteSeleccionados = collect();
        
        // Obtener los IDs de jugadores desde los parámetros de la URL
        if ($request->has('titulares_local') && !empty($request->get('titulares_local'))) {
            $titularesLocalIds = explode(',', $request->get('titulares_local'));
            $titularesLocalIds = array_filter($titularesLocalIds, 'is_numeric');
            
            if (!empty($titularesLocalIds)) {
                $titularesLocalSeleccionados = \App\Models\Jugador::whereIn('id', $titularesLocalIds)
                    ->whereHas('equipos', function($query) use ($juego) {
                        $query->where('equipo_jugadores.equipo_id', $juego->equipo_local_id)
                            ->where('equipo_jugadores.activo', 1);
                    })
                    ->with(['equipos' => function($query) use ($juego) {
                        $query->where('equipo_jugadores.equipo_id', $juego->equipo_local_id)
                            ->select('equipos.id', 'equipos.nombre', 'equipo_jugadores.numero_camiseta');
                    }])
                    ->select('id', 'nombre', 'foto_url')
                    ->get()
                    ->map(function($jugador) {
                        $jugador->numero_camiseta = $jugador->equipos->first()->pivot->numero_camiseta ?? 'N/A';
                        return $jugador;
                    });
            }
        }
        
        if ($request->has('titulares_visitante') && !empty($request->get('titulares_visitante'))) {
            $titularesVisitanteIds = explode(',', $request->get('titulares_visitante'));
            $titularesVisitanteIds = array_filter($titularesVisitanteIds, 'is_numeric');
            
            if (!empty($titularesVisitanteIds)) {
                $titularesVisitanteSeleccionados = \App\Models\Jugador::whereIn('id', $titularesVisitanteIds)
                    ->whereHas('equipos', function($query) use ($juego) {
                        $query->where('equipo_jugadores.equipo_id', $juego->equipo_visitante_id)
                            ->where('equipo_jugadores.activo', 1);
                    })
                    ->with(['equipos' => function($query) use ($juego) {
                        $query->where('equipo_jugadores.equipo_id', $juego->equipo_visitante_id)
                            ->select('equipos.id', 'equipos.nombre', 'equipo_jugadores.numero_camiseta');
                    }])
                    ->select('id', 'nombre', 'foto_url')
                    ->get()
                    ->map(function($jugador) {
                        $jugador->numero_camiseta = $jugador->equipos->first()->pivot->numero_camiseta ?? 'N/A';
                        return $jugador;
                    });
            }
        }

        return view('arbitro.partido', compact(
            'juego', 
            'alineacionesLocal', 
            'alineacionesVisitante',
            'titularesLocalSeleccionados',
            'titularesVisitanteSeleccionados'
        ));
    }

    public function verPartido($juegoId)
    {
        $user = Auth::user();
        $arbitro = Arbitro::where('user_id', $user->id)->first();

        if (!$arbitro) {
            return redirect()->route('login')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // Obtener el juego con todas las relaciones necesarias
        $juego = Juego::where('id', $juegoId)
            ->where(function($query) use ($arbitro) {
                $query->where('arbitro_principal_id', $arbitro->id)
                    ->orWhere('arbitro_auxiliar_id', $arbitro->id)
                    ->orWhere('mesa_control_id', $arbitro->id);
            })
            ->with([
                'equipoLocal',
                'equipoVisitante', 
                'cancha', 
                'torneo',
                'juegoAlineaciones.jugador' => function($query) {
                    $query->select('id', 'nombre', 'foto_url');
                }
            ])
            ->firstOrFail();

        // Procesar jugadores seleccionados (para estado Programado)
        $titularesLocalSeleccionados = collect();
        $titularesVisitanteSeleccionados = collect();
        
        // Si el juego está programado, obtener los titulares desde la solicitud
        if ($juego->estado === 'Programado') {
            if (request()->has('titulares_local') && !empty(request()->get('titulares_local'))) {
                $titularesLocalIds = explode(',', request()->get('titulares_local'));
                $titularesLocalSeleccionados = Jugador::whereIn('id', $titularesLocalIds)
                    ->whereHas('equipos', function($query) use ($juego) {
                        $query->where('equipo_jugadores.equipo_id', $juego->equipo_local_id)
                            ->where('equipo_jugadores.activo', 1);
                    })
                    ->with(['equipos' => function($query) use ($juego) {
                        $query->where('equipo_jugadores.equipo_id', $juego->equipo_local_id)
                            ->select('equipos.id', 'equipos.nombre', 'equipo_jugadores.numero_camiseta');
                    }])
                    ->select('id', 'nombre', 'foto_url')
                    ->get()
                    ->map(function($jugador) {
                        $jugador->numero_camiseta = $jugador->equipos->first()->pivot->numero_camiseta ?? 'N/A';
                        return $jugador;
                    });
            }
            
            if (request()->has('titulares_visitante') && !empty(request()->get('titulares_visitante'))) {
                $titularesVisitanteIds = explode(',', request()->get('titulares_visitante'));
                $titularesVisitanteSeleccionados = Jugador::whereIn('id', $titularesVisitanteIds)
                    ->whereHas('equipos', function($query) use ($juego) {
                        $query->where('equipo_jugadores.equipo_id', $juego->equipo_visitante_id)
                            ->where('equipo_jugadores.activo', 1);
                    })
                    ->with(['equipos' => function($query) use ($juego) {
                        $query->where('equipo_jugadores.equipo_id', $juego->equipo_visitante_id)
                            ->select('equipos.id', 'equipos.nombre', 'equipo_jugadores.numero_camiseta');
                    }])
                    ->select('id', 'nombre', 'foto_url')
                    ->get()
                    ->map(function($jugador) {
                        $jugador->numero_camiseta = $jugador->equipos->first()->pivot->numero_camiseta ?? 'N/A';
                        return $jugador;
                    });
            }
        }

        return view('arbitro.partido', compact(
            'juego', 
            'titularesLocalSeleccionados',
            'titularesVisitanteSeleccionados'
        ));
    }

    public function registrarAccion(Request $request, $juegoId)
    {
        $juego = Juego::findOrFail($juegoId);
        
        // Verificar que el juego esté en curso
        if ($juego->estado !== 'En Curso') {
            return response()->json(['error' => 'El juego no está en curso'], 400);
        }

        // Verificar permisos del árbitro
        $arbitro = Arbitro::where('user_id', Auth::id())->first();
        if (!$arbitro) {
            return response()->json(['error' => 'No se encontró el perfil de árbitro'], 403);
        }

        $tienePermiso = (
            $juego->arbitro_principal_id === $arbitro->id ||
            $juego->arbitro_auxiliar_id === $arbitro->id ||
            $juego->mesa_control_id === $arbitro->id
        );

        if (!$tienePermiso) {
            return response()->json(['error' => 'No tienes permisos para registrar acciones'], 403);
        }

        $validated = $request->validate([
            'jugador_id' => 'required|exists:jugadores,id',
            'equipo_id' => 'required|exists:equipos,id',
            'tipo_equipo' => 'required|in:Local,Visitante',
            'tipo_accion' => 'required|in:punto,falta',
            'valor' => 'required|integer|min:1|max:3',
        ]);

        DB::beginTransaction();
        try {
            // Buscar el registro en juego_alineaciones
            $alineacion = JuegoAlineacion::where([
                'juego_id' => $juego->id,
                'jugador_id' => $validated['jugador_id'],
                'equipo_id' => $validated['equipo_id'],
                'tipo_equipo' => $validated['tipo_equipo'],
            ])->first();

            if (!$alineacion) {
                return response()->json(['error' => 'Jugador no encontrado en las alineaciones'], 404);
            }

            if ($validated['tipo_accion'] === 'punto') {
                // Registrar puntos
                $alineacion->puntos += $validated['valor'];
                
                // Actualizar estadísticas específicas según el valor
                switch($validated['valor']) {
                    case 1:
                        $alineacion->tiros_libres_anotados++;
                        $alineacion->tiros_libres_intentados++;
                        break;
                    case 2:
                        $alineacion->tiros_2pts_anotados++;
                        $alineacion->tiros_2pts_intentados++;
                        break;
                    case 3:
                        $alineacion->tiros_3pts_anotados++;
                        $alineacion->tiros_3pts_intentados++;
                        break;
                }
                
                // Actualizar puntos del equipo
                if ($validated['tipo_equipo'] === 'Local') {
                    $juego->increment('puntos_local', $validated['valor']);
                } else {
                    $juego->increment('puntos_visitante', $validated['valor']);
                }
                
            } elseif ($validated['tipo_accion'] === 'falta') {
                // Registrar falta
                $alineacion->faltas_personales++;
            }

            $alineacion->save();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'jugador_id' => $validated['jugador_id'],
                'tipo_equipo' => $validated['tipo_equipo'],
                'puntos_jugador' => $alineacion->puntos,
                'puntos_equipo' => $validated['tipo_equipo'] === 'Local' ? $juego->puntos_local : $juego->puntos_visitante,
                'faltas_jugador' => $alineacion->faltas_personales
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar acción: '.$e->getMessage());
            return response()->json(['error' => 'Error al registrar la acción'], 500);
        }
    }

    public function iniciarPartidoOficial(Request $request, $juego)
    {
        $juego = Juego::findOrFail($juego);
        
        // Verificar permisos
        $arbitro = Arbitro::where('user_id', Auth::id())->first();
        if (!$arbitro) {
            return response()->json(['error' => 'No se encontró el perfil de árbitro'], 403);
        }

        $tienePermiso = (
            $juego->arbitro_principal_id === $arbitro->id ||
            $juego->arbitro_auxiliar_id === $arbitro->id ||
            $juego->mesa_control_id === $arbitro->id
        );

        if (!$tienePermiso) {
            return response()->json(['error' => 'No tienes permisos para iniciar este juego'], 403);
        }

        DB::beginTransaction();
        try {
            $titularesLocalIds = $request->input('titulares_local', []);
            $titularesVisitanteIds = $request->input('titulares_visitante', []);

            // Validar que hay jugadores seleccionados
            if (empty($titularesLocalIds)) {
                throw new \Exception('Debes seleccionar jugadores para el equipo local');
            }
            if (empty($titularesVisitanteIds)) {
                throw new \Exception('Debes seleccionar jugadores para el equipo visitante');
            }

            // Validar número de jugadores titulares (máximo 5 por equipo)
            if (count($titularesLocalIds) > 5) {
                throw new \Exception('El equipo local no puede tener más de 5 jugadores titulares');
            }
            if (count($titularesVisitanteIds) > 5) {
                throw new \Exception('El equipo visitante no puede tener más de 5 jugadores titulares');
            }

            // Crear alineaciones para el equipo local
            foreach ($titularesLocalIds as $jugadorId) {
                $numeroCamiseta = DB::table('equipo_jugadores')
                    ->where('jugador_id', $jugadorId)
                    ->where('equipo_id', $juego->equipo_local_id)
                    ->value('numero_camiseta');

                JuegoAlineacion::create([
                    'juego_id' => $juego->id,
                    'jugador_id' => $jugadorId,
                    'equipo_id' => $juego->equipo_local_id,
                    'tipo_equipo' => 'Local',
                    'es_titular' => true,
                    'esta_en_cancha' => true,
                    'numero_camiseta' => $numeroCamiseta ?? 0,
                    'posicion_jugada' => 'Base (PG)', // Puedes hacer esto dinámico si tienes la posición
                    'minuto_entrada' => 0, // Los titulares entran en el minuto 0
                    'puntos' => 0,
                    'tiros_libres_anotados' => 0,
                    'tiros_libres_intentados' => 0,
                    'tiros_2pts_anotados' => 0,
                    'tiros_2pts_intentados' => 0,
                    'tiros_3pts_anotados' => 0,
                    'tiros_3pts_intentados' => 0,
                    'asistencias' => 0,
                    'rebotes_defensivos' => 0,
                    'rebotes_ofensivos' => 0,
                    'rebotes_totales' => 0,
                    'robos' => 0,
                    'bloqueos' => 0,
                    'perdidas' => 0,
                    'faltas_personales' => 0,
                    'faltas_tecnicas' => 0,
                    'faltas_descalificantes' => 0,
                    'minutos_jugados' => 0,
                ]);
            }

            // Crear alineaciones para el equipo visitante
            foreach ($titularesVisitanteIds as $jugadorId) {
                $numeroCamiseta = DB::table('equipo_jugadores')
                    ->where('jugador_id', $jugadorId)
                    ->where('equipo_id', $juego->equipo_visitante_id)
                    ->value('numero_camiseta');

                JuegoAlineacion::create([
                    'juego_id' => $juego->id,
                    'jugador_id' => $jugadorId,
                    'equipo_id' => $juego->equipo_visitante_id,
                    'tipo_equipo' => 'Visitante',
                    'es_titular' => true,
                    'esta_en_cancha' => true,
                    'numero_camiseta' => $numeroCamiseta ?? 0,
                    'posicion_jugada' => 'Base (PG)', // Puedes hacer esto dinámico si tienes la posición
                    'minuto_entrada' => 0, // Los titulares entran en el minuto 0
                    'puntos' => 0,
                    'tiros_libres_anotados' => 0,
                    'tiros_libres_intentados' => 0,
                    'tiros_2pts_anotados' => 0,
                    'tiros_2pts_intentados' => 0,
                    'tiros_3pts_anotados' => 0,
                    'tiros_3pts_intentados' => 0,
                    'asistencias' => 0,
                    'rebotes_defensivos' => 0,
                    'rebotes_ofensivos' => 0,
                    'rebotes_totales' => 0,
                    'robos' => 0,
                    'bloqueos' => 0,
                    'perdidas' => 0,
                    'faltas_personales' => 0,
                    'faltas_tecnicas' => 0,
                    'faltas_descalificantes' => 0,
                    'minutos_jugados' => 0,
                ]);
            }

            // Registrar jugadores suplentes (si los hay en el request)
            $suplentesLocalIds = $request->input('suplentes_local', []);
            $suplentesVisitanteIds = $request->input('suplentes_visitante', []);

            // Crear registros para suplentes del equipo local
            foreach ($suplentesLocalIds as $jugadorId) {
                $numeroCamiseta = DB::table('equipo_jugadores')
                    ->where('jugador_id', $jugadorId)
                    ->where('equipo_id', $juego->equipo_local_id)
                    ->value('numero_camiseta');

                JuegoAlineacion::create([
                    'juego_id' => $juego->id,
                    'jugador_id' => $jugadorId,
                    'equipo_id' => $juego->equipo_local_id,
                    'tipo_equipo' => 'Local',
                    'es_titular' => false,
                    'esta_en_cancha' => false,
                    'numero_camiseta' => $numeroCamiseta ?? 0,
                    'posicion_jugada' => 'Base (PG)', // Ajustar según necesidad
                    'minuto_entrada' => null, // No han entrado aún
                    'puntos' => 0,
                    'tiros_libres_anotados' => 0,
                    'tiros_libres_intentados' => 0,
                    'tiros_2pts_anotados' => 0,
                    'tiros_2pts_intentados' => 0,
                    'tiros_3pts_anotados' => 0,
                    'tiros_3pts_intentados' => 0,
                    'asistencias' => 0,
                    'rebotes_defensivos' => 0,
                    'rebotes_ofensivos' => 0,
                    'rebotes_totales' => 0,
                    'robos' => 0,
                    'bloqueos' => 0,
                    'perdidas' => 0,
                    'faltas_personales' => 0,
                    'faltas_tecnicas' => 0,
                    'faltas_descalificantes' => 0,
                    'minutos_jugados' => 0,
                ]);
            }

            // Crear registros para suplentes del equipo visitante
            foreach ($suplentesVisitanteIds as $jugadorId) {
                $numeroCamiseta = DB::table('equipo_jugadores')
                    ->where('jugador_id', $jugadorId)
                    ->where('equipo_id', $juego->equipo_visitante_id)
                    ->value('numero_camiseta');

                JuegoAlineacion::create([
                    'juego_id' => $juego->id,
                    'jugador_id' => $jugadorId,
                    'equipo_id' => $juego->equipo_visitante_id,
                    'tipo_equipo' => 'Visitante',
                    'es_titular' => false,
                    'esta_en_cancha' => false,
                    'numero_camiseta' => $numeroCamiseta ?? 0,
                    'posicion_jugada' => 'Base (PG)', // Ajustar según necesidad
                    'minuto_entrada' => null, // No han entrado aún
                    'puntos' => 0,
                    'tiros_libres_anotados' => 0,
                    'tiros_libres_intentados' => 0,
                    'tiros_2pts_anotados' => 0,
                    'tiros_2pts_intentados' => 0,
                    'tiros_3pts_anotados' => 0,
                    'tiros_3pts_intentados' => 0,
                    'asistencias' => 0,
                    'rebotes_defensivos' => 0,
                    'rebotes_ofensivos' => 0,
                    'rebotes_totales' => 0,
                    'robos' => 0,
                    'bloqueos' => 0,
                    'perdidas' => 0,
                    'faltas_personales' => 0,
                    'faltas_tecnicas' => 0,
                    'faltas_descalificantes' => 0,
                    'minutos_jugados' => 0,
                ]);
            }

            // Actualizar estado del juego
            $juego->update([
                'estado' => 'En Curso',
                'cuarto_actual' => 1,
                'puntos_local' => 0,
                'puntos_visitante' => 0,
                'tiempo_inicio' => now(), // Registrar cuando inició el partido
            ]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Partido iniciado oficialmente',
                'data' => [
                    'juego_id' => $juego->id,
                    'titulares_local' => count($titularesLocalIds),
                    'titulares_visitante' => count($titularesVisitanteIds),
                    'suplentes_local' => count($suplentesLocalIds),
                    'suplentes_visitante' => count($suplentesVisitanteIds),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al iniciar partido: '.$e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Función auxiliar para obtener número de camiseta
    private function getNumeroCamiseta($jugadorId, $equipoId)
    {
        return \App\Models\EquipoJugador::where('jugador_id', $jugadorId)
            ->where('equipo_id', $equipoId)
            ->value('numero_camiseta') ?? 0;
    }

    // Método para registrar estadísticas simples (ACTUALIZADO)
    public function registrarEstadisticaSimple(Request $request, $juegoId)
    {
        $juego = Juego::findOrFail($juegoId);
        
        // Verificar que el juego esté en curso
        if ($juego->estado !== 'En Curso') {
            return response()->json(['error' => 'El juego no está en curso'], 400);
        }

        // Verificar permisos del árbitro
        $arbitro = Arbitro::where('user_id', Auth::id())->first();
        if (!$arbitro) {
            return response()->json(['error' => 'No se encontró el perfil de árbitro'], 403);
        }

        $tienePermiso = (
            $juego->arbitro_principal_id === $arbitro->id ||
            $juego->arbitro_auxiliar_id === $arbitro->id ||
            $juego->mesa_control_id === $arbitro->id
        );

        if (!$tienePermiso) {
            return response()->json(['error' => 'No tienes permisos para registrar estadísticas'], 403);
        }

        // VALIDACIÓN ACTUALIZADA - Ahora acepta alineacion_id en lugar de jugador_id + equipo_id
        $validated = $request->validate([
            'alineacion_id' => 'required|exists:juego_alineaciones,id',
            'tipo_equipo' => 'required|in:Local,Visitante',
            'tipo_estadistica' => 'required|in:asistencia,rebote_defensivo,rebote_ofensivo,robo,bloqueo,perdida,falta_personal,falta_tecnica,falta_descalificante',
        ]);

        DB::beginTransaction();
        try {
            // SIMPLIFICADO: Buscar directamente por ID de alineación
            $alineacion = JuegoAlineacion::where([
                'id' => $validated['alineacion_id'],
                'juego_id' => $juego->id,
                'tipo_equipo' => $validated['tipo_equipo'], // Verificación adicional de seguridad
            ])->first();

            if (!$alineacion) {
                DB::rollBack();
                return response()->json(['error' => 'Registro de alineación no encontrado'], 404);
            }

            // Actualizar la estadística correspondiente
            switch($validated['tipo_estadistica']) {
                case 'asistencia':
                    $alineacion->asistencias++;
                    break;
                    
                case 'rebote_defensivo':
                    $alineacion->rebotes_defensivos++;
                    $alineacion->rebotes_totales++;
                    break;
                    
                case 'rebote_ofensivo':
                    $alineacion->rebotes_ofensivos++;
                    $alineacion->rebotes_totales++;
                    break;
                    
                case 'robo':
                    $alineacion->robos++;
                    break;
                    
                case 'bloqueo':
                    $alineacion->bloqueos++;
                    break;
                    
                case 'perdida':
                    $alineacion->perdidas++;
                    break;
                    
                case 'falta_personal':
                    $alineacion->faltas_personales++;
                    break;
                    
                case 'falta_tecnica':
                    $alineacion->faltas_tecnicas++;
                    break;
                    
                case 'falta_descalificante':
                    $alineacion->faltas_descalificantes++;
                    $alineacion->esta_en_cancha = false; // El jugador sale de la cancha
                    break;
                    
                default:
                    DB::rollBack();
                    return response()->json(['error' => 'Tipo de estadística no válido'], 400);
            }

            // Guardar la alineación
            $alineacion->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'jugador_id' => $alineacion->jugador_id, // Devolvemos el jugador_id para actualizar UI
                'alineacion_id' => $alineacion->id,
                'tipo_equipo' => $validated['tipo_equipo'],
                'tipo_estadistica' => $validated['tipo_estadistica'],
                'mensaje' => 'Estadística registrada correctamente',
                'estadisticas_actualizadas' => [
                    'asistencias' => $alineacion->asistencias,
                    'rebotes_defensivos' => $alineacion->rebotes_defensivos,
                    'rebotes_ofensivos' => $alineacion->rebotes_ofensivos,
                    'rebotes_totales' => $alineacion->rebotes_totales,
                    'robos' => $alineacion->robos,
                    'bloqueos' => $alineacion->bloqueos,
                    'perdidas' => $alineacion->perdidas,
                    'faltas_personales' => $alineacion->faltas_personales,
                    'faltas_tecnicas' => $alineacion->faltas_tecnicas,
                    'faltas_descalificantes' => $alineacion->faltas_descalificantes,
                    'esta_en_cancha' => $alineacion->esta_en_cancha,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al registrar estadística: ' . $e->getMessage(), [
                'juego_id' => $juegoId,
                'request_data' => $validated ?? $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Error al registrar la estadística',
                'debug_message' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    // Método updatePlayerStats también actualizado
    public function updatePlayerStats(Request $request, $juegoId)
    {
        $juego = Juego::findOrFail($juegoId);
        $user = Auth::user();
        $arbitro = Arbitro::where('user_id', $user->id)->first();

        if (!$arbitro) {
            return response()->json(['error' => 'No se encontró el perfil de árbitro'], 403);
        }

        // Verificar permisos
        $tienePermiso = (
            $juego->arbitro_principal_id === $arbitro->id ||
            $juego->arbitro_auxiliar_id === $arbitro->id ||
            $juego->mesa_control_id === $arbitro->id
        );

        if (!$tienePermiso) {
            return response()->json(['error' => 'No tienes permisos para actualizar estadísticas'], 403);
        }

        // VALIDACIÓN ACTUALIZADA
        $validated = $request->validate([
            'alineacion_id' => 'required|exists:juego_alineaciones,id',
            'tipo_equipo' => 'required|in:Local,Visitante',
            'tipo_punto' => 'required|in:2pts,3pts,tiro_libre',
            'anotado' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            // SIMPLIFICADO: Buscar directamente por ID de alineación
            $alineacion = JuegoAlineacion::where([
                'id' => $validated['alineacion_id'],
                'juego_id' => $juego->id,
                'tipo_equipo' => $validated['tipo_equipo'],
            ])->first();

            if (!$alineacion) {
                return response()->json(['error' => 'Registro de alineación no encontrado'], 404);
            }

            // Actualizar estadísticas según el tipo de punto
            $puntosSumar = 0;
            switch($validated['tipo_punto']) {
                case '2pts':
                    $alineacion->tiros_2pts_intentados++;
                    if ($validated['anotado']) {
                        $alineacion->tiros_2pts_anotados++;
                        $puntosSumar = 2;
                    }
                    break;
                    
                case '3pts':
                    $alineacion->tiros_3pts_intentados++;
                    if ($validated['anotado']) {
                        $alineacion->tiros_3pts_anotados++;
                        $puntosSumar = 3;
                    }
                    break;
                    
                case 'tiro_libre':
                    $alineacion->tiros_libres_intentados++;
                    if ($validated['anotado']) {
                        $alineacion->tiros_libres_anotados++;
                        $puntosSumar = 1;
                    }
                    break;
            }

            // Si anotó, sumar puntos
            if ($validated['anotado'] && $puntosSumar > 0) {
                $alineacion->puntos += $puntosSumar;
                
                // Actualizar puntos del equipo en el juego
                if ($validated['tipo_equipo'] === 'Local') {
                    $juego->increment('puntos_local', $puntosSumar);
                } else {
                    $juego->increment('puntos_visitante', $puntosSumar);
                }
            }

            $alineacion->save();

            DB::commit();
            
            return response()->json([
                'success' => true,
                'jugador_id' => $alineacion->jugador_id,
                'alineacion_id' => $alineacion->id,
                'tipo_equipo' => $validated['tipo_equipo'],
                'puntos_jugador' => $alineacion->puntos,
                'puntos_equipo' => $validated['tipo_equipo'] === 'Local' ? $juego->puntos_local : $juego->puntos_visitante,
                'estadisticas' => [
                    'tiros_2pts' => $alineacion->tiros_2pts_anotados . '/' . $alineacion->tiros_2pts_intentados,
                    'tiros_3pts' => $alineacion->tiros_3pts_anotados . '/' . $alineacion->tiros_3pts_intentados,
                    'tiros_libres' => $alineacion->tiros_libres_anotados . '/' . $alineacion->tiros_libres_intentados,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar estadísticas de puntos: '.$e->getMessage());
            return response()->json(['error' => 'Error al actualizar las estadísticas'], 500);
        }
    }

    // Método para obtener estadísticas en tiempo real
    public function obtenerEstadisticasJuego($juegoId)
    {
        $juego = Juego::with(['juegoAlineaciones.jugador'])->findOrFail($juegoId);
        
        $estadisticasLocal = $juego->juegoAlineaciones
            ->where('tipo_equipo', 'Local')
            ->map(function($alineacion) {
                return [
                    'jugador_id' => $alineacion->jugador_id,
                    'nombre' => $alineacion->jugador->nombre,
                    'puntos' => $alineacion->puntos,
                    'asistencias' => $alineacion->asistencias,
                    'rebotes_totales' => $alineacion->rebotes_totales,
                    'robos' => $alineacion->robos,
                    'bloqueos' => $alineacion->bloqueos,
                    'faltas_personales' => $alineacion->faltas_personales,
                    'tiros_2pts' => $alineacion->tiros_2pts_anotados . '/' . $alineacion->tiros_2pts_intentados,
                    'tiros_3pts' => $alineacion->tiros_3pts_anotados . '/' . $alineacion->tiros_3pts_intentados,
                    'tiros_libres' => $alineacion->tiros_libres_anotados . '/' . $alineacion->tiros_libres_intentados,
                ];
            });

        $estadisticasVisitante = $juego->juegoAlineaciones
            ->where('tipo_equipo', 'Visitante')
            ->map(function($alineacion) {
                return [
                    'jugador_id' => $alineacion->jugador_id,
                    'nombre' => $alineacion->jugador->nombre,
                    'puntos' => $alineacion->puntos,
                    'asistencias' => $alineacion->asistencias,
                    'rebotes_totales' => $alineacion->rebotes_totales,
                    'robos' => $alineacion->robos,
                    'bloqueos' => $alineacion->bloqueos,
                    'faltas_personales' => $alineacion->faltas_personales,
                    'tiros_2pts' => $alineacion->tiros_2pts_anotados . '/' . $alineacion->tiros_2pts_intentados,
                    'tiros_3pts' => $alineacion->tiros_3pts_anotados . '/' . $alineacion->tiros_3pts_intentados,
                    'tiros_libres' => $alineacion->tiros_libres_anotados . '/' . $alineacion->tiros_libres_intentados,
                ];
            });

        return response()->json([
            'success' => true,
            'juego' => [
                'puntos_local' => $juego->puntos_local,
                'puntos_visitante' => $juego->puntos_visitante,
                'faltas_local' => $juego->faltas_local,
                'faltas_visitante' => $juego->faltas_visitante,
            ],
            'estadisticas_local' => $estadisticasLocal,
            'estadisticas_visitante' => $estadisticasVisitante,
        ]);
    }

    // Método para corregir estadísticas (en caso de error)
    public function corregirEstadistica(Request $request, $juegoId)
    {
        $juego = Juego::findOrFail($juegoId);
        
        $validated = $request->validate([
            'jugador_id' => 'required|exists:jugadores,id',
            'tipo_equipo' => 'required|in:Local,Visitante',
            'campo' => 'required|string',
            'valor' => 'required|integer|min:0',
        ]);

        $arbitro = Arbitro::where('user_id', Auth::id())->first();
        if (!$arbitro || $juego->mesa_control_id !== $arbitro->id) {
            return response()->json(['error' => 'No tienes permisos para corregir estadísticas'], 403);
        }

        DB::beginTransaction();
        try {
            $alineacion = JuegoAlineacion::where([
                'juego_id' => $juego->id,
                'jugador_id' => $validated['jugador_id'],
                'tipo_equipo' => $validated['tipo_equipo'],
            ])->firstOrFail();

            // Campos permitidos para corrección
            $camposPermitidos = [
                'puntos', 'asistencias', 'rebotes_defensivos', 'rebotes_ofensivos', 
                'robos', 'bloqueos', 'perdidas', 'faltas_personales',
                'tiros_2pts_anotados', 'tiros_2pts_intentados',
                'tiros_3pts_anotados', 'tiros_3pts_intentados',
                'tiros_libres_anotados', 'tiros_libres_intentados'
            ];

            if (!in_array($validated['campo'], $camposPermitidos)) {
                return response()->json(['error' => 'Campo no permitido para corrección'], 400);
            }

            $alineacion->{$validated['campo']} = $validated['valor'];
            
            // Recalcular rebotes totales si se modificó algún rebote
            if (in_array($validated['campo'], ['rebotes_defensivos', 'rebotes_ofensivos'])) {
                $alineacion->rebotes_totales = $alineacion->rebotes_defensivos + $alineacion->rebotes_ofensivos;
            }

            $alineacion->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'mensaje' => 'Estadística corregida correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al corregir estadística: '.$e->getMessage());
            return response()->json(['error' => 'Error al corregir la estadística'], 500);
        }
    }


    
    public function __construct(
        private ResultadosPorPuntos $torneoService
    ) {}

    public function finalizarPartido(Request $request, $juego)
    {
        try {
            Log::info("Iniciando proceso de finalización para partido ID: {$juego}");
            
            $juego = Juego::findOrFail($juego);
            $juego->estado = 'Finalizado';
            $juego->save();

            // Solo procesar en torneo si tiene torneo_id
            if ($juego->torneo_id) {
                try {
                    $this->torneoService->procesarResultadoJuego($juego);
                } catch (\Exception $e) {
                    Log::error("Error al procesar resultado en ResultadosPorPuntos", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Continúa aunque falle el procesamiento del torneo
                }
            }

            return redirect()
                ->route('arbitro.partido.resultados', ['juego' => $juego->id])
                ->with('success', 'Partido finalizado correctamente.');

        } catch (\Exception $e) {
            Log::error("Error al finalizar partido", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al procesar el partido: '.$e->getMessage());
        }
    }

    public function resultadosPartido($partidoId)
    {
        $arbitro = Arbitro::where('user_id', Auth::id())->first();
        
        if (!$arbitro) {
            return redirect()->route('login')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $juego = Juego::where('id', $partidoId)
            ->where('estado', 'Finalizado')
            ->where(function($query) use ($arbitro) {
                $query->where('arbitro_principal_id', $arbitro->id)
                    ->orWhere('arbitro_auxiliar_id', $arbitro->id)
                    ->orWhere('mesa_control_id', $arbitro->id);
            })
            ->with([
                'equipoLocal',
                'equipoVisitante', 
                'cancha', 
                'torneo',
                'juegoAlineaciones.jugador' => function($query) {
                    $query->select('id', 'nombre', 'foto_url');
                }
            ])
            ->firstOrFail();

        // Separar estadísticas por equipo
        $estadisticasLocal = $juego->juegoAlineaciones
            ->where('tipo_equipo', 'Local')
            ->sortByDesc('puntos');

        $estadisticasVisitante = $juego->juegoAlineaciones
            ->where('tipo_equipo', 'Visitante')
            ->sortByDesc('puntos');

        // Calcular totales por equipo
        $totalesLocal = [
            'puntos' => $estadisticasLocal->sum('puntos'),
            'tiros_libres_anotados' => $estadisticasLocal->sum('tiros_libres_anotados'),
            'tiros_libres_intentados' => $estadisticasLocal->sum('tiros_libres_intentados'),
            'tiros_2pts_anotados' => $estadisticasLocal->sum('tiros_2pts_anotados'),
            'tiros_2pts_intentados' => $estadisticasLocal->sum('tiros_2pts_intentados'),
            'tiros_3pts_anotados' => $estadisticasLocal->sum('tiros_3pts_anotados'),
            'tiros_3pts_intentados' => $estadisticasLocal->sum('tiros_3pts_intentados'),
            'asistencias' => $estadisticasLocal->sum('asistencias'),
            'rebotes_totales' => $estadisticasLocal->sum('rebotes_totales'),
            'rebotes_defensivos' => $estadisticasLocal->sum('rebotes_defensivos'),
            'rebotes_ofensivos' => $estadisticasLocal->sum('rebotes_ofensivos'),
            'robos' => $estadisticasLocal->sum('robos'),
            'bloqueos' => $estadisticasLocal->sum('bloqueos'),
            'perdidas' => $estadisticasLocal->sum('perdidas'),
            'faltas_personales' => $estadisticasLocal->sum('faltas_personales'),
            'minutos_jugados' => $estadisticasLocal->sum('minutos_jugados'),
        ];

        $totalesVisitante = [
            'puntos' => $estadisticasVisitante->sum('puntos'),
            'tiros_libres_anotados' => $estadisticasVisitante->sum('tiros_libres_anotados'),
            'tiros_libres_intentados' => $estadisticasVisitante->sum('tiros_libres_intentados'),
            'tiros_2pts_anotados' => $estadisticasVisitante->sum('tiros_2pts_anotados'),
            'tiros_2pts_intentados' => $estadisticasVisitante->sum('tiros_2pts_intentados'),
            'tiros_3pts_anotados' => $estadisticasVisitante->sum('tiros_3pts_anotados'),
            'tiros_3pts_intentados' => $estadisticasVisitante->sum('tiros_3pts_intentados'),
            'asistencias' => $estadisticasVisitante->sum('asistencias'),
            'rebotes_totales' => $estadisticasVisitante->sum('rebotes_totales'),
            'rebotes_defensivos' => $estadisticasVisitante->sum('rebotes_defensivos'),
            'rebotes_ofensivos' => $estadisticasVisitante->sum('rebotes_ofensivos'),
            'robos' => $estadisticasVisitante->sum('robos'),
            'bloqueos' => $estadisticasVisitante->sum('bloqueos'),
            'perdidas' => $estadisticasVisitante->sum('perdidas'),
            'faltas_personales' => $estadisticasVisitante->sum('faltas_personales'),
            'minutos_jugados' => $estadisticasVisitante->sum('minutos_jugados'),
        ];

        return view('arbitro.resultado_partido', compact(
            'juego', 
            'estadisticasLocal', 
            'estadisticasVisitante',
            'totalesLocal',
            'totalesVisitante'
        ));
    }

    public function controlarTiempo(Request $request, $juegoId)
    {
        try {
            $juego = \App\Models\Juego::findOrFail($juegoId);
            
            // Verificar permisos del árbitro
            $arbitro = Arbitro::where('user_id', Auth::id())->first();
            if (!$arbitro) {
                return response()->json([
                    'success' => false,
                    'error' => 'No se encontró el perfil de árbitro'
                ], 403);
            }

            $tienePermiso = (
                $juego->arbitro_principal_id === $arbitro->id ||
                $juego->arbitro_auxiliar_id === $arbitro->id ||
                $juego->mesa_control_id === $arbitro->id
            );

            if (!$tienePermiso) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permisos para controlar el tiempo'
                ], 403);
            }

            $request->validate([
                'accion' => 'required|in:iniciar,pausar,reiniciar,avanzar_cuarto'
            ]);

            $ahora = now();
            $duracionCuarto = $juego->duracion_cuarto * 60; // en segundos
            $duracionDescanso = $juego->duracion_descanso * 60; // en segundos

            // Inicializar campos si es necesario
            if (is_null($juego->tiempo_restante) || is_null($juego->ultimo_cambio_tiempo)) {
                $tiempoInicial = $juego->en_descanso ? $duracionDescanso : $duracionCuarto;
                $juego->tiempo_restante = $tiempoInicial;
                $juego->ultimo_cambio_tiempo = $ahora;
                $juego->save(); // Guardar inicialización
            }

            switch($request->accion) {
                case 'iniciar':
                    // Permitir iniciar si está pausado O en descanso
                    if ($juego->estado_tiempo === 'corriendo') {
                        return response()->json([
                            'success' => false,
                            'error' => 'El tiempo ya está corriendo'
                        ], 400);
                    }

                    $juego->estado_tiempo = 'corriendo';
                    $juego->ultimo_cambio_tiempo = $ahora;
                    break;

                case 'pausar':
                    if ($juego->estado_tiempo !== 'corriendo') {
                        return response()->json([
                            'success' => false,
                            'error' => 'El tiempo no está corriendo'
                        ], 400);
                    }

                    // CORRECCIÓN: Calcular tiempo restante con mayor precisión
                    $tiempoInicio = \Carbon\Carbon::parse($juego->ultimo_cambio_tiempo);
                    $segundosTranscurridos = $tiempoInicio->diffInSeconds($ahora);
                    
                    // IMPORTANTE: Limitar a máximo 1 segundo de delay para evitar tiempo extra
                    if ($segundosTranscurridos > ($juego->tiempo_restante + 1)) {
                        $segundosTranscurridos = $juego->tiempo_restante;
                    }
                    
                    $nuevoTiempoRestante = max(0, (int)($juego->tiempo_restante - $segundosTranscurridos));
                    
                    $juego->tiempo_restante = $nuevoTiempoRestante;
                    $juego->estado_tiempo = $juego->en_descanso ? 'descanso' : 'pausado';
                    $juego->ultimo_cambio_tiempo = $ahora;
                    
                    // Si el tiempo llegó a 0, implementar lógica de finalización automática
                    if ($juego->tiempo_restante <= 0) {
                        $juego->tiempo_restante = 0;
                    }
                    break;

                case 'reiniciar':
                    // Reiniciar el tiempo según el estado actual
                    if ($juego->en_descanso) {
                        $juego->tiempo_restante = $duracionDescanso;
                        $juego->estado_tiempo = 'descanso';
                    } else {
                        $juego->tiempo_restante = $duracionCuarto;
                        $juego->estado_tiempo = 'pausado';
                    }
                    $juego->ultimo_cambio_tiempo = $ahora;
                    break;

                case 'avanzar_cuarto':
                    if ($juego->estado_tiempo === 'corriendo') {
                        return response()->json([
                            'success' => false,
                            'error' => 'Debes pausar el tiempo antes de avanzar de cuarto'
                        ], 400);
                    }

                    // Lógica para avanzar cuarto
                    if ($juego->en_descanso) {
                        // Si estamos en descanso, avanzar al siguiente cuarto
                        $juego->cuarto_actual++;
                        $juego->en_descanso = false;
                        $juego->tiempo_restante = $duracionCuarto;
                        $juego->estado_tiempo = 'pausado';
                        
                        if ($juego->cuarto_actual > 4) {
                            $juego->cuarto_actual = 4; // Mantener en 4 máximo
                        }
                    } else {
                        // Estamos en un cuarto, verificar si hay descanso
                        if ($juego->cuarto_actual <= 3) {
                            // Cuartos 1, 2, 3 tienen descanso después
                            $juego->en_descanso = true;
                            $juego->estado_tiempo = 'descanso';
                            $juego->tiempo_restante = $duracionDescanso;
                        } else {
                            // Cuarto 4 - NO finalizar automáticamente
                            // Solo pausar el tiempo, el árbitro deberá finalizar manualmente
                            $juego->estado_tiempo = 'pausado';
                        }
                    }
                    
                    $juego->ultimo_cambio_tiempo = $ahora;
                    break;
            }

            $juego->save();

            // Calcular tiempo actual para la respuesta (sin delay adicional)
            $tiempoParaRespuesta = $juego->tiempo_restante;

            // Determinar el estado para la respuesta
            $estadoRespuesta = $juego->estado_tiempo;
            if ($juego->en_descanso && $juego->estado_tiempo !== 'corriendo') {
                $estadoRespuesta = 'descanso';
            }

            // Determinar si se puede finalizar el partido
            $puedeFinalizarPartido = false;
            
            if ($juego->cuarto_actual >= 4 && 
                !$juego->en_descanso && 
                $juego->estado_tiempo !== 'corriendo' &&
                $juego->estado !== 'Finalizado') {
                $puedeFinalizarPartido = true;
            }

            return response()->json([
                'success' => true,
                'cuarto_actual' => $juego->cuarto_actual,
                'estado_tiempo' => $estadoRespuesta,
                'tiempo_restante' => (int)$tiempoParaRespuesta,
                'estado_partido' => $juego->estado,
                'en_descanso' => $juego->en_descanso,
                'puede_finalizar' => $puedeFinalizarPartido
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en controlarTiempo: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function obtenerEstadoTiempo($juegoId)
    {
        try {
            $juego = \App\Models\Juego::findOrFail($juegoId);
            
            // CORRECCIÓN: Usar método del modelo para mayor precisión
            $tiempoRestante = $juego->getTiempoActual();

            // Determinar estado para respuesta
            $estadoRespuesta = $juego->estado_tiempo;
            if ($juego->en_descanso && $juego->estado_tiempo !== 'corriendo') {
                $estadoRespuesta = 'descanso';
            }

            // Determinar si se puede finalizar el partido
            $puedeFinalizarPartido = false;
            
            if ($juego->cuarto_actual >= 4 && 
                !$juego->en_descanso && 
                $juego->estado_tiempo !== 'corriendo' &&
                $juego->estado !== 'Finalizado') {
                $puedeFinalizarPartido = true;
            }

            return response()->json([
                'success' => true,
                'cuarto_actual' => $juego->cuarto_actual,
                'estado_tiempo' => $estadoRespuesta,
                'tiempo_restante' => (int)$tiempoRestante,
                'estado_partido' => $juego->estado,
                'en_descanso' => $juego->en_descanso,
                'puede_finalizar' => $puedeFinalizarPartido
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en obtenerEstadoTiempo: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}