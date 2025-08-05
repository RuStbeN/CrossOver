<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Categoria;
use App\Models\Entrenador;
use App\Models\Liga;


class EquipoController extends Controller
{

    public function index(Request $request)
    {
        \Log::info('Accediendo a la lista de equipos', [
            'user_id' => Auth::id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        try {
            // Obtener parámetros de filtrado
            $search = $request->get('search');
            $estado = $request->get('estado');
            $categoria_id = $request->get('categoria_id');
            $liga_id = $request->get('liga_id');
            $entrenador_id = $request->get('entrenador_id');
            $ordenar = $request->get('ordenar', 'nombre');
            $direccion = $request->get('direccion', 'asc');

            // Construir query
            $query = Equipo::with([
                'categoria', 
                'liga', 
                'entrenador',
                'jugadores' => function($query) {
                    $query->withPivot([
                        'numero_camiseta',
                        'posicion_principal',
                        'posicion_secundaria',
                        'fecha_ingreso',
                        'fecha_salida',
                        'es_capitan',
                        'activo',
                        'temporada_id',
                        'torneo_id'
                    ]);
                }
            ]);

            // Aplicar filtros
            if ($search) {
                $query->where('nombre', 'like', '%' . $search . '%');
            }

            if ($estado !== null && $estado !== '') {
                $query->where('activo', (bool) $estado);
            }

            if ($categoria_id) {
                $query->where('categoria_id', $categoria_id);
            }

            if ($liga_id) {
                $query->where('liga_id', $liga_id);
            }

            if ($entrenador_id) {
                $query->where('entrenador_id', $entrenador_id);
            }

            // Ordenamiento
            $ordenamientosValidos = ['nombre', 'created_at', 'fecha_fundacion'];
            $direccionValida = in_array($direccion, ['asc', 'desc']) ? $direccion : 'asc';
            
            if (in_array($ordenar, $ordenamientosValidos)) {
                $query->orderBy($ordenar, $direccionValida);
            } else {
                $query->orderBy('nombre', 'asc');
            }

            // Paginación con valor directo 12
            $equipos = $query->paginate(12);
            $equipos->appends($request->query());

            // Datos para filtros
            $categorias = Categoria::orderBy('nombre')->get();
            $ligas = Liga::orderBy('nombre')->get();
            $entrenadores = Entrenador::orderBy('nombre')->get();
            $totalEquipos = Equipo::count();
            
            \Log::debug('Listado de equipos cargado correctamente', [
                'total_equipos' => $equipos->total(),
                'current_page' => $equipos->currentPage(),
                'per_page' => $equipos->perPage()
            ]);
            
            return view('admin.equipos.index', [
                'equipos' => $equipos,
                'categorias' => $categorias,
                'ligas' => $ligas,
                'entrenadores' => $entrenadores,
                'totalEquipos' => $totalEquipos
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar listado de equipos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Ocurrió un error al cargar el listado de equipos. Por favor intente nuevamente.');
        }
    }

    public function store(Request $request)
    {
        Log::info('Inicio de creación de equipo', [
            'user_id' => auth()->id(),
            'data' => $request->except(['logo_url'])
        ]);

        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'categoria_id' => 'required|exists:categorias,id',
                'liga_id' => 'required|exists:ligas,id',
                'entrenador_id' => 'required|exists:entrenadores,id',
                'logo_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'fecha_fundacion' => 'nullable|date',
                'color_primario' => 'required|string',
                'color_secundario' => 'required|string',
                'activo' => 'required|in:0,1', // Asegura que sea 0 o 1
            ]);

            // Procesar la imagen si se subió
            if ($request->hasFile('logo_url')) {
                $path = $request->file('logo_url')->store('equipos/logos', 'public');
                $validated['logo_url'] = $path;
                Log::info('Logo subido para nuevo equipo', [
                    'path' => $path,
                    'size' => $request->file('logo_url')->getSize()
                ]);
            }

            // No necesitas reasignar 'activo' porque ya está en $validated
            $validated['created_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();

            $equipo = Equipo::create($validated);
            
            Log::info('Equipo creado exitosamente', [
                'user_id' => auth()->id(),
                'equipo_id' => $equipo->id,
                'equipo_nombre' => $equipo->nombre,
                'estado' => $equipo->activo ? 'Activo' : 'Inactivo' // Para el log
            ]);

            return redirect()->route('equipos.index')
                ->with('success', 'Equipo creado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al crear equipo', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('equipos.index')
                ->with('error', 'Error al crear el equipo: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Equipo $equipo)
    {
        Log::info('Inicio de actualización de equipo', [
            'user_id' => auth()->id(),
            'equipo_id' => $equipo->id,
            'data' => $request->except(['logo_url']) // Excluimos el archivo del log
        ]);

        try {
            // Validación de los datos del formulario
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:100',
                'categoria_id' => 'required|exists:categorias,id',
                'liga_id' => 'required|exists:ligas,id',
                'entrenador_id' => 'nullable|exists:entrenadores,id',
                'logo_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'fecha_fundacion' => 'nullable|date',
                'color_primario' => 'required|string|max:7',
                'color_secundario' => 'required|string|max:7',
                'activo' => 'boolean',
            ]);

            // Manejo del logo (si se subió uno nuevo)
            if ($request->hasFile('logo_url')) {
                // Eliminar el logo anterior si existe
                if ($equipo->logo_url) {
                    Storage::disk('public')->delete($equipo->logo_url);
                }
                
                // Guardar el nuevo logo
                $path = $request->file('logo_url')->store('equipos/logos', 'public');
                $validatedData['logo_url'] = $path;
            } else {
                // Mantener el logo existente si no se subió uno nuevo
                unset($validatedData['logo_url']);
            }

            // Convertir el valor de activo a booleano
            $validatedData['activo'] = $request->has('activo');
            $validatedData['updated_by'] = auth()->id();

            // Guardar datos antes de actualizar para comparación
            $oldData = $equipo->toArray();
            
            // Actualizar el equipo
            $updated = $equipo->update($validatedData);
            
            if ($updated) {
                Log::info('Equipo actualizado exitosamente', [
                    'user_id' => auth()->id(),
                    'equipo_id' => $equipo->id,
                    'changes' => array_diff_assoc($equipo->fresh()->toArray(), $oldData)
                ]);
                
                return redirect()->route('equipos.index')
                    ->with('success', 'Equipo actualizado exitosamente');
            } else {
                Log::warning('Actualización de equipo retornó false', [
                    'equipo_id' => $equipo->id,
                    'data' => $validatedData
                ]);
                
                return redirect()->route('equipos.index')
                    ->with('error', 'No se pudo actualizar el equipo');
            }

        } catch (\Exception $e) {
            Log::error('Error al actualizar equipo', [
                'user_id' => auth()->id(),
                'equipo_id' => $equipo->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('equipos.index')
                ->with('error', 'Error al actualizar el equipo: ' . $e->getMessage());
        }
    }


    public function destroy(Equipo $equipo)
    {
        Log::info('Inicio de eliminación de equipo', [
            'user_id' => auth()->id(),
            'equipo_id' => $equipo->id,
            'equipo_nombre' => $equipo->nombre,
            'jugadores_asociados' => $equipo->jugadores()->count()
        ]);

        try {
            // Verificar si el equipo tiene jugadores asociados
            if ($equipo->jugadores()->count() > 0) {
                Log::warning('Intento de eliminar equipo con jugadores asociados', [
                    'equipo_id' => $equipo->id,
                    'jugadores_count' => $equipo->jugadores()->count()
                ]);
                
                return redirect()->route('equipos.index')
                    ->with('error', 'No se puede eliminar el equipo porque tiene jugadores asociados');
            }

            // Guardar datos antes de eliminar para registro
            $equipoData = $equipo->toArray();
            
            // Eliminar el logo si existe
            if ($equipo->logo_url && Storage::exists($equipo->logo_url)) {
                Storage::delete($equipo->logo_url);
                Log::info('Logo de equipo eliminado', [
                    'equipo_id' => $equipo->id,
                    'path' => $equipo->logo_url
                ]);
            }

            // Eliminar el equipo
            $deleted = $equipo->delete();
            
            if ($deleted) {
                Log::info('Equipo eliminado exitosamente', [
                    'equipo_id' => $equipo->id,
                    'user_id' => auth()->id(),
                    'data_eliminada' => $equipoData
                ]);
                
                // Verificación adicional en base de datos
                $stillExists = Equipo::withTrashed()->find($equipo->id);
                
                if ($stillExists) {
                    Log::warning('El equipo aparece como eliminado pero aún existe en BD (soft delete)', [
                        'equipo_id' => $equipo->id
                    ]);
                } else {
                    Log::info('Verificación BD: Equipo completamente eliminado', [
                        'equipo_id' => $equipo->id
                    ]);
                }
                
                return redirect()->route('equipos.index')
                    ->with('success', 'Equipo eliminado exitosamente');
            } else {
                Log::error('Fallo al eliminar equipo (delete() retornó false)', [
                    'equipo_id' => $equipo->id
                ]);
                
                return redirect()->route('equipos.index')
                    ->with('error', 'No se pudo eliminar el equipo');
            }
                    
        } catch (\Exception $e) {
            Log::error('Error al eliminar equipo', [
                'user_id' => auth()->id(),
                'equipo_id' => $equipo->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('equipos.index')
                ->with('error', 'Error al eliminar el equipo: ' . $e->getMessage());
        }
    }
}