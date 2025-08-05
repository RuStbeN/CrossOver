<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Liga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Capturar parámetros de filtro
            $search = $request->get('search');
            $ligaId = $request->get('liga');
            $estado = $request->get('estado');
            $edadMin = $request->get('edad_min');
            $edadMax = $request->get('edad_max');
            
            // Construir query base
            $query = Categoria::with('liga');
            
            // Aplicar filtros
            if ($search) {
                $query->where('nombre', 'like', '%' . $search . '%');
            }
            
            if ($ligaId) {
                $query->where('liga_id', $ligaId);
            }
            
            if ($estado !== null && $estado !== '') {
                $query->where('activo', (bool) $estado);
            }
            
            if ($edadMin) {
                $query->where('edad_minima', '>=', $edadMin);
            }
            
            if ($edadMax) {
                $query->where('edad_maxima', '<=', $edadMax);
            }
            
            // Ejecutar query con paginación (valor directo 12)
            $categorias = $query->orderBy('nombre')->paginate(12);
            
            // Mantener parámetros de filtro en la paginación
            $categorias->appends($request->query());
            
            // Obtener ligas para el select
            $ligas = Liga::where('activo', true)
                ->orderBy('nombre')
                ->get();
                
            // Total de categorías (sin filtros)
            $totalCategorias = Categoria::count();
            
            Log::info('Listado de categorías cargado correctamente', [
                'total_categorias' => $categorias->total(),
                'current_page' => $categorias->currentPage(),
                'filtros_aplicados' => [
                    'search' => $search,
                    'liga' => $ligaId,
                    'estado' => $estado,
                    'edad_min' => $edadMin,
                    'edad_max' => $edadMax
                ],
                'user_id' => auth()->id()
            ]);
            
            return view('admin.categorias.index', compact('categorias', 'ligas', 'totalCategorias'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar listado de categorías', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Ocurrió un error al cargar el listado de categorías. Por favor intente nuevamente.');
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'liga_id' => 'required|exists:ligas,id',
            'edad_minima' => 'required|integer|min:0',
            'edad_maxima' => 'required|integer|gt:edad_minima',
            'activo' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()
                ->withInput()
                ->with('error', $firstError);
        }

        try {
            $validatedData = $validator->validated();
            
            $categoria = Categoria::create([
                'nombre' => $validatedData['nombre'],
                'liga_id' => $validatedData['liga_id'],
                'edad_minima' => $validatedData['edad_minima'],
                'edad_maxima' => $validatedData['edad_maxima'],
                'activo' => $validatedData['activo'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            Log::info('Categoría creada exitosamente', [
                'categoria_id' => $categoria->id,
                'nombre' => $categoria->nombre,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('categorias.index')
                ->with('success', 'Categoría creada exitosamente.');
                
        } catch (\Exception $e) {
            Log::error('Error al crear categoría', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'input' => $request->except('_token')
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la categoría. Intenta nuevamente.');
        }
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'liga_id' => 'required|exists:ligas,id',
            'edad_minima' => 'required|integer|min:0',
            'edad_maxima' => 'required|integer|gt:edad_minima',
            'activo' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()
                ->withInput()
                ->with('error', $firstError);
        }

        try {
            $validatedData = $validator->validated();
            
            $categoria->update([
                'nombre' => $validatedData['nombre'],
                'liga_id' => $validatedData['liga_id'],
                'edad_minima' => $validatedData['edad_minima'],
                'edad_maxima' => $validatedData['edad_maxima'],
                'activo' => $request->boolean('activo'),
                'updated_by' => auth()->id()
            ]);

            Log::info('Categoría actualizada exitosamente', [
                'categoria_id' => $categoria->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('categorias.index')
                ->with('success', 'Categoría actualizada exitosamente.');
                
        } catch (\Exception $e) {
            Log::error('Error al actualizar categoría', [
                'categoria_id' => $categoria->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la categoría. Intenta nuevamente.');
        }
    }

    public function destroy(Categoria $categoria)
    {
        try {
            // Verificar si hay equipos asociados
            $equiposCount = $categoria->equipos()->count();
            
            if ($equiposCount > 0) {
                Log::warning('Intento de eliminar categoría con equipos asociados', [
                    'categoria_id' => $categoria->id,
                    'equipos_count' => $equiposCount,
                    'user_id' => auth()->id()
                ]);
                
                return redirect()->route('categorias.index')
                    ->with('error', 'No se puede eliminar la categoría porque tiene equipos asociados.');
            }

            // Eliminar la categoría
            $deleted = $categoria->delete();
            
            if ($deleted) {
                Log::info('Categoría eliminada exitosamente', [
                    'categoria_id' => $categoria->id,
                    'user_id' => auth()->id()
                ]);
                
                return redirect()->route('categorias.index')
                    ->with('success', 'Categoría eliminada exitosamente.');
            } else {
                Log::error('Fallo al eliminar categoría', [
                    'categoria_id' => $categoria->id,
                    'user_id' => auth()->id()
                ]);
                
                return redirect()->route('categorias.index')
                    ->with('error', 'No se pudo eliminar la categoría.');
            }
                    
        } catch (\Exception $e) {
            Log::error('Error al eliminar categoría', [
                'categoria_id' => $categoria->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->route('categorias.index')
                ->with('error', 'Error al eliminar la categoría. Intenta nuevamente.');
        }
    }
}