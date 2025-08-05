<?php

namespace App\Http\Controllers;

use App\Models\Entrenador;
use App\Models\Equipo;
use App\Models\Juego;
use App\Models\Jugador;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EntrenadorDashboardController extends Controller
{
   
    public function dashboard()
    {
        $user = Auth::user();
        
        // Obtener el entrenador asociado al usuario
        $entrenador = Entrenador::where('email', $user->email)->first();

        if (!$entrenador) {
            return redirect()->route('login')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // Obtener el equipo principal del entrenador (asumiendo que un entrenador tiene un equipo principal)
        $equipo = $entrenador->equipos()->first();

        if (!$equipo) {
            return view('equipos.dashboard', [
                'entrenador' => $entrenador,
                'sin_equipo' => true
            ]);
        }

        // Obtener los jugadores del equipo con paginación
        $jugadores = $equipo->jugadores()->paginate(10);

        // Obtener los próximos partidos del equipo
        $proximosPartidos = Juego::where(function($query) use ($equipo) {
                $query->where('equipo_local_id', $equipo->id)
                      ->orWhere('equipo_visitante_id', $equipo->id);
            })
            ->where('estado', 'Programado')
            ->with(['equipoLocal', 'equipoVisitante', 'cancha', 'torneo'])
            ->orderBy('fecha', 'asc')
            ->take(5)
            ->get();

        // Obtener partidos recientes
        $partidosRecientes = Juego::where(function($query) use ($equipo) {
                $query->where('equipo_local_id', $equipo->id)
                      ->orWhere('equipo_visitante_id', $equipo->id);
            })
            ->where('estado', 'Finalizado')
            ->with(['equipoLocal', 'equipoVisitante', 'torneo'])
            ->orderBy('fecha', 'desc')
            ->take(5)
            ->get();

        // Obtener torneos activos del equipo
        $torneosActivos = $equipo->torneos()
            ->where('estado', 'En Progreso')
            ->get();

        // Estadísticas del equipo
        $estadisticas = [
            'total_jugadores' => $equipo->jugadores()->count(),
            'partidos_ganados' => $this->contarPartidosGanados($equipo),
            'partidos_perdidos' => $this->contarPartidosPerdidos($equipo),
            'partidos_empatados' => $this->contarPartidosEmpatados($equipo),
        ];

        return view('equipos.dashboard', compact(
            'entrenador',
            'equipo',
            'jugadores',
            'proximosPartidos',
            'partidosRecientes',
            'torneosActivos',
            'estadisticas'
        ));
    }

    // Métodos auxiliares para estadísticas
    private function contarPartidosGanados(Equipo $equipo)
    {
        return Juego::where(function($query) use ($equipo) {
            $query->where('equipo_local_id', $equipo->id)
                  ->where('resultado', 'Local')
                  ->orWhere(function($q) use ($equipo) {
                      $q->where('equipo_visitante_id', $equipo->id)
                        ->where('resultado', 'Visitante');
                  });
        })->where('estado', 'Finalizado')->count();
    }

    private function contarPartidosPerdidos(Equipo $equipo)
    {
        return Juego::where(function($query) use ($equipo) {
            $query->where('equipo_local_id', $equipo->id)
                  ->where('resultado', 'Visitante')
                  ->orWhere(function($q) use ($equipo) {
                      $q->where('equipo_visitante_id', $equipo->id)
                        ->where('resultado', 'Local');
                  });
        })->where('estado', 'Finalizado')->count();
    }

    private function contarPartidosEmpatados(Equipo $equipo)
    {
        return Juego::where(function($query) use ($equipo) {
            $query->where('equipo_local_id', $equipo->id)
                  ->orWhere('equipo_visitante_id', $equipo->id);
        })->where('resultado', 'Empate')
          ->where('estado', 'Finalizado')
          ->count();
    }
}