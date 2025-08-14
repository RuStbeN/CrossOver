<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Liga;
use App\Models\Temporada;
use App\Models\Categoria;
use App\Models\Cancha;
use App\Models\Equipo;
use App\Models\Juego;
use App\Models\TorneoClasificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\TorneoPuntosService;

class TorneoController extends Controller
{

    public function index(Request $request)
    {
        Log::info('Accediendo al índice de torneos', [
            'user_id' => auth()->id(),
            'ip' => $request->ip()
        ]);

        try {
            $query = Torneo::with(['liga', 'temporada', 'categoria', 'cancha', 'equipos', 'juegos', 'clasificacion.equipo'])
                    ->orderBy('fecha_inicio', 'desc');

            // Aplicar filtros si es necesario
            if ($request->has('search')) {
                $query->where('nombre', 'like', '%' . $request->search . '%');
            }

            // Paginación con valor directo 12
            $torneos = $query->paginate(12);
            
            $ligas = Liga::orderBy('nombre')->get();
            $temporadas = Temporada::orderBy('nombre')->get();
            $categorias = Categoria::orderBy('nombre')->get();
            $canchas = Cancha::orderBy('nombre')->get();

            Log::info('Torneos cargados exitosamente', [
                'total' => $torneos->total(),
                'per_page' => $torneos->perPage()
            ]);

            return view('admin.torneos.index', compact('torneos', 'ligas', 'temporadas', 'categorias', 'canchas'));

        } catch (\Exception $e) {
            Log::error('Error al cargar torneos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Ocurrió un error al cargar los torneos. Por favor intente nuevamente.');
        }
    }


