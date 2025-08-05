<?php

namespace App\Http\Controllers;

use App\Models\Temporada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Validator;

class TemporadaController extends Controller
{
    public function index()
    {
        Log::info('Accediendo a la lista de temporadas');
        
        $temporadas = Temporada::with(['diasHabiles' => function($query) {
            $query->orderBy('dia_semana');
        }])
        ->orderBy('fecha_inicio', 'desc')
        ->paginate(12);
        
        return view('admin.temporadas.index', compact('temporadas'));
    }


    public function store(Request $request)
    {
        try {
            // DEBUG: Log inicial para ver todos los datos que llegan
            Log::info('=== INICIO STORE TEMPORADA ===');
            Log::info('Datos recibidos completos:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:150',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'horario_inicio' => 'required|date_format:H:i',
                'horario_fin' => 'required|date_format:H:i|after:horario_inicio',
                'dias_juego' => 'required|array|min:1',
                'dias_juego.*' => 'integer|between:1,7',
                'dias_horarios' => 'nullable|array',
                'dias_horarios.*.horario_inicio' => 'nullable|date_format:H:i',
                'dias_horarios.*.horario_fin' => 'nullable|date_format:H:i|after:dias_horarios.*.horario_inicio',
                'descripcion' => 'nullable|string',
                'activo' => 'required|boolean',
            ], [
                'dias_juego.required' => 'Debes seleccionar al menos un día de juego',
                'dias_juego.min' => 'Debes seleccionar al menos un día de juego',
                'dias_juego.*.between' => 'Los días seleccionados no son válidos',
                'dias_horarios.*.horario_fin.after' => 'El horario de fin debe ser posterior al de inicio',
            ]);

            if ($validator->fails()) {
                Log::warning('Validación fallida en store:', $validator->errors()->toArray());
                $firstError = $validator->errors()->first();
                return redirect()->back()
                    ->withInput()
                    ->with('error', $firstError);
            }

            $validated = $validator->validated();
            
            // DEBUG: Log datos validados
            Log::info('Datos validados:', [
                'dias_juego' => $validated['dias_juego'] ?? 'No definido',
                'dias_juego_count' => count($validated['dias_juego'] ?? []),
                'dias_horarios' => $validated['dias_horarios'] ?? 'No definido',
                'nombre' => $validated['nombre'] ?? 'No definido'
            ]);
            
            $diasJuego = $validated['dias_juego'];
            $diasHorarios = $validated['dias_horarios'] ?? [];
            
            Log::info('Arrays procesados:', [
                'diasJuego' => $diasJuego,
                'diasJuego_type' => gettype($diasJuego),
                'diasHorarios' => $diasHorarios,
                'diasHorarios_count' => count($diasHorarios)
            ]);
            
            // Remover datos que no van en la tabla principal
            unset($validated['dias_juego'], $validated['dias_horarios']);

            $validated['created_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();

            Log::info('Datos para crear temporada:', $validated);

            DB::beginTransaction();
            
            try {
                // Crear la temporada
                $temporada = Temporada::create($validated);
                Log::info('Temporada creada con ID:', ['id' => $temporada->id]);
                
                // Crear los días hábiles con sus horarios específicos
                foreach ($diasJuego as $dia) {
                    $diaData = [
                        'dia_semana' => $dia,
                        'activo' => true
                    ];
                    
                    Log::info('Procesando día:', ['dia' => $dia, 'tipo' => gettype($dia)]);
                    
                    // Verificar si este día tiene horarios específicos
                    if (isset($diasHorarios[$dia])) {
                        $diaData['horario_inicio'] = $diasHorarios[$dia]['horario_inicio'];
                        $diaData['horario_fin'] = $diasHorarios[$dia]['horario_fin'];
                        Log::info('Día con horario específico:', [
                            'dia' => $dia,
                            'horario_inicio' => $diasHorarios[$dia]['horario_inicio'],
                            'horario_fin' => $diasHorarios[$dia]['horario_fin']
                        ]);
                    }
                    
                    $diaHabil = $temporada->diasHabiles()->create($diaData);
                    Log::info('Día hábil creado:', [
                        'id' => $diaHabil->id,
                        'dia_semana' => $diaHabil->dia_semana,
                        'horario_inicio' => $diaHabil->horario_inicio,
                        'horario_fin' => $diaHabil->horario_fin
                    ]);
                }
                
                DB::commit();
                
                $horariosEspecificos = count(array_filter($diasHorarios));
                $mensaje = 'Temporada creada exitosamente con ' . count($diasJuego) . ' días de juego';
                if ($horariosEspecificos > 0) {
                    $mensaje .= ' (' . $horariosEspecificos . ' con horarios específicos)';
                }
                
                Log::info('=== TEMPORADA CREADA EXITOSAMENTE ===', [
                    'id' => $temporada->id,
                    'nombre' => $temporada->nombre,
                    'dias_habiles_count' => count($diasJuego),
                    'horarios_especificos_count' => $horariosEspecificos,
                    'mensaje' => $mensaje
                ]);

                return redirect()->route('temporadas.index')
                    ->with('success', $mensaje);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error en transacción de store:', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('=== ERROR GENERAL EN STORE ===', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la temporada. Intenta nuevamente.');
        }
    }

    public function update(Request $request, Temporada $temporada)
    {
        try {
            // DEBUG: Log inicial para el update
            Log::info('=== INICIO UPDATE TEMPORADA ===', [
                'temporada_id' => $temporada->id,
                'temporada_nombre' => $temporada->nombre
            ]);
            Log::info('Datos recibidos para update:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:150',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'horario_inicio' => 'required|date_format:H:i',
                'horario_fin' => 'required|date_format:H:i|after:horario_inicio',
                'dias_juego' => 'required|array|min:1',
                'dias_juego.*' => 'integer|between:1,7',
                'dias_horarios' => 'nullable|array',
                'dias_horarios.*.horario_inicio' => 'nullable|date_format:H:i',
                'dias_horarios.*.horario_fin' => 'nullable|date_format:H:i|after:dias_horarios.*.horario_inicio',
                'descripcion' => 'nullable|string',
                'activo' => 'required|boolean',
            ], [
                'dias_juego.required' => 'Debes seleccionar al menos un día de juego',
                'dias_juego.min' => 'Debes seleccionar al menos un día de juego',
                'dias_juego.*.between' => 'Los días seleccionados no son válidos',
                'dias_horarios.*.horario_fin.after' => 'El horario de fin debe ser posterior al de inicio',
            ]);

            if ($validator->fails()) {
                Log::warning('Validación fallida en update:', [
                    'temporada_id' => $temporada->id,
                    'errors' => $validator->errors()->toArray()
                ]);
                $firstError = $validator->errors()->first();
                return redirect()->back()
                    ->withInput()
                    ->with('error', $firstError);
            }

            $validated = $validator->validated();
            
            // DEBUG: Log datos validados para update
            Log::info('Datos validados para update:', [
                'dias_juego' => $validated['dias_juego'] ?? 'No definido',
                'dias_juego_count' => count($validated['dias_juego'] ?? []),
                'dias_horarios' => $validated['dias_horarios'] ?? 'No definido',
                'nombre' => $validated['nombre'] ?? 'No definido'
            ]);
            
            $diasJuego = $validated['dias_juego'];
            $diasHorarios = $validated['dias_horarios'] ?? [];
            
            // Remover datos que no van en la tabla principal
            unset($validated['dias_juego'], $validated['dias_horarios']);

            $validated['updated_by'] = auth()->id();

            Log::info('Datos para actualizar temporada:', $validated);

            DB::beginTransaction();
            
            try {
                // Actualizar la temporada
                $temporada->update($validated);
                Log::info('Temporada actualizada:', ['id' => $temporada->id]);
                
                // Obtener días hábiles anteriores para log
                $diasAnteriores = $temporada->diasHabiles()->pluck('dia_semana')->toArray();
                Log::info('Días anteriores:', $diasAnteriores);
                
                // Eliminar días hábiles existentes
                $eliminados = $temporada->diasHabiles()->delete();
                Log::info('Días hábiles eliminados:', ['count' => $eliminados]);
                
                // Crear los nuevos días hábiles con sus horarios específicos
                foreach ($diasJuego as $dia) {
                    $diaData = [
                        'dia_semana' => $dia,
                        'activo' => true
                    ];
                    
                    Log::info('Procesando día en update:', ['dia' => $dia, 'tipo' => gettype($dia)]);
                    
                    // Verificar si este día tiene horarios específicos
                    if (isset($diasHorarios[$dia])) {
                        $diaData['horario_inicio'] = $diasHorarios[$dia]['horario_inicio'];
                        $diaData['horario_fin'] = $diasHorarios[$dia]['horario_fin'];
                        Log::info('Día con horario específico en update:', [
                            'dia' => $dia,
                            'horario_inicio' => $diasHorarios[$dia]['horario_inicio'],
                            'horario_fin' => $diasHorarios[$dia]['horario_fin']
                        ]);
                    }
                    
                    $diaHabil = $temporada->diasHabiles()->create($diaData);
                    Log::info('Día hábil recreado:', [
                        'id' => $diaHabil->id,
                        'dia_semana' => $diaHabil->dia_semana,
                        'horario_inicio' => $diaHabil->horario_inicio,
                        'horario_fin' => $diaHabil->horario_fin
                    ]);
                }
                
                DB::commit();
                
                $horariosEspecificos = count(array_filter($diasHorarios));
                $mensaje = 'Temporada actualizada exitosamente con ' . count($diasJuego) . ' días de juego';
                if ($horariosEspecificos > 0) {
                    $mensaje .= ' (' . $horariosEspecificos . ' con horarios específicos)';
                }
                
                Log::info('=== TEMPORADA ACTUALIZADA EXITOSAMENTE ===', [
                    'id' => $temporada->id,
                    'nombre' => $temporada->nombre,
                    'dias_anteriores' => $diasAnteriores,
                    'dias_nuevos' => $diasJuego,
                    'horarios_especificos_count' => $horariosEspecificos,
                    'mensaje' => $mensaje
                ]);

                return redirect()->route('temporadas.index')
                    ->with('success', $mensaje);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error en transacción de update:', [
                    'temporada_id' => $temporada->id,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('=== ERROR GENERAL EN UPDATE ===', [
                'temporada_id' => $temporada->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la temporada. Intenta nuevamente.');
        }
    }

    public function destroy(Temporada $temporada)
    {
        try {
            Log::info('=== INICIO DESTROY TEMPORADA ===', [
                'temporada_id' => $temporada->id,
                'temporada_nombre' => $temporada->nombre,
                'usuario_id' => auth()->id()
            ]);
            
            // Verificar si hay relaciones antes de eliminar
            $juegosCount = $temporada->juegos()->count();
            $diasHabilesCount = $temporada->diasHabiles()->count();
            
            Log::info('Verificando relaciones:', [
                'juegos_count' => $juegosCount,
                'dias_habiles_count' => $diasHabilesCount
            ]);
            
            if ($juegosCount > 0) {
                Log::warning('Intento de eliminar temporada con juegos asociados', [
                    'temporada_id' => $temporada->id,
                    'juegos_count' => $juegosCount,
                    'usuario_id' => auth()->id()
                ]);
                return redirect()->route('temporadas.index')
                    ->with('error', 'No se puede eliminar la temporada porque tiene ' . $juegosCount . ' juego(s) asociado(s)');
            }
            
            DB::beginTransaction();
            
            try {
                // Obtener información antes de eliminar para el log
                $temporadaInfo = [
                    'id' => $temporada->id,
                    'nombre' => $temporada->nombre,
                    'fecha_inicio' => $temporada->fecha_inicio,
                    'fecha_fin' => $temporada->fecha_fin,
                    'dias_habiles' => $temporada->diasHabiles()->pluck('dia_semana')->toArray(),
                    'activo' => $temporada->activo
                ];
                
                // Primero eliminar días hábiles (aunque debería ser automático por cascade)
                $diasEliminados = $temporada->diasHabiles()->delete();
                Log::info('Días hábiles eliminados:', ['count' => $diasEliminados]);
                
                // Luego eliminar la temporada (soft delete)
                $temporada->delete();
                
                DB::commit();
                
                Log::info('=== TEMPORADA ELIMINADA EXITOSAMENTE ===', [
                    'temporada_info' => $temporadaInfo,
                    'dias_habiles_eliminados' => $diasEliminados,
                    'usuario_id' => auth()->id(),
                    'timestamp' => now()
                ]);
                
                return redirect()->route('temporadas.index')
                    ->with('success', 'Temporada "' . $temporadaInfo['nombre'] . '" eliminada exitosamente');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error en transacción de destroy:', [
                    'temporada_id' => $temporada->id,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('=== ERROR GENERAL EN DESTROY ===', [
                'temporada_id' => $temporada->id ?? 'No disponible',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'usuario_id' => auth()->id()
            ]);
            return redirect()->back()
                ->with('error', 'Error al eliminar la temporada. Intenta nuevamente.');
        }
    }
    
}