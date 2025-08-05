<?php
namespace App\Http\Controllers;

use App\Models\Jugador;
use App\Models\Equipo;
use App\Models\User;
use App\Models\Juego;
use App\Models\Liga;
use App\Models\Categoria;
use App\Models\Temporada;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
        
    public function dashboard()
    {
        $stats = [
            'total_ligas' => Liga::count(),
            'total_categorias' => Categoria::count(),
            'total_equipos' => Equipo::count(),
            'total_temporadas' => Temporada::count(),
            'usuarios_registrados' => User::count(),
            'ultimos_jugadores' => Jugador::latest()->take(5)->get()
        ];

        // Obtener juegos en vivo
        $juegosEnVivo = Juego::with(['equipoLocal', 'equipoVisitante', 'cancha', 'torneo'])
            ->where('estado', 'En Curso')
            ->where('activo', true)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->take(3)
            ->get();

        // Obtener próximos partidos
        $proximosPartidos = Juego::with(['equipoLocal', 'equipoVisitante', 'cancha', 'torneo'])
            ->where('estado', 'Programado')
            ->where('activo', true)
            ->where('fecha', '>=', now()->format('Y-m-d'))
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->take(3)
            ->get();

        // Obtener resultados recientes
        $resultadosRecientes = Juego::with(['equipoLocal', 'equipoVisitante', 'torneo'])
            ->where('estado', 'Finalizado')
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->take(3)
            ->get();

        // Torneos programados
        $torneosProgramados = Torneo::with(['liga', 'equipos'])
            ->where('estado', 'Programado')
            ->where('fecha_fin', '>=', now())
            ->orderBy('fecha_inicio', 'asc')
            ->take(3)
            ->get();

        // Actividades recientes
        $actividadesRecientes = collect()
            ->merge($this->getUltimosRegistros('App\Models\Equipo', 'equipo_creado', 'Equipo: '))
            ->merge($this->getUltimosRegistros('App\Models\Jugador', 'jugador_registrado', 'Jugador: '))
            ->merge($this->getUltimosRegistros('App\Models\Torneo', 'torneo_creado', 'Torneo: '))
            ->sortByDesc('created_at')
            ->take(5);

        return view('admin.dashboard', compact(
            'stats', 
            'juegosEnVivo', 
            'proximosPartidos', 
            'resultadosRecientes', 
            'torneosProgramados', 
            'actividadesRecientes'
        ));
    }


    public function getCategorias(Liga $liga): JsonResponse
    {
        try {
            $categorias = $liga->categorias()
                ->where('activo', true)
                ->select('id', 'nombre', 'edad_minima', 'edad_maxima')
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'categorias' => $categorias,
                'liga' => [
                    'id' => $liga->id,
                    'nombre' => $liga->nombre
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las categorías',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Nuevo método para obtener categorías por liga
    public function getCategoriasByLiga($ligaId)
    {
        $categorias = Categoria::where('liga_id', $ligaId)->orderBy('nombre')->get();
        return response()->json($categorias);
    }


    private function getUltimosRegistros($modelClass, $tipo, $mensajeBase) {
        return $modelClass::latest()
            ->take(3)
            ->get()
            ->map(function ($item) use ($tipo, $mensajeBase) {
                return [
                    'tipo' => $tipo,
                    'descripcion' => $this->getPrefijoTipo($tipo) . $item->nombre, // Usa método nuevo
                    'created_at' => $item->created_at,
                    'icono' => $this->getIconoPorTipo($tipo),
                    'user' => $item->createdBy ?? null
                ];
            });
    }

    private function getPrefijoTipo($tipo) {
        $prefijos = [
            'equipo_creado' => 'Equipo creado: ',
            'jugador_registrado' => 'Jugador agregado: ',
            'torneo_creado' => 'Torneo creado: '
        ];
        return $prefijos[$tipo] ?? 'Registro: ';
    }

    private function getIconoPorTipo($tipo) {
        $iconos = [
            'equipo_creado' => 'fa-users',          // Icono para equipos
            'jugador_registrado' => 'fa-user',     // Icono para jugadores
            'torneo_creado' => 'fa-trophy'         // Icono para torneos
        ];
        
        return $iconos[$tipo] ?? 'fa-circle-info';  // Icono por defecto
    }
}