    public function store(Request $request)
    {
        Log::info('Iniciando creación de torneo', ['datos' => $request->all()]);
        
        // SOLUCIÓN: Convertir canchas_ids de string a array si es necesario
        if ($request->has('canchas_ids') && is_string($request->canchas_ids)) {
            $request->merge([
                'canchas_ids' => array_filter(explode(',', $request->canchas_ids))
            ]);
            Log::info('Canchas convertidas de string a array', ['canchas_ids' => $request->canchas_ids]);
        }
        
        $rules = [
            // Información general
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:eliminacion_directa,doble_eliminacion,round_robin,grupos_eliminacion,por_puntos',
            'liga_id' => 'required|exists:ligas,id',
            'temporada_id' => 'required|exists:temporadas,id',
            'categoria_id' => 'required|exists:categorias,id',
            'canchas_ids' => 'required|array|min:1',
            'canchas_ids.*' => 'exists:canchas,id',
            
            // Fechas y tiempos
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'duracion_cuarto_minutos' => 'required|integer|min:1|max:60',
            'tiempo_entre_partidos_minutos' => 'required|integer|min:1|max:60',
            
            // Configuración general
            'premio_total' => 'nullable|numeric|min:0',
            'estado' => 'required|in:Programado,En Curso,Finalizado,Cancelado,Suspendido',
            'activo' => 'required|in:true,false,1,0,"true","false","1","0"',
            
            // Configuración de puntos
            'puntos_por_victoria' => 'required|integer|min:0|max:10',
            'puntos_por_empate' => 'required|integer|min:0|max:10',
            'puntos_por_derrota' => 'required|integer|min:0|max:10'
        ];

        // Validación condicional para playoffs
        if ($request->tipo === 'por_puntos') {
            $rules['usa_playoffs'] = 'nullable|boolean';
            $rules['equipos_playoffs'] = 'nullable|integer|in:2,4,6,8';
            
            if ($request->has('usa_playoffs') && filter_var($request->usa_playoffs, FILTER_VALIDATE_BOOLEAN)) {
                $rules['equipos_playoffs'] = 'required|integer|in:2,4,6,8';
            }
        }

        try {
            $validated = $request->validate($rules, [
                'equipos_playoffs.in' => 'El número de equipos para playoffs debe ser 2, 4, 6 u 8',
                'fecha_fin.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha de inicio',
                'canchas_ids.required' => 'Debe seleccionar al menos una cancha',
                'canchas_ids.*.exists' => 'Una o más canchas seleccionadas no existen'
            ]);
            
            Log::info('Validación exitosa', ['validated' => $validated]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error en validación', ['errors' => $e->errors()]);
            throw $e;
        }

        // Procesamiento de datos
        $data = $this->procesarDatosTorneo($request, $validated);
        
        Log::info('Datos procesados para crear torneo', ['data' => $data]);

        DB::beginTransaction();
        
        try {
            // 1. Crear el torneo (excluir canchas_ids que no existe en la tabla)
            $torneoData = collect($data)->except(['canchas_ids'])->toArray();
            $torneo = Torneo::create($torneoData);
            
            Log::info('Torneo creado en base de datos', ['torneo_id' => $torneo->id]);
            
            // 2. Asociar las canchas seleccionadas
            $canchasConPrioridad = [];
            foreach ($request->canchas_ids as $index => $canchaId) {
                $canchasConPrioridad[$canchaId] = [
                    'es_principal' => $index === 0,
                    'orden_prioridad' => $index + 1
                ];
            }
            
            $torneo->canchas()->sync($canchasConPrioridad);
            
            Log::info('Canchas asociadas al torneo', [
                'torneo_id' => $torneo->id,
                'canchas' => $canchasConPrioridad
            ]);
            
            // 3. Configurar según el tipo de torneo
            switch ($torneo->tipo) {
                case 'por_puntos':
                    $service = app(TorneoPuntosService::class);
                    Log::info('Configurando torneo por puntos');
                    $service->configurarTorneo($torneo);
                    break;
                    
                case 'eliminacion_directa':
                    throw new \Exception("Tipo de torneo eliminación directa no implementado aún");
                    
                case 'doble_eliminacion':
                    throw new \Exception("Tipo de torneo doble eliminación no implementado aún");
                    
                default:
                    throw new \Exception("Tipo de torneo no soportado: " . $torneo->tipo);
            }
            
            DB::commit();
            
            Log::info('Torneo creado exitosamente', [
                'torneo_id' => $torneo->id, 
                'nombre' => $torneo->nombre,
                'tipo' => $torneo->tipo
            ]);
            
            return redirect()->route('torneos.index')
                        ->with('success', 'Torneo creado exitosamente con juegos generados');
                        
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al crear torneo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data ?? 'No procesada'
            ]);
            
            return redirect()->back()
                        ->withInput()
                        ->with('error', 'Error al crear el torneo: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Torneo $torneo)
    {
        Log::info('Iniciando actualización de torneo', [
            'torneo_id' => $torneo->id,
            'datos' => $request->all()
        ]);
        
        // SOLUCIÓN: Convertir canchas_ids de string a array si es necesario
        if ($request->has('canchas_ids') && is_string($request->canchas_ids)) {
            $request->merge([
                'canchas_ids' => array_filter(explode(',', $request->canchas_ids))
            ]);
            Log::info('Canchas convertidas de string a array en update', ['canchas_ids' => $request->canchas_ids]);
        }
        
        $rules = [
            // Información general
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:eliminacion_directa,doble_eliminacion,round_robin,grupos_eliminacion,por_puntos',
            'liga_id' => 'required|exists:ligas,id',
            'temporada_id' => 'required|exists:temporadas,id',
            'categoria_id' => 'required|exists:categorias,id',
            'canchas_ids' => 'required|array|min:1',
            'canchas_ids.*' => 'exists:canchas,id',
            
            // Fechas y tiempos
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'duracion_cuarto_minutos' => 'required|integer|min:1|max:60',
            'tiempo_entre_partidos_minutos' => 'required|integer|min:1|max:60',
            
            // Configuración general
            'premio_total' => 'nullable|numeric|min:0',
            'estado' => 'required|in:Programado,En Curso,Finalizado,Cancelado,Suspendido',
            'activo' => 'required|in:1,0,"1","0",true,false,"true","false"',
            
            // Configuración de puntos
            'puntos_por_victoria' => 'required|integer|min:0|max:10',
            'puntos_por_empate' => 'required|integer|min:0|max:10',
            'puntos_por_derrota' => 'required|integer|min:0|max:10'
        ];

        // Validación condicional para playoffs
        if ($request->tipo === 'por_puntos') {
            $rules['usa_playoffs'] = 'required|in:1,0,"1","0",true,false,"true","false"';
            $rules['equipos_playoffs'] = 'required_if:usa_playoffs,1,true,"1","true"|nullable|integer|in:2,4,6,8';
        }

        try {
            $validated = $request->validate($rules, [
                'equipos_playoffs.in' => 'El número de equipos para playoffs debe ser 2, 4, 6 u 8',
                'fecha_fin.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha de inicio',
                'canchas_ids.required' => 'Debe seleccionar al menos una cancha',
                'canchas_ids.*.exists' => 'Una o más canchas seleccionadas no existen',
                'canchas_ids.array' => 'Las canchas deben ser un array válido'
            ]);
            
            Log::info('Validación exitosa en update', ['validated' => $validated]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error en validación de update', ['errors' => $e->errors()]);
            throw $e;
        }

        // Procesamiento de datos
        $data = $this->procesarDatosTorneo($request, $validated);
        
        Log::info('Datos procesados para actualizar torneo', ['data' => $data]);

        DB::beginTransaction();
        
        try {
            // 1. Actualizar datos básicos del torneo (sin canchas_ids que no existe en la tabla)
            $torneoData = collect($data)->except(['canchas_ids'])->toArray();
            $torneo->update($torneoData);
            
            Log::info('Datos básicos del torneo actualizados', ['torneo_id' => $torneo->id]);
            
            // 2. Actualizar canchas asociadas
            $canchasConPrioridad = [];
            foreach ($request->canchas_ids as $index => $canchaId) {
                $canchasConPrioridad[$canchaId] = [
                    'es_principal' => $index === 0, // La primera es principal
                    'orden_prioridad' => $index + 1
                ];
            }
            
            $torneo->canchas()->sync($canchasConPrioridad);
            
            Log::info('Canchas actualizadas para torneo', [
                'torneo_id' => $torneo->id,
                'canchas' => $canchasConPrioridad
            ]);
            
            DB::commit();
            
            Log::info('Torneo actualizado exitosamente', [
                'torneo_id' => $torneo->id,
                'nombre' => $torneo->nombre,
                'tipo' => $torneo->tipo
            ]);
            
            return redirect()->route('torneos.index')
                        ->with('success', 'Torneo actualizado exitosamente');
                        
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al actualizar torneo', [
                'torneo_id' => $torneo->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data ?? 'No procesada'
            ]);
            
            return redirect()->back()
                        ->withInput()
                        ->with('error', 'Error al actualizar el torneo: ' . $e->getMessage());
        }
    }

    /**
     * Procesa los datos del torneo antes de guardarlo
     */
    private function procesarDatosTorneo(Request $request, array $validated)
    {
        // Convertir valores checkbox a booleanos
        $validated['activo'] = filter_var($validated['activo'], FILTER_VALIDATE_BOOLEAN);
        
        if ($request->tipo === 'por_puntos') {
            $validated['usa_playoffs'] = isset($validated['usa_playoffs']) ? 
                                        filter_var($validated['usa_playoffs'], FILTER_VALIDATE_BOOLEAN) : false;
            
            if (!$validated['usa_playoffs']) {
                $validated['equipos_playoffs'] = null;
            }
        } else {
            $validated['usa_playoffs'] = false;
            $validated['equipos_playoffs'] = null;
        }

        // Agregar campos de auditoría si están disponibles
        if (auth()->check()) {
            $validated['created_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();
        }
        
        // Asegurar que premio_total sea decimal
        $validated['premio_total'] = $validated['premio_total'] ?? 0.00;
        
        Log::info('Datos después de procesar', ['processed_data' => $validated]);
        
        return $validated;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Torneo $torneo)
    {
        Log::info('Iniciando eliminación de torneo', [
            'torneo_id' => $torneo->id,
            'nombre' => $torneo->nombre
        ]);

        try {
            $torneoId = $torneo->id;
            $torneoNombre = $torneo->nombre;
            
            $torneo->delete();

            Log::info('Torneo eliminado exitosamente', [
                'torneo_id' => $torneoId,
                'nombre' => $torneoNombre
            ]);

            return redirect()->route('torneos.index')
                            ->with('success', 'Torneo eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar torneo', [
                'torneo_id' => $torneo->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('torneos.index')
                            ->with('error', 'Error al eliminar el torneo: ' . $e->getMessage());
        }
    }
}