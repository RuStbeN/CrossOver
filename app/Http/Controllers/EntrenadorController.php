<?php

namespace App\Http\Controllers;

use App\Models\Entrenador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EntrenadorController extends Controller
{

    public function index(Request $request)
    {
        try {
            Log::info('Consultando listado de entrenadores', [
                'usuario' => auth()->id(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()
            ]);

            // Capturar parámetros de filtro
            $search = $request->get('search');
            $estado = $request->get('estado');
            $ordenar = $request->get('ordenar', 'created_at');
            $direccion = $request->get('direccion', 'desc');
            
            // Construir query base
            $query = Entrenador::withCount('equipos');
            
            // Aplicar filtros
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('cedula_profesional', 'like', '%' . $search . '%');
                });
            }
            
            if ($estado !== null && $estado !== '') {
                $query->where('activo', (bool) $estado);
            }
            
            // Aplicar ordenamiento
            $ordenamientosValidos = ['nombre', 'created_at', 'updated_at', 'equipos_count'];
            if (in_array($ordenar, $ordenamientosValidos)) {
                $query->orderBy($ordenar, $direccion === 'asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            // Paginación con valor directo 12
            $entrenadores = $query->paginate(12);
            $entrenadores->appends($request->query());
            
            // Total de entrenadores (sin filtros)
            $totalEntrenadores = Entrenador::count();
            
            Log::info('Listado de entrenadores obtenido exitosamente', [
                'total_entrenadores' => $entrenadores->total(),
                'current_page' => $entrenadores->currentPage(),
                'per_page' => $entrenadores->perPage(),
                'usuario' => auth()->id()
            ]);

            return view('admin.entrenadores.index', compact('entrenadores', 'totalEntrenadores'));
            
        } catch (\Exception $e) {
            Log::error('Error al obtener listado de entrenadores', [
                'error' => $e->getMessage(),
                'usuario' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Error al cargar los entrenadores');
        }
    }


    public function store(Request $request)
    {
        Log::info('Iniciando creación de entrenador', [
            'datos' => $request->except(['_token']),
            'usuario' => auth()->id(),
            'ip' => $request->ip()
        ]);

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'cedula_profesional' => 'nullable|string|max:50',
            'fecha_nacimiento' => 'nullable|date',
            'experiencia' => 'nullable|string',
            'activo' => 'required|in:true,false,1,0',
        ]);

        if ($validator->fails()) {
            Log::warning('Validación fallida al crear entrenador', [
                'errores' => $validator->errors()->toArray(),
                'datos' => $request->except(['_token']),
                'usuario' => auth()->id()
            ]);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $validator->validated();
            $data['created_by'] = auth()->id();
            
            // Convertir activo a boolean
            $data['activo'] = filter_var($data['activo'], FILTER_VALIDATE_BOOLEAN);

            $entrenador = Entrenador::create($data);

            Log::info('Entrenador creado exitosamente', [
                'entrenador_id' => $entrenador->id,
                'nombre' => $entrenador->nombre,
                'usuario' => auth()->id()
            ]);

            return redirect()->route('entrenadores.index')
                ->with('success', 'Entrenador creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al crear entrenador', [
                'error' => $e->getMessage(),
                'datos' => $data ?? $request->except(['_token']),
                'usuario' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Error al crear el entrenador')
                ->withInput();
        }
    }


    public function update(Request $request, Entrenador $entrenadore)
    {
        Log::info('Iniciando actualización de entrenador', [
            'entrenador_id' => $entrenadore->id,
            'entrenador_exists' => $entrenadore->exists,
            'request_url' => $request->url(),
            'request_method' => $request->method(),
            'route_parameters' => $request->route()->parameters(),
            'datos_anteriores' => $entrenadore->toArray(),
            'datos_nuevos' => $request->except(['_token', '_method']),
            'usuario' => auth()->id(),
            'ip' => $request->ip()
        ]);

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'cedula_profesional' => 'nullable|string|max:50',
            'fecha_nacimiento' => 'nullable|date',
            'experiencia' => 'nullable|string',
            'activo' => 'required|in:true,false,1,0',
        ]);

        if ($validator->fails()) {
            Log::warning('Validación fallida al actualizar entrenador', [
                'entrenador_id' => $entrenadore->id,
                'errores' => $validator->errors()->toArray(),
                'datos' => $request->except(['_token', '_method']),
                'usuario' => auth()->id()
            ]);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $validator->validated();
            $data['updated_by'] = auth()->id();
            
            // Convertir activo a boolean
            $data['activo'] = filter_var($data['activo'], FILTER_VALIDATE_BOOLEAN);

            $entrenadore->update($data);

            Log::info('Entrenador actualizado exitosamente', [
                'entrenador_id' => $entrenadore->id,
                'nombre' => $entrenadore->nombre,
                'cambios' => $entrenadore->getChanges(),
                'usuario' => auth()->id()
            ]);

            return redirect()->route('entrenadores.index')
                ->with('success', 'Entrenador actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar entrenador', [
                'entrenador_id' => $entrenadore->id,
                'error' => $e->getMessage(),
                'datos' => $data ?? $request->except(['_token', '_method']),
                'usuario' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Error al actualizar el entrenador')
                ->withInput();
        }
    }


    public function destroy(Entrenador $entrenadore)
    {
        Log::info('Iniciando eliminación de entrenador', [
            'entrenador_id' => $entrenadore->id,
            'nombre' => $entrenadore->nombre,
            'usuario' => auth()->id(),
            'ip' => request()->ip()
        ]);

        try {
            if ($entrenadore->equipos()->exists()) {
                Log::warning('Intento de eliminar entrenador con equipos asociados', [
                    'entrenador_id' => $entrenadore->id,
                    'nombre' => $entrenadore->nombre,
                    'cantidad_equipos' => $entrenadore->equipos()->count(),
                    'usuario' => auth()->id()
                ]);

                return redirect()->back()
                    ->with('error', 'No se puede eliminar el entrenador porque tiene equipos asociados');
            }

            $entrenadore->delete();

            Log::info('Entrenador eliminado exitosamente', [
                'entrenador_id' => $entrenadore->id,
                'nombre' => $entrenadore->nombre,
                'usuario' => auth()->id(),
                'deleted_at' => now()
            ]);

            return redirect()->route('entrenadores.index')
                ->with('success', 'Entrenador eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar entrenador', [
                'entrenador_id' => $entrenadore->id,
                'error' => $e->getMessage(),
                'usuario' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Error al eliminar el entrenador');
        }
    }
}