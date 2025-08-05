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
        
        $rules = [
            // Información general
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:eliminacion_directa,doble_eliminacion,round_robin,grupos_eliminacion,por_puntos',
            'liga_id' => 'required|exists:ligas,id',
            'temporada_id' => 'required|exists:temporadas,id',
            'categoria_id' => 'required|exists:categorias,id',
            'cancha_id' => 'required|exists:canchas,id',
            
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

        Log::info('Datos recibidos para playoffs', [
            'usa_playoffs' => $request->usa_playoffs,
            'equipos_playoffs' => $request->equipos_playoffs,
            'tipo' => $request->tipo
        ]);

        // Validación condicional para playoffs
        if ($request->tipo === 'por_puntos') {
            $rules['usa_playoffs'] = 'nullable|boolean'; // Cambia a validación booleana
            $rules['equipos_playoffs'] = 'nullable|integer|in:2,4,6,8';
            
            // Si usa_playoffs es true, entonces equipos_playoffs es requerido
            if ($request->has('usa_playoffs') && filter_var($request->usa_playoffs, FILTER_VALIDATE_BOOLEAN)) {
                $rules['equipos_playoffs'] = 'required|integer|in:2,4,6,8';
            }
        }

        try {
            $validated = $request->validate($rules, [
                'equipos_playoffs.in' => 'El número de equipos para playoffs debe ser 2, 4, 6 u 8',
                'fecha_fin.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha de inicio'
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
            // 1. Crear el torneo
            $torneo = Torneo::create($data);
            
            Log::info('Torneo creado en base de datos', ['torneo_id' => $torneo->id]);
            
            // 2. Determinar qué servicio usar según el tipo de torneo
            switch ($torneo->tipo) {
                case 'por_puntos':
                    $service = app(TorneoPuntosService::class);
                    Log::info('Configurando torneo por puntos');
                    $service->configurarTorneo($torneo);
                    break;
                    
                case 'eliminacion_directa':
                    // $service = app(TorneoEliminacionDirectaService::class);
                    // $service->configurarTorneo($torneo);
                    // break;
                    throw new \Exception("Tipo de torneo eliminación directa no implementado aún");
                    
                case 'doble_eliminacion':
                    // $service = app(TorneoDobleEliminacionService::class);
                    // $service->configurarTorneo($torneo);
                    // break;
                    throw new \Exception("Tipo de torneo doble eliminación no implementado aún");
                    
                // Agrega más casos según necesites
                    
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

    /**
     * Procesa los datos del torneo antes de guardarlo
     */
    private function procesarDatosTorneo(Request $request, array $validated)
    {
        // Función helper para convertir valores a booleano
        $toBool = function($value) {
            if (is_bool($value)) return $value;
            if (is_string($value)) {
                return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
            }
            return (bool) $value;
        };

        // Convertir valores checkbox a booleanos
        $validated['activo'] = $request->has('activo') && in_array($request->activo, ['1', 'true', 'on']);
        
        if ($request->tipo === 'por_puntos') {
            $validated['usa_playoffs'] = $request->has('usa_playoffs') && 
                                        in_array($request->usa_playoffs, ['on', '1', 'true']);
            
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
 
    
    public function update(Request $request, Torneo $torneo)
    {
        Log::info('Iniciando actualización de torneo', [
            'torneo_id' => $torneo->id,
            'datos' => $request->all()
        ]);

        $rules = [
            // Información general
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:eliminacion_directa,doble_eliminacion,round_robin,grupos_eliminacion,por_puntos',
            'liga_id' => 'required|exists:ligas,id',
            'temporada_id' => 'required|exists:temporadas,id',
            'categoria_id' => 'required|exists:categorias,id',
            'cancha_id' => 'required|exists:canchas,id',
            
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
                'fecha_fin.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha de inicio'
            ]);
            
            Log::info('Validación exitosa', ['validated' => $validated]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error en validación', ['errors' => $e->errors()]);
            throw $e;
        }

        // Procesamiento de datos
        $data = $validated;
        
        // Convertir valores a booleanos
        $data['activo'] = filter_var($data['activo'], FILTER_VALIDATE_BOOLEAN);
        
        if ($request->tipo === 'por_puntos') {
            $data['usa_playoffs'] = filter_var($data['usa_playoffs'], FILTER_VALIDATE_BOOLEAN);
            $data['equipos_playoffs'] = $data['usa_playoffs'] ? $data['equipos_playoffs'] : null;
        } else {
            $data['usa_playoffs'] = false;
            $data['equipos_playoffs'] = null;
        }

        // Actualización del torneo
        DB::beginTransaction();
        try {
            $torneo->update($data);
            
            // Aquí podrías agregar lógica adicional si necesitas actualizar
            // los partidos o configuración del torneo
            
            DB::commit();
            
            Log::info('Torneo actualizado exitosamente', [
                'torneo_id' => $torneo->id,
                'nombre' => $torneo->nombre
            ]);

            return redirect()->route('torneos.index')
                        ->with('success', 'Torneo actualizado exitosamente');
                        
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al actualizar torneo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                        ->withInput()
                        ->with('error', 'Error al actualizar el torneo: ' . $e->getMessage());
        }
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