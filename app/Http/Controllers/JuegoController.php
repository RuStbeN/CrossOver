<?php
namespace App\Http\Controllers;

use App\Models\Juego;
use App\Models\Liga;
use App\Models\Equipo;
use App\Models\Cancha;
use App\Models\Temporada;
use App\Models\Arbitro;
use App\Models\Torneo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class JuegoController extends Controller
{
    public function index()
    {
        Log::info('Accediendo a la lista de juegos');
        
        $juegos = Juego::with([
            'liga', 
            'equipoLocal', 
            'equipoVisitante', 
            'cancha', 
            'temporada',
            'arbitroPrincipal',
            'arbitroAuxiliar',
            'torneo',
            'mesaControl'
        ])
        ->latest()
        ->paginate(9); // Valor directo en lugar de constante

        // Obtener datos para los selects del formulario
        $ligas = Liga::where('activo', true)->orderBy('nombre')->get();
        $temporadas = Temporada::where('activo', true)->orderBy('nombre')->get();
        $canchas = Cancha::where('activo', true)->orderBy('nombre')->get();
        $equipos = Equipo::where('activo', true)->orderBy('nombre')->get();
        $arbitros = Arbitro::where('activo', true)->orderBy('nombre')->get();
        $torneos = Torneo::where('activo', true)->orderBy('nombre')->get();

        return view('admin.juegos.index', compact(
            'juegos',
            'ligas',
            'temporadas',
            'canchas',
            'equipos',
            'torneos',
            'arbitros'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Información general
            'liga_id' => 'required|exists:ligas,id',
            'temporada_id' => 'nullable|exists:temporadas,id',
            'torneo_id' => 'nullable|exists:torneos,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'cancha_id' => 'required|exists:canchas,id',
            'duracion_cuarto' => 'required|integer|min:1',
            'duracion_descanso' => 'required|integer|min:1',
            'estado' => 'required|in:Programado,En Curso,Finalizado,Cancelado,Suspendido',
            'observaciones' => 'nullable|string',
            'activo' => 'required|boolean',
            
            // Asignación de equipos (ahora nullable ambos)
            'equipo_local_id' => 'nullable|exists:equipos,id',
            'equipo_visitante_id' => 'nullable|exists:equipos,id|different:equipo_local_id',
            
            // Árbitros y mesa de control
            'arbitro_principal_id' => 'nullable|exists:arbitros,id',
            'arbitro_auxiliar_id' => 'nullable|exists:arbitros,id',
            'mesa_control_id' => 'nullable|exists:arbitros,id'
        ]);

        // Validaciones adicionales personalizadas
        $validator->after(function ($validator) use ($request) {
            // Si se selecciona un torneo, verificar que esté activo y en estado válido
            if ($request->torneo_id) {
                $torneo = \App\Models\Torneo::find($request->torneo_id);
                if ($torneo && !$torneo->activo) {
                    $validator->errors()->add('torneo_id', 'El torneo seleccionado no está activo.');
                }
                if ($torneo && !in_array($torneo->estado, ['Programado', 'En Curso'])) {
                    $validator->errors()->add('torneo_id', 'El torneo debe estar en estado planificado o en progreso.');
                }
            }
            
            // Si se selecciona un torneo, verificar que la liga coincida
            if ($request->torneo_id && $request->liga_id) {
                $torneo = \App\Models\Torneo::find($request->torneo_id);
                if ($torneo && $torneo->liga_id != $request->liga_id) {
                    $validator->errors()->add('torneo_id', 'El torneo seleccionado no pertenece a la liga especificada.');
                }
            }
            
            // Validar que los equipos pertenezcan a la liga del torneo (si hay torneo y equipo)
            if ($request->torneo_id) {
                $torneo = \App\Models\Torneo::find($request->torneo_id);
                
                if ($torneo) {
                    // Validar equipo local si existe
                    if ($request->equipo_local_id) {
                        $equipoLocal = \App\Models\Equipo::find($request->equipo_local_id);
                        if ($equipoLocal && $equipoLocal->liga_id != $torneo->liga_id) {
                            $validator->errors()->add('equipo_local_id', 'El equipo local no pertenece a la liga del torneo.');
                        }
                    }
                    
                    // Validar equipo visitante si existe
                    if ($request->equipo_visitante_id) {
                        $equipoVisitante = \App\Models\Equipo::find($request->equipo_visitante_id);
                        if ($equipoVisitante && $equipoVisitante->liga_id != $torneo->liga_id) {
                            $validator->errors()->add('equipo_visitante_id', 'El equipo visitante no pertenece a la liga del torneo.');
                        }
                    }
                }
            }
        });

        if ($validator->fails()) {
            Log::warning('Validación de juego falló', [
                'errores' => $validator->errors()->toArray(),
                'datos_enviados' => $request->all()
            ]);
            
            $firstError = $validator->errors()->first();
            return redirect()->back()
                ->withInput()
                ->with('error', $firstError);
        }

        DB::beginTransaction();
        try {
            $validated = $validator->validated();
            
            // Convertir valor vacío a null para torneo_id
            if (empty($validated['torneo_id'])) {
                $validated['torneo_id'] = null;
            }
            
            $juego = Juego::create($validated);
            
            // Si hay torneo, registrar automáticamente los equipos que existan
            if ($validated['torneo_id']) {
                // Registrar equipo local si existe
                if (!empty($validated['equipo_local_id'])) {
                    \DB::table('torneo_equipo')->updateOrInsert(
                        [
                            'torneo_id' => $validated['torneo_id'],
                            'equipo_id' => $validated['equipo_local_id']
                        ],
                        [
                            'grupo' => null,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                }
                
                // Registrar equipo visitante si existe
                if (!empty($validated['equipo_visitante_id'])) {
                    \DB::table('torneo_equipo')->updateOrInsert(
                        [
                            'torneo_id' => $validated['torneo_id'],
                            'equipo_id' => $validated['equipo_visitante_id']
                        ],
                        [
                            'grupo' => null,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                }
            }
            
            DB::commit();
            Log::info('Juego creado exitosamente', ['id' => $juego->id]);
            
            return redirect()->route('juegos.index')
                ->with('success', 'Juego creado exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear juego: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el juego. Intenta nuevamente.');
        }
    }

    public function update(Request $request, Juego $juego)
    {
        Log::info('Iniciando actualización de juego', ['juego_id' => $juego->id, 'datos_recibidos' => $request->all()]);

        $validator = Validator::make($request->all(), [
            // Información general
            'liga_id' => 'required|exists:ligas,id',
            'temporada_id' => 'nullable|exists:temporadas,id',
            'torneo_id' => 'nullable|exists:torneos,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'cancha_id' => 'required|exists:canchas,id',
            'duracion_cuarto' => 'required|integer|min:1',
            'duracion_descanso' => 'required|integer|min:1',
            'estado' => 'required|in:Programado,En Curso,Finalizado,Cancelado,Suspendido',
            'observaciones' => 'nullable|string',
            'activo' => 'boolean',
            
            // Asignación de equipos
            'equipo_local_id' => 'nullable|exists:equipos,id',
            'equipo_visitante_id' => 'nullable|exists:equipos,id|different:equipo_local_id',
            
            // Árbitros y mesa de control
            'arbitro_principal_id' => 'nullable|exists:arbitros,id',
            'arbitro_auxiliar_id' => 'nullable|exists:arbitros,id',
            'mesa_control_id' => 'nullable|exists:arbitros,id'
        ]);


        // Validaciones adicionales personalizadas
        $validator->after(function ($validator) use ($request, $juego) {
            Log::debug('Ejecutando validaciones adicionales para el juego', ['juego_id' => $juego->id]);

            // Si se selecciona un torneo, verificar que esté activo y en estado válido
            if ($request->torneo_id) {
                $torneo = \App\Models\Torneo::find($request->torneo_id);
                Log::debug('Validando torneo', ['torneo_id' => $request->torneo_id, 'torneo_activo' => $torneo ? $torneo->activo : null]);

                if ($torneo && !$torneo->activo) {
                    $validator->errors()->add('torneo_id', 'El torneo seleccionado no está activo.');
                    Log::warning('Torneo no activo', ['torneo_id' => $request->torneo_id]);
                }
                if ($torneo && !in_array($torneo->estado, ['Programado', 'En Curso'])) {
                    $validator->errors()->add('torneo_id', 'El torneo debe estar en estado planificado o en progreso.');
                    Log::warning('Estado de torneo inválido', ['torneo_id' => $request->torneo_id, 'estado' => $torneo->estado]);
                }
                // Validar que no sean el mismo equipo cuando ambos existen
                if ($request->equipo_local_id && $request->equipo_visitante_id && 
                    $request->equipo_local_id == $request->equipo_visitante_id) {
                    $validator->errors()->add('equipo_visitante_id', 'El equipo visitante debe ser diferente al equipo local.');
                }
            }
            
            // Si se selecciona un torneo, verificar que la liga coincida
            if ($request->torneo_id && $request->liga_id) {
                $torneo = \App\Models\Torneo::find($request->torneo_id);
                if ($torneo && $torneo->liga_id != $request->liga_id) {
                    $validator->errors()->add('torneo_id', 'El torneo seleccionado no pertenece a la liga especificada.');
                    Log::warning('Inconsistencia entre liga y torneo', [
                        'liga_solicitada' => $request->liga_id,
                        'liga_torneo' => $torneo->liga_id
                    ]);
                }
            }
            
            // Validar que al menos un equipo esté presente
            if (empty($request->equipo_local_id) && empty($request->equipo_visitante_id) && 
                empty($juego->equipo_local_id) && empty($juego->equipo_visitante_id)) {
                $validator->errors()->add('equipo_local_id', 'Debe especificar al menos un equipo (local o visitante).');
            }
            
            // Validar que los equipos pertenezcan a la liga del torneo (si hay torneo y equipo)
            if ($request->torneo_id) {
                $torneo = \App\Models\Torneo::find($request->torneo_id);
                Log::debug('Validando equipos contra torneo', ['torneo_id' => $request->torneo_id]);
                
                if ($torneo) {
                    // Validar equipo local si existe
                    if ($request->equipo_local_id) {
                        $equipoLocal = \App\Models\Equipo::find($request->equipo_local_id);
                        if ($equipoLocal && $equipoLocal->liga_id != $torneo->liga_id) {
                            $validator->errors()->add('equipo_local_id', 'El equipo local no pertenece a la liga del torneo.');
                            Log::warning('Equipo local no pertenece a la liga del torneo', [
                                'equipo_id' => $request->equipo_local_id,
                                'liga_equipo' => $equipoLocal->liga_id,
                                'liga_torneo' => $torneo->liga_id
                            ]);
                        }
                    }
                    
                    // Validar equipo visitante si existe
                    if ($request->equipo_visitante_id) {
                        $equipoVisitante = \App\Models\Equipo::find($request->equipo_visitante_id);
                        if ($equipoVisitante && $equipoVisitante->liga_id != $torneo->liga_id) {
                            $validator->errors()->add('equipo_visitante_id', 'El equipo visitante no pertenece a la liga del torneo.');
                            Log::warning('Equipo visitante no pertenece a la liga del torneo', [
                                'equipo_id' => $request->equipo_visitante_id,
                                'liga_equipo' => $equipoVisitante->liga_id,
                                'liga_torneo' => $torneo->liga_id
                            ]);
                        }
                    }
                }
            }
        });

        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            Log::warning('Validación fallida al actualizar juego', [
                'juego_id' => $juego->id,
                'errores' => $validator->errors()->toArray(),
                'primer_error' => $firstError
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', $firstError);
        }

        DB::beginTransaction();
        Log::info('Iniciando transacción para actualizar juego', ['juego_id' => $juego->id]);

        try {
            $validated = $validator->validated();
            Log::debug('Datos validados', ['datos_validados' => $validated]);
            
            // Convertir valor vacío a null para torneo_id
            if (empty($validated['torneo_id'])) {
                $validated['torneo_id'] = null;
                Log::debug('Torneo ID convertido a null');
            }
            
            // Por esta versión mejorada:
            if (!array_key_exists('equipo_local_id', $validated) || is_null($validated['equipo_local_id'])) {
                $validated['equipo_local_id'] = null; // Permite remover el equipo
            }

            if (!array_key_exists('equipo_visitante_id', $validated) || is_null($validated['equipo_visitante_id'])) {
                $validated['equipo_visitante_id'] = null; // Permite remover el equipo
            }
            
            $juego->update($validated);
            Log::info('Juego actualizado en base de datos', ['juego_id' => $juego->id, 'campos_actualizados' => $validated]);
            
            // Si hay torneo, registrar automáticamente los equipos que existan
            if ($validated['torneo_id']) {
                Log::info('Registrando equipos en torneo', ['torneo_id' => $validated['torneo_id']]);
                
                // Registrar equipo local si existe
                if (!empty($validated['equipo_local_id'])) {
                    \DB::table('torneo_equipo')->updateOrInsert(
                        [
                            'torneo_id' => $validated['torneo_id'],
                            'equipo_id' => $validated['equipo_local_id']
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                    Log::info('Equipo local registrado en torneo', [
                        'torneo_id' => $validated['torneo_id'],
                        'equipo_id' => $validated['equipo_local_id']
                    ]);
                }
                
                // Registrar equipo visitante si existe
                if (!empty($validated['equipo_visitante_id'])) {
                    \DB::table('torneo_equipo')->updateOrInsert(
                        [
                            'torneo_id' => $validated['torneo_id'],
                            'equipo_id' => $validated['equipo_visitante_id']
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                    Log::info('Equipo visitante registrado en torneo', [
                        'torneo_id' => $validated['torneo_id'],
                        'equipo_id' => $validated['equipo_visitante_id']
                    ]);
                }
            }
            
            DB::commit();
            Log::info('Transacción completada exitosamente', ['juego_id' => $juego->id]);
            
            return redirect()->route('juegos.index')
                ->with('success', 'Juego actualizado exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar juego: ' . $e->getMessage(), [
                'juego_id' => $juego->id,
                'exception' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el juego. Intenta nuevamente.');
        }
    }

    public function destroy(Juego $juego)
    {
        DB::beginTransaction();
        try {
            $juego->delete();
            
            DB::commit();
            Log::info('Juego eliminado exitosamente', ['id' => $juego->id]);
            
            return redirect()->route('juegos.index')
                ->with('success', 'Juego eliminado exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar juego: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al eliminar el juego. Intenta nuevamente.');
        }
    }
}