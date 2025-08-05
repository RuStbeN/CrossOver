<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Jugador;
use App\Models\Equipo;
use App\Models\Liga;
use App\Models\Categoria;

class JugadoresController extends Controller
{

    public function index()
    {
        try {
            Log::info('Consultando listado de jugadores', [
                'usuario' => auth()->id(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()
            ]);

            // Obtener jugadores con sus relaciones actuales
            $jugadores = Jugador::with([
                    'equipos_actual.equipo',
                    'liga', 
                    'categoria'
                ])
                ->latest()
                ->paginate(12);  // Valor directo en lugar de constante
                
            // Obtener datos para los selects
            $equipos = Equipo::orderBy('nombre')->get();
            $ligas = Liga::where('activo', true)->orderBy('nombre')->get();
            $categorias = Categoria::orderBy('nombre')->get();
            
            Log::info('Listado de jugadores obtenido exitosamente', [
                'total_jugadores' => $jugadores->total(),
                'current_page' => $jugadores->currentPage(),
                'per_page' => $jugadores->perPage(),
                'total_equipos' => $equipos->count(),
                'total_ligas' => $ligas->count(),
                'total_categorias' => $categorias->count(),
                'usuario' => auth()->id()
            ]);

            return view('admin.jugadores.index', compact('jugadores', 'equipos', 'ligas', 'categorias'));
        } catch (\Exception $e) {
            Log::error('Error al obtener listado de jugadores', [
                'error' => $e->getMessage(),
                'usuario' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Error al cargar el listado de jugadores');
        }
    }
    
    
    public function store(Request $request)
    {
        try {
            Log::info('Inicio del proceso de creación de jugador', [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Validación de datos
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'rfc' => 'required|string|max:20',
                'edad' => 'required|integer|min:15|max:50',
                'sexo' => 'required|in:Masculino,Femenino',
                'direccion' => 'nullable|string|max:255',
                'estado_fisico' => 'required|in:Óptimo,Regular,Lesionado',
                'telefono' => 'nullable|string|max:15',
                'email' => 'required|email|max:255',
                'foto_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'liga_id' => 'required|exists:ligas,id',
                'categoria_id' => 'required|exists:categorias,id',
                'equipo_id' => 'required|exists:equipos,id', 
                'posicion_principal' => 'required|in:Base (PG),Escolta (SG),Alero (SF),Ala-Pívot (PF),Centro (C)',
                'posicion_secundaria' => 'nullable|in:Base (PG),Escolta (SG),Alero (SF),Ala-Pívot (PF),Centro (C)',
                'numero_camiseta' => 'required|integer|min:1|max:99',
                'contacto_emergencia_nombre' => 'required|string|max:255',
                'contacto_emergencia_telefono' => 'required|string|max:15',
                'contacto_emergencia_relacion' => 'required|string|max:100',
                'fecha_nacimiento' => 'required|date',
                'fecha_ingreso' => 'required|date',
                'temporada_id' => 'nullable|exists:temporadas,id',
                'torneo_id' => 'nullable|exists:torneos,id',
                'es_capitan' => 'nullable|in:0,1',
                'activo' => 'required|boolean'
            ]);

            Log::debug('Datos validados correctamente', [
                'data' => array_merge($validatedData, ['foto_url' => isset($validatedData['foto_url']) ? 'presente' : 'ausente'])
            ]);

            // Procesar la foto si se subió
            if ($request->hasFile('foto_url')) {
                $path = $request->file('foto_url')->store('jugadores/fotos', 'public');
                $validatedData['foto_url'] = $path;
                Log::info('Foto subida para nuevo jugador', [
                    'path' => $path,
                    'size' => $request->file('foto_url')->getSize(),
                    'mime_type' => $request->file('foto_url')->getMimeType()
                ]);
            } else {
                Log::debug('No se subió archivo de foto para el jugador');
            }

            // Crear el jugador (sin equipo_id)
            $jugadorData = [
                'nombre' => $validatedData['nombre'],
                'rfc' => $validatedData['rfc'],
                'edad' => $validatedData['edad'],
                'sexo' => $validatedData['sexo'],
                'direccion' => $validatedData['direccion'] ?? null,
                'estado_fisico' => $validatedData['estado_fisico'],
                'telefono' => $validatedData['telefono'] ?? null,
                'email' => $validatedData['email'],
                'foto_url' => $validatedData['foto_url'] ?? null,
                'liga_id' => $validatedData['liga_id'],
                'categoria_id' => $validatedData['categoria_id'],
                'contacto_emergencia_nombre' => $validatedData['contacto_emergencia_nombre'],
                'contacto_emergencia_telefono' => $validatedData['contacto_emergencia_telefono'],
                'contacto_emergencia_relacion' => $validatedData['contacto_emergencia_relacion'],
                'fecha_nacimiento' => $validatedData['fecha_nacimiento'],
                'activo' => $validatedData['activo'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ];

            Log::debug('Preparando datos para crear jugador', ['jugador_data' => $jugadorData]);

            $jugador = Jugador::create($jugadorData);
            Log::info('Jugador creado en base de datos', ['jugador_id' => $jugador->id]);
            
            // Crear la relación en equipo_jugadores
            $equipoJugadorData = [
                'equipo_id' => $validatedData['equipo_id'],
                'jugador_id' => $jugador->id,
                'temporada_id' => $validatedData['temporada_id'] ?? null,
                'torneo_id' => $validatedData['torneo_id'] ?? null,
                'fecha_ingreso' => $validatedData['fecha_ingreso'],
                'numero_camiseta' => $validatedData['numero_camiseta'],
                'posicion_principal' => $validatedData['posicion_principal'],
                'posicion_secundaria' => $validatedData['posicion_secundaria'] ?? null,
                'es_capitan' => $validatedData['es_capitan'] === '1' ? 1 : 0,
                'activo' => $validatedData['activo'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ];

            Log::debug('Preparando datos para relación equipo_jugador', ['equipo_jugador_data' => $equipoJugadorData]);

            DB::table('equipo_jugadores')->insert($equipoJugadorData);
            Log::info('Relación equipo-jugador creada exitosamente', [
                'jugador_id' => $jugador->id,
                'equipo_id' => $validatedData['equipo_id']
            ]);
            
            Log::info('Proceso de creación de jugador completado exitosamente', [
                'jugador_id' => $jugador->id,
                'equipo_id' => $validatedData['equipo_id'],
                'user_id' => auth()->id(),
                'tiempo_ejecucion' => microtime(true) - LARAVEL_START
            ]);

            return redirect()->route('jugadores.index')
                ->with('success', 'Jugador creado y asignado a equipo exitosamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación al crear jugador', [
                'errors' => $e->errors(),
                'input' => $request->except(['foto_url']), // Excluimos el archivo por seguridad
                'user_id' => auth()->id()
            ]);
            
            throw $e; // Laravel manejará esta excepción y redirigirá con los errores

        } catch (\Exception $e) {
            Log::error('Error crítico al crear jugador', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['foto_url']), // Excluimos el archivo por seguridad
                'user_id' => auth()->id(),
                'ip' => $request->ip()
            ]);

            return redirect()->back()
                ->withInput($request->except(['foto_url']))
                ->with('error', 'Ocurrió un error inesperado al crear el jugador. Por favor intente nuevamente.');
        }
    }


    public function update(Request $request, string $id)
    {
        try {
            Log::info('Inicio del proceso de actualización de jugador', [
                'jugador_id' => $id,
                'user_id' => auth()->id()
            ]);

            // Buscar el jugador
            $jugador = Jugador::findOrFail($id);
            
            // Validación de datos
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'rfc' => 'required|string|max:20',
                'edad' => 'required|integer|min:15|max:50',
                'sexo' => 'required|in:Masculino,Femenino',
                'direccion' => 'nullable|string|max:255',
                'estado_fisico' => 'required|in:Óptimo,Regular,Lesionado',
                'telefono' => 'nullable|string|max:15',
                'email' => 'required|email|max:255',
                'foto_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'liga_id' => 'required|exists:ligas,id',
                'categoria_id' => 'required|exists:categorias,id',
                'equipo_id' => 'required|exists:equipos,id', 
                'posicion_principal' => 'required|in:Base (PG),Escolta (SG),Alero (SF),Ala-Pívot (PF),Centro (C)',
                'posicion_secundaria' => 'nullable|in:Base (PG),Escolta (SG),Alero (SF),Ala-Pívot (PF),Centro (C)',
                'numero_camiseta' => 'required|integer|min:1|max:99',
                'contacto_emergencia_nombre' => 'required|string|max:255',
                'contacto_emergencia_telefono' => 'required|string|max:15',
                'contacto_emergencia_relacion' => 'required|string|max:100',
                'fecha_nacimiento' => 'required|date',
                'fecha_ingreso' => 'required|date',
                'temporada_id' => 'nullable|exists:temporadas,id',
                'torneo_id' => 'nullable|exists:torneos,id',
                'es_capitan' => 'nullable|boolean',
                'activo' => 'required|boolean'
            ]);

            Log::debug('Datos validados para actualización', [
                'jugador_id' => $id,
                'data' => $validatedData
            ]);

            // Manejo de la foto (si se subió una nueva)
            if ($request->hasFile('foto_url')) {
                // Eliminar la foto anterior si existe
                if ($jugador->foto_url) {
                    Storage::disk('public')->delete($jugador->foto_url);
                }
                
                // Guardar la nueva foto
                $path = $request->file('foto_url')->store('jugadores/fotos', 'public');
                $validatedData['foto_url'] = $path;
                
                Log::info('Foto actualizada para jugador', [
                    'jugador_id' => $id,
                    'new_path' => $path
                ]);
            } else {
                // Mantener la foto existente si no se subió una nueva
                unset($validatedData['foto_url']);
            }

            // Actualizar los datos básicos del jugador
            $jugador->update([
                'nombre' => $validatedData['nombre'],
                'rfc' => $validatedData['rfc'],
                'edad' => $validatedData['edad'],
                'sexo' => $validatedData['sexo'],
                'direccion' => $validatedData['direccion'] ?? null,
                'estado_fisico' => $validatedData['estado_fisico'],
                'telefono' => $validatedData['telefono'] ?? null,
                'email' => $validatedData['email'],
                'foto_url' => $validatedData['foto_url'] ?? $jugador->foto_url,
                'liga_id' => $validatedData['liga_id'],
                'categoria_id' => $validatedData['categoria_id'],
                'contacto_emergencia_nombre' => $validatedData['contacto_emergencia_nombre'],
                'contacto_emergencia_telefono' => $validatedData['contacto_emergencia_telefono'],
                'contacto_emergencia_relacion' => $validatedData['contacto_emergencia_relacion'],
                'fecha_nacimiento' => $validatedData['fecha_nacimiento'],
                'activo' => $validatedData['activo'],
                'updated_by' => auth()->id(),
            ]);

            // Actualizar o crear la relación en equipo_jugadores
            $equipoJugadorData = [
                'equipo_id' => $validatedData['equipo_id'],
                'temporada_id' => $validatedData['temporada_id'] ?? null,
                'torneo_id' => $validatedData['torneo_id'] ?? null,
                'fecha_ingreso' => $validatedData['fecha_ingreso'],
                'numero_camiseta' => $validatedData['numero_camiseta'],
                'posicion_principal' => $validatedData['posicion_principal'],
                'posicion_secundaria' => $validatedData['posicion_secundaria'] ?? null,
                'es_capitan' => $validatedData['es_capitan'] ?? false,
                'activo' => $validatedData['activo'],
                'updated_by' => auth()->id(),
            ];

            // Buscar si ya existe una relación para este jugador
            $equipoJugador = DB::table('equipo_jugadores')
                ->where('jugador_id', $jugador->id)
                ->first();

            if ($equipoJugador) {
                // Actualizar la relación existente
                DB::table('equipo_jugadores')
                    ->where('jugador_id', $jugador->id)
                    ->update($equipoJugadorData);
            } else {
                // Crear nueva relación
                $equipoJugadorData['jugador_id'] = $jugador->id;
                $equipoJugadorData['created_by'] = auth()->id();
                DB::table('equipo_jugadores')->insert($equipoJugadorData);
            }
            
            Log::info('Jugador y relación con equipo actualizados exitosamente', [
                'jugador_id' => $jugador->id,
                'equipo_id' => $validatedData['equipo_id'],
                'user_id' => auth()->id()
            ]);

            return redirect()->route('jugadores.index')
                ->with('success', 'Jugador actualizado exitosamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación al actualizar jugador', [
                'jugador_id' => $id,
                'errors' => $e->errors(),
                'input' => $request->all(),
                'user_id' => auth()->id()
            ]);
            throw $e;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Jugador no encontrado al intentar actualizar', [
                'jugador_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->route('jugadores.index')
                ->with('error', 'El jugador que intentas actualizar no existe.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar jugador', [
                'jugador_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['password']),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al actualizar el jugador. Por favor intente nuevamente.');
        }
    }


    public function destroy(string $id)
    {
        try {
            Log::info('Inicio del proceso de eliminación de jugador', [
                'jugador_id' => $id,
                'user_id' => auth()->id()
            ]);

            // Buscar el jugador
            $jugador = Jugador::findOrFail($id);
            
            // Guardar información para el log antes de eliminar
            $jugadorInfo = [
                'id' => $jugador->id,
                'nombre' => $jugador->nombre,
                'equipo' => $jugador->equipo_id,
                'foto_url' => $jugador->foto_url
            ];

            // Eliminar la foto si existe
            if ($jugador->foto_url) {
                Storage::disk('public')->delete($jugador->foto_url);
                Log::info('Foto de jugador eliminada', [
                    'jugador_id' => $id,
                    'foto_path' => $jugador->foto_url
                ]);
            }

            // Eliminar el jugador
            $jugador->delete();

            Log::info('Jugador eliminado exitosamente', [
                'jugador_info' => $jugadorInfo,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('jugadores.index')
                ->with('success', 'Jugador eliminado exitosamente');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Jugador no encontrado al intentar eliminar', [
                'jugador_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->route('jugadores.index')
                ->with('error', 'El jugador que intentas eliminar no existe.');

        } catch (\Exception $e) {
            Log::error('Error al eliminar jugador', [
                'jugador_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return redirect()->route('jugadores.index')
                ->with('error', 'Ocurrió un error al eliminar el jugador. Por favor intente nuevamente.');
        }
    }
}
