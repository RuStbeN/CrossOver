<?php

namespace App\Http\Controllers;

use App\Models\Liga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class LigaController extends Controller
{
    public function index(Request $request)
    {
        \Log::info('Acceso a listado de ligas', [
            'user_id' => Auth::id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        try {
            // Capturar parámetros de filtro
            $search = $request->get('search');
            $estado = $request->get('estado');
            $ordenar = $request->get('ordenar', 'created_at');
            $direccion = $request->get('direccion', 'desc');
            
            // Construir query base
            $query = Liga::withCount(['categorias', 'equipos']);
            
            // Aplicar filtros
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'like', '%' . $search . '%')
                    ->orWhere('descripcion', 'like', '%' . $search . '%');
                });
            }
            
            if ($estado !== null && $estado !== '') {
                $query->where('activo', (bool) $estado);
            }
            
            // Aplicar ordenamiento
            $ordenamientosValidos = ['nombre', 'created_at', 'updated_at', 'categorias_count', 'equipos_count'];
            if (in_array($ordenar, $ordenamientosValidos)) {
                $query->orderBy($ordenar, $direccion === 'asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            // Ejecutar query con paginación (valor directo 12)
            $ligas = $query->paginate(12);
            
            // Calcular conteo de jugadores por liga (de forma eficiente)
            $ligas->each(function ($liga) {
                $liga->jugadores_count = $liga->equipos()->withCount(['jugadores' => function($query) {
                    $query->where('equipo_jugadores.activo', true);
                }])->get()->sum('jugadores_count');
            });
            
            // Mantener parámetros de filtro en la paginación
            $ligas->appends($request->query());
            
            // Total de ligas (sin filtros)
            $totalLigas = Liga::count();
            
            \Log::debug('Listado de ligas cargado correctamente', [
                'total_ligas' => $ligas->total(),
                'current_page' => $ligas->currentPage(),
                'per_page' => $ligas->perPage(),
                'filtros_aplicados' => [
                    'search' => $search,
                    'estado' => $estado,
                    'ordenar' => $ordenar,
                    'direccion' => $direccion
                ]
            ]);
            
            return view('admin.ligas.index', compact('ligas', 'totalLigas'));
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar listado de ligas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Ocurrió un error al cargar el listado de ligas. Por favor intente nuevamente.');
        }
    }


    public function store(Request $request)
    {
        \Log::info('Iniciando creación de nueva liga', ['user_id' => Auth::id(), 'data' => $request->all()]);

        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                'min:3',
                'regex:/^[\pL\s\-]+$/u',
                'unique:ligas,nombre'
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:500'
            ],
            'activo' => [
                'required',
                'boolean',
                Rule::in([true, false])
            ],
            // Agregar aquí temporada_id o fechas si aplican
        ], [
            'nombre.required' => 'El nombre de la liga es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 100 caracteres.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras, espacios y guiones.',
            'nombre.unique' => 'Ya existe una liga con este nombre.',
            'descripcion.max' => 'La descripción no puede exceder los 500 caracteres.',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres si se proporciona.',
            'activo.required' => 'Debe seleccionar el estado de la liga.',
            'activo.boolean' => 'El estado debe ser válido.',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        try {
            $liga = Liga::create($validated);
            \Log::info('Liga creada exitosamente', ['liga_id' => $liga->id, 'user_id' => Auth::id()]);
            
            return redirect()->route('ligas.index')
                ->with('success', 'Liga creada exitosamente.');
        } catch (\Exception $e) {
            \Log::error('Error al crear liga', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Ocurrió un error al crear la liga. Por favor intente nuevamente.')
                ->withInput();
        }
    }

    public function update(Request $request, Liga $liga)
    {
        \Log::info('Iniciando actualización de liga', [
            'liga_id' => $liga->id,
            'user_id' => Auth::id(),
            'data' => $request->all()
        ]);

        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('ligas', 'nombre')->ignore($liga->id)
            ],
            'descripcion' => 'nullable|string',
            'activo' => 'required|boolean',
        ], [
            'nombre.required' => 'El nombre de la liga es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder los 100 caracteres.',
            'nombre.unique' => 'Ya existe una liga con este nombre.',
            'activo.required' => 'Debe seleccionar el estado de la liga.',
            'activo.boolean' => 'El estado debe ser válido.',
        ]);

        $validated['updated_by'] = Auth::id();

        try {
            $liga->update($validated);
            \Log::info('Liga actualizada exitosamente', [
                'liga_id' => $liga->id,
                'user_id' => Auth::id(),
                'changes' => $liga->getChanges()
            ]);
            
            return redirect()->route('ligas.index')
                ->with('success', 'Liga actualizada exitosamente.');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar liga', [
                'liga_id' => $liga->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Ocurrió un error al actualizar la liga. Por favor intente nuevamente.')
                ->withInput();
        }
    }


    public function destroy(Liga $liga)
    {
        \Log::info('Iniciando eliminación de liga', [
            'liga_id' => $liga->id,
            'user_id' => Auth::id()
        ]);

        if ($liga->categorias()->exists() || $liga->equipos()->exists() || $liga->juegos()->exists()) {
            return redirect()->route('ligas.index')
                ->with('error', 'No se puede eliminar la liga porque tiene categorías, equipos o juegos asociados.');
        }

        try {
            $liga->delete();
            \Log::info('Liga eliminada exitosamente', [
                'liga_id' => $liga->id,
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('ligas.index')
                ->with('success', 'Liga eliminada exitosamente.');
        } catch (\Exception $e) {
            \Log::error('Error al eliminar liga', [
                'liga_id' => $liga->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return redirect()->route('ligas.index')
                ->with('error', 'No se pudo eliminar la liga. Puede que tenga equipos asociados.');
        }
    }


    public function toggleStatus(Liga $liga)
    {
        $liga->update([
            'activo' => !$liga->activo,
            'updated_by' => Auth::id(),
        ]);

        $status = $liga->activo ? 'activada' : 'desactivada';
        
        return redirect()->route('ligas.index')
            ->with('success', "Liga {$status} exitosamente.");
    }

    function getActiveLigas()
    {
        $ligas = Liga::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($ligas);
    }


    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $ligas = Liga::where('nombre', 'like', "%{$query}%")
            ->orWhere('descripcion', 'like', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('admin.ligas.index', compact('ligas'));
    }


    public function getLiga(Liga $liga)
    {
        return response()->json([
            'id' => $liga->id,
            'nombre' => $liga->nombre,
            'descripcion' => $liga->descripcion,
            'activo' => $liga->activo
        ]);
    }
}