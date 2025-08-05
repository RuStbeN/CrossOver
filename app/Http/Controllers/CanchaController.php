<?php

namespace App\Http\Controllers;

use App\Models\Cancha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class CanchaController extends Controller
{

    public function index(Request $request)
    {
        Log::info('Accediendo a la lista de canchas', [
            'user_id' => Auth::id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        try {
            // Parámetros de filtrado
            $search = $request->get('search');
            $estado = $request->get('estado');
            $tipoSuperficie = $request->get('tipo_superficie');
            $techada = $request->get('techada');
            $iluminacion = $request->get('iluminacion');
            $ordenar = $request->get('ordenar', 'created_at');
            $direccion = $request->get('direccion', 'desc');
            
            // Construir query
            $query = Cancha::query();
            
            // Aplicar filtros
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'like', '%'.$search.'%')
                    ->orWhere('direccion', 'like', '%'.$search.'%');
                });
            }
            
            if ($estado !== null && $estado !== '') {
                $query->where('activo', (bool)$estado);
            }
            
            if ($tipoSuperficie) {
                $query->where('tipo_superficie', $tipoSuperficie);
            }
            
            if ($techada !== null && $techada !== '') {
                $query->where('techada', (bool)$techada);
            }
            
            if ($iluminacion !== null && $iluminacion !== '') {
                $query->where('iluminacion', (bool)$iluminacion);
            }
            
            // Ordenamiento
            $ordenamientosValidos = ['nombre', 'capacidad', 'tarifa_por_hora', 'created_at'];
            if (in_array($ordenar, $ordenamientosValidos)) {
                $query->orderBy($ordenar, $direccion === 'asc' ? 'asc' : 'desc');
            } else {
                $query->latest();
            }
            
            // Paginación
            $canchas = $query->paginate(10);
            $canchas->appends($request->query());
            
            // Total de canchas (sin filtros)
            $totalCanchas = Cancha::count();
            
            // Tipos de superficie disponibles
            $tiposSuperficie = Cancha::select('tipo_superficie')
                ->distinct()
                ->pluck('tipo_superficie')
                ->filter()
                ->toArray();
            
            Log::debug('Listado de canchas cargado correctamente', [
                'total_canchas' => $canchas->total(),
                'current_page' => $canchas->currentPage(),
                'per_page' => $canchas->perPage()
            ]);
            
            return view('admin.canchas.index', [
                'canchas' => $canchas,
                'totalCanchas' => $totalCanchas,
                'tiposSuperficie' => $tiposSuperficie
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al cargar listado de canchas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Ocurrió un error al cargar el listado de canchas. Por favor intente nuevamente.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:150',
                'direccion' => 'nullable|string',
                'capacidad' => 'nullable|integer|min:0',
                'tipo_superficie' => 'required|in:Sintética,Natural,Cemento,Parquet,Otros',
                'techada' => 'required|boolean',
                'iluminacion' => 'required|boolean',
                'equipamiento' => 'nullable|string',
                'tarifa_por_hora' => 'nullable|numeric|min:0',
                'activo' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return redirect()->back()
                    ->withInput()
                    ->with('error', $firstError);
            }

            $validated = $validator->validated();
            $validated['created_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();

            $cancha = Cancha::create($validated);
            Log::info('Cancha creada exitosamente', ['id' => $cancha->id]);

            return redirect()->route('canchas.index')
                ->with('success', 'Cancha creada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al crear cancha: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la cancha. Intenta nuevamente.');
        }
    }

    public function update(Request $request, Cancha $cancha)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:150',
                'direccion' => 'nullable|string',
                'capacidad' => 'nullable|integer|min:0',
                'tipo_superficie' => 'required|in:Sintética,Natural,Cemento,Parquet,Otros',
                'techada' => 'required|boolean',
                'iluminacion' => 'required|boolean',
                'equipamiento' => 'nullable|string',
                'tarifa_por_hora' => 'nullable|numeric|min:0',
                'activo' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return redirect()->back()
                    ->withInput()
                    ->with('error', $firstError);
            }

            $validated = $validator->validated();
            $validated['updated_by'] = auth()->id();

            $cancha->update($validated);
            Log::info('Cancha actualizada exitosamente', ['id' => $cancha->id]);

            return redirect()->route('canchas.index')
                ->with('success', 'Cancha actualizada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar cancha: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la cancha. Intenta nuevamente.');
        }
    }

    public function destroy(Cancha $cancha)
    {
        try {
            if ($cancha->juegos()->count() > 0) {
                Log::warning('Intento de eliminar cancha con juegos asociados', ['id' => $cancha->id]);
                return redirect()->route('canchas.index')
                    ->with('error', 'No se puede eliminar la cancha porque tiene juegos asociados');
            }

            $cancha->delete();
            Log::info('Cancha eliminada exitosamente', ['id' => $cancha->id]);

            return redirect()->route('canchas.index')
                ->with('success', 'Cancha eliminada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar cancha: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al eliminar la cancha. Intenta nuevamente.');
        }
    }
}