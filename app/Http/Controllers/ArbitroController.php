<?php
namespace App\Http\Controllers;

use App\Models\Arbitro;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ArbitroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Log::info('Accediendo a la lista de árbitros');
        
        // Validar parámetros individualmente para evitar el error gte
        $request->validate([
            'search' => 'nullable|string|max:255',
            'estado' => 'nullable|in:0,1',
            'edad_min' => 'nullable|integer|min:18|max:100',
            'edad_max' => 'nullable|integer|min:18|max:100',
            'ordenar' => 'nullable|in:created_at,updated_at,nombre,edad,correo',
            'direccion' => 'nullable|in:asc,desc'
        ]);
        
        // Obtener parámetros validados
        $search = $request->input('search');
        $estado = $request->input('estado');
        $edadMin = $request->input('edad_min');
        $edadMax = $request->input('edad_max');
        $ordenar = $request->input('ordenar', 'created_at');
        $direccion = $request->input('direccion', 'desc');
        
        // Validación manual para edad máxima >= edad mínima
        if (!empty($edadMin) && !empty($edadMax) && $edadMax < $edadMin) {
            return redirect()
                ->route('arbitros.index')
                ->with('error', 'La edad máxima debe ser mayor o igual a la edad mínima')
                ->withInput();
        }
        
        // Construir la consulta base
        $query = Arbitro::with('user');
        
        // Aplicar filtros
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', '%'.$search.'%')
                ->orWhere('correo', 'LIKE', '%'.$search.'%')
                ->orWhere('telefono', 'LIKE', '%'.$search.'%');
            });
        }
        
        if (!is_null($estado)) {
            $query->where('activo', (bool)$estado);
        }
        
        if (!empty($edadMin)) {
            $query->where('edad', '>=', (int)$edadMin);
        }
        
        if (!empty($edadMax)) {
            $query->where('edad', '<=', (int)$edadMax);
        }
        
        // Asegurar que exista edad para ordenar por ella
        if ($ordenar === 'edad') {
            $query->whereNotNull('edad');
        }
        
        // Conteos
        $totalArbitros = Arbitro::count();
        $arbitrosFiltered = $query->count();
        
        // Paginación con ordenamiento
        $arbitros = $query->orderBy($ordenar, $direccion)
                        ->paginate(12)
                        ->appends($request->query());
        
        return view('admin.arbitros.index', compact(
            'arbitros',
            'totalArbitros',
            'arbitrosFiltered'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:150',
                'edad' => 'nullable|integer|min:18|max:100',
                'direccion' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:20',
                'correo' => 'required|email|max:100|unique:arbitros,correo|unique:users,email',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'activo' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return redirect()->back()
                    ->withInput()
                    ->with('error', $firstError);
            }

            $validatedData = $validator->validated();

            // Generar contraseña temporal
            $passwordTemporal = $this->generateTemporaryPassword();

            // Crear usuario
            $user = User::create([
                'name' => $validatedData['nombre'],
                'email' => $validatedData['correo'],
                'password' => Hash::make($passwordTemporal),
                'email_verified_at' => now(),
            ]);

            // Asignar rol de árbitro
            $rolArbitro = Rol::where('nombre', 'arbitro')->first();
            if ($rolArbitro) {
                $user->roles()->attach($rolArbitro->id);
            }

            // Agregar user_id a los datos del árbitro
            $validatedData['user_id'] = $user->id;

            // Manejo de la foto
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('arbitros/fotos', 'public');
                $validatedData['foto'] = $path;
                Log::info('Foto guardada', ['ruta' => $path]);
            }

            $arbitro = Arbitro::create($validatedData);
            Log::info('Árbitro creado exitosamente', ['id' => $arbitro->id, 'user_id' => $user->id]);

            return redirect()->route('arbitros.index')
                ->with('success', 'Árbitro registrado exitosamente.')
                ->with('password_temporal', $passwordTemporal)
                ->with('arbitro_nombre', $validatedData['nombre']);

        } catch (\Exception $e) {
            Log::error('Error al registrar árbitro: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar el árbitro. Intenta nuevamente.');
        }
    }

    public function update(Request $request, Arbitro $arbitro)
    {
        Log::info('Intentando actualizar árbitro', ['id' => $arbitro->id]);
        
        try {
            // Reglas de validación básicas
            $rules = [
                'nombre' => 'required|string|max:150',
                'edad' => 'nullable|integer|min:18|max:100',
                'direccion' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:20',
                'correo' => 'nullable|email|max:100|unique:arbitros,correo,' . $arbitro->id . '|unique:users,email,' . $arbitro->user_id,
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'activo' => 'required|boolean',
            ];

            // Agregar validaciones de contraseña si se proporciona
            if ($request->filled('nueva_password')) {
                $rules['nueva_password'] = 'required|string|min:8|confirmed';
                $rules['nueva_password_confirmation'] = 'required';
            }

            $validator = Validator::make($request->all(), $rules, [
                'nueva_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
                'nueva_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
                'nueva_password_confirmation.required' => 'Debe confirmar la nueva contraseña.',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return redirect()->back()
                    ->withInput()
                    ->with('error', $firstError);
            }

            $validatedData = $validator->validated();

            // Actualizar usuario asociado
            if ($arbitro->user) {
                $userUpdateData = [
                    'name' => $validatedData['nombre'],
                    'email' => $validatedData['correo'],
                ];

                // Agregar cambio de contraseña si se proporciona
                if ($request->filled('nueva_password')) {
                    $userUpdateData['password'] = Hash::make($validatedData['nueva_password']);
                    $userUpdateData['password_changed_at'] = now();
                    $userUpdateData['must_change_password'] = true; // Forzar cambio en próximo login
                    
                    Log::info('Contraseña de árbitro cambiada por administrador', [
                        'arbitro_id' => $arbitro->id,
                        'arbitro_nombre' => $validatedData['nombre'],
                        'admin_user_id' => Auth::id(),
                        'changed_at' => now()
                    ]);
                }

                $arbitro->user->update($userUpdateData);
            }

            // Manejo de la foto
            if ($request->hasFile('foto')) {
                if ($arbitro->foto) {
                    Storage::disk('public')->delete($arbitro->foto);
                    Log::info('Foto anterior eliminada', ['ruta' => $arbitro->foto]);
                }
                $path = $request->file('foto')->store('arbitros/fotos', 'public');
                $validatedData['foto'] = $path;
                Log::info('Nueva foto guardada', ['ruta' => $path]);
            }

            // Remover campos de contraseña antes de actualizar el árbitro
            unset($validatedData['nueva_password'], $validatedData['nueva_password_confirmation']);

            $arbitro->update($validatedData);
            Log::info('Árbitro actualizado exitosamente', ['id' => $arbitro->id]);

            // Mensaje de éxito personalizado
            $mensaje = 'Árbitro actualizado exitosamente.';
            if ($request->filled('nueva_password')) {
                $mensaje .= ' La contraseña ha sido cambiada y el árbitro deberá cambiarla en su próximo acceso.';
            }

            return redirect()->route('arbitros.index')
                ->with('success', $mensaje);
                            
        } catch (\Exception $e) {
            Log::error('Error al actualizar árbitro: ' . $e->getMessage(), [
                'arbitro_id' => $arbitro->id,
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el árbitro. Intenta nuevamente.');
        }
    }

    public function destroy(Arbitro $arbitro)
    {
        Log::info('Intentando eliminar árbitro', ['id' => $arbitro->id]);
        
        try {
            if ($arbitro->foto) {
                Storage::delete('public/' . $arbitro->foto);
                Log::info('Foto eliminada', ['ruta' => $arbitro->foto]);
            }

            // Eliminar usuario asociado
            if ($arbitro->user) {
                $arbitro->user->delete();
                Log::info('Usuario eliminado', ['user_id' => $arbitro->user->id]);
            }

            $arbitro->delete();
            Log::info('Árbitro eliminado', ['id' => $arbitro->id]);

            return redirect()->route('arbitros.index')
                ->with('success', 'Árbitro eliminado exitosamente.');
                            
        } catch (\Exception $e) {
            Log::error('Error al eliminar árbitro: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al eliminar el árbitro. Intenta nuevamente.');
        }
    }

    /**
     * Generar contraseña temporal
     */
    private function generateTemporaryPassword()
    {
        return 'arbitro_' . Str::random(6) . '_' . date('md');
    }
}