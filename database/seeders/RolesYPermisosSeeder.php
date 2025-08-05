<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolesYPermisosSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear roles
        $administrador = Rol::create(['nombre' => 'administrador', 'descripcion' => 'Administrador del sistema']);
        $entrenador = Rol::create(['nombre' => 'entrenador', 'descripcion' => 'Entrenador del equipo']);
        $jugador = Rol::create(['nombre' => 'jugador', 'descripcion' => 'Jugador del equipo']);
        $arbitro = Rol::create(['nombre' => 'arbitro', 'descripcion' => 'Árbitro']);

        // 2. Permisos generales (para todos los roles)
        $verTorneos = Permiso::create(['nombre' => 'ver-torneos', 'descripcion' => 'Ver torneos disponibles']);
        $verPartidos = Permiso::create(['nombre' => 'ver-partidos', 'descripcion' => 'Ver partidos']);

        // 3. Permisos específicos por rol
        // Jugador
        $verMiEquipo = Permiso::create(['nombre' => 'ver-mi-equipo', 'descripcion' => 'Ver jugadores de su equipo']);
        $verMisEstadisticas = Permiso::create(['nombre' => 'ver-mis-estadisticas', 'descripcion' => 'Ver sus propias estadísticas']);

        // Entrenador
        $verEstadisticasEquipo = Permiso::create(['nombre' => 'ver-estadisticas-equipo', 'descripcion' => 'Ver estadísticas de todo el equipo']);
        $verJugadoresEquipo = Permiso::create(['nombre' => 'ver-jugadores-equipo', 'descripcion' => 'Ver jugadores de su equipo']);

        // Árbitro
        $actualizarResultados = Permiso::create(['nombre' => 'actualizar-resultados', 'descripcion' => 'Actualizar resultados de partidos']);

        // 4. Asignación de permisos
        // Administrador (todos los permisos)
        $administrador->permissions()->attach(Permiso::all()->pluck('id'));

        // Entrenador
        $entrenador->permissions()->attach([
            $verTorneos->id,
            $verPartidos->id,
            $verEstadisticasEquipo->id,
            $verJugadoresEquipo->id,
        ]);

        // Jugador
        $jugador->permissions()->attach([
            $verTorneos->id,
            $verPartidos->id,
            $verMiEquipo->id,
            $verMisEstadisticas->id,
        ]);

        // Árbitro
        $arbitro->permissions()->attach([
            $verPartidos->id,
            $actualizarResultados->id,
        ]);

        // 5. Crear usuario administrador (opcional)
        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@crossovermx.com',
            'password' => bcrypt('password123'),
        ])->roles()->attach($administrador->id);


        
    }
}