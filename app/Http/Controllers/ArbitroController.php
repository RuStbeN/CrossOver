<?php
namespace App\Http\Controllers;

use App\Models\Arbitro;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ArbitroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Log::info('Accediendo a la lista de árbitros');
        $arbitros = Arbitro::with('user')->orderBy('created_at', 'desc')->paginate(12);
        return view('admin.arbitros.index', compact('arbitros'));
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
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:150',
                'edad' => 'nullable|integer|min:18|max:100',
                'direccion' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:20',
                'correo' => 'nullable|email|max:100|unique:arbitros,correo,' . $arbitro->id . '|unique:users,email,' . $arbitro->user_id,
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

            // Actualizar usuario asociado
            if ($arbitro->user) {
                $arbitro->user->update([
                    'name' => $validatedData['nombre'],
                    'email' => $validatedData['correo'],
                ]);
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

            $arbitro->update($validatedData);
            Log::info('Árbitro actualizado exitosamente', ['id' => $arbitro->id]);

            return redirect()->route('arbitros.index')
                ->with('success', 'Árbitro actualizado exitosamente.');
                            
        } catch (\Exception $e) {
            Log::error('Error al actualizar árbitro: ' . $e->getMessage());
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