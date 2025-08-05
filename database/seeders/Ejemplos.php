<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Ejemplos extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar tablas antes de insertar
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('ligas')->truncate();
        DB::table('temporadas')->truncate();
        DB::table('categorias')->truncate();
        DB::table('entrenadores')->truncate();
        DB::table('equipos')->truncate();
        DB::table('jugadores')->truncate();
        DB::table('arbitros')->truncate();
        DB::table('canchas')->truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ligas (4 registros)
        $liga1 = DB::table('ligas')->insertGetId([
            'nombre' => 'Liga Nacional de Baloncesto',
            'descripcion' => 'Liga profesional de baloncesto a nivel nacional',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $liga2 = DB::table('ligas')->insertGetId([
            'nombre' => 'Liga Regional de Baloncesto',
            'descripcion' => 'Liga amateur de baloncesto regional',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $liga3 = DB::table('ligas')->insertGetId([
            'nombre' => 'Liga Juvenil de Baloncesto',
            'descripcion' => 'Liga para jóvenes talentos del baloncesto',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $liga4 = DB::table('ligas')->insertGetId([
            'nombre' => 'Liga Femenina de Baloncesto',
            'descripcion' => 'Liga profesional de baloncesto femenino',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Temporadas (4 registros)
        $temporada1 = DB::table('temporadas')->insertGetId([
            'nombre' => 'Temporada 2023-2024',
            'fecha_inicio' => '2023-09-01',
            'fecha_fin' => '2024-06-30',
            'horario_inicio' => '08:00:00',
            'horario_fin' => '22:00:00',
            'descripcion' => 'Temporada regular 2023-2024',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $temporada2 = DB::table('temporadas')->insertGetId([
            'nombre' => 'Temporada 2024-2025',
            'fecha_inicio' => '2024-09-01',
            'fecha_fin' => '2025-06-30',
            'horario_inicio' => '08:00:00',
            'horario_fin' => '22:00:00',
            'descripcion' => 'Temporada regular 2024-2025',
            'activo' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $temporada3 = DB::table('temporadas')->insertGetId([
            'nombre' => 'Temporada Verano 2024',
            'fecha_inicio' => '2024-06-01',
            'fecha_fin' => '2024-08-31',
            'horario_inicio' => '08:00:00',
            'horario_fin' => '22:00:00',
            'descripcion' => 'Temporada de verano 2024',
            'activo' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $temporada4 = DB::table('temporadas')->insertGetId([
            'nombre' => 'Temporada 2022-2023',
            'fecha_inicio' => '2022-09-01',
            'fecha_fin' => '2023-06-30',
            'horario_inicio' => '08:00:00',
            'horario_fin' => '22:00:00',
            'descripcion' => 'Temporada regular 2022-2023',
            'activo' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Categorías (4 registros)
        $categoria1 = DB::table('categorias')->insertGetId([
            'nombre' => 'Sub-18',
            'edad_minima' => 15,
            'edad_maxima' => 18,
            'liga_id' => $liga1,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoria2 = DB::table('categorias')->insertGetId([
            'nombre' => 'Sub-21',
            'edad_minima' => 18,
            'edad_maxima' => 21,
            'liga_id' => $liga1,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoria3 = DB::table('categorias')->insertGetId([
            'nombre' => 'Sub-15',
            'edad_minima' => 12,
            'edad_maxima' => 15,
            'liga_id' => $liga1,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoria4 = DB::table('categorias')->insertGetId([
            'nombre' => 'Senior',
            'edad_minima' => 23,
            'edad_maxima' => 35,
            'liga_id' => $liga1,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Entrenadores (4 registros)
        $entrenador1 = DB::table('entrenadores')->insertGetId([
            'nombre' => 'Juan Pérez',
            'cedula_profesional' => 'CP123456',
            'email' => 'juan.perez@example.com',
            'telefono' => '5551234567',
            'fecha_nacimiento' => '1980-05-15',
            'experiencia' => '10 años entrenando equipos juveniles',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $entrenador2 = DB::table('entrenadores')->insertGetId([
            'nombre' => 'María García',
            'cedula_profesional' => 'CP654321',
            'email' => 'maria.garcia@example.com',
            'telefono' => '5557654321',
            'fecha_nacimiento' => '1985-08-22',
            'experiencia' => '8 años en ligas regionales',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $entrenador3 = DB::table('entrenadores')->insertGetId([
            'nombre' => 'Luis Gómez',
            'cedula_profesional' => 'CP789012',
            'email' => 'luis.gomez@example.com',
            'telefono' => '5553334455',
            'fecha_nacimiento' => '1975-11-30',
            'experiencia' => '15 años entrenando equipos profesionales',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $entrenador4 = DB::table('entrenadores')->insertGetId([
            'nombre' => 'Sofía López',
            'cedula_profesional' => 'CP345678',
            'email' => 'sofia.lopez@example.com',
            'telefono' => '5554445566',
            'fecha_nacimiento' => '1988-02-14',
            'experiencia' => '7 años en ligas nacionales',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Equipos (4 registros)
        $equipo1 = DB::table('equipos')->insertGetId([
            'nombre' => 'Águilas Doradas',
            'liga_id' => $liga1,
            'categoria_id' => $categoria2,
            'entrenador_id' => $entrenador1,
            'fecha_fundacion' => '2010-01-15',
            'color_primario' => '#FFD700',
            'color_secundario' => '#000000',
            'logo_url' => '',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $equipo2 = DB::table('equipos')->insertGetId([
            'nombre' => 'Leones Rojos',
            'liga_id' => $liga1,
            'categoria_id' => $categoria2,
            'entrenador_id' => $entrenador2,
            'fecha_fundacion' => '2012-03-20',
            'color_primario' => '#FF0000',
            'color_secundario' => '#FFFFFF',
            'logo_url' => '',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $equipo3 = DB::table('equipos')->insertGetId([
            'nombre' => 'Tigres Blancos',
            'liga_id' => $liga1,
            'categoria_id' => $categoria2,
            'entrenador_id' => $entrenador3,
            'fecha_fundacion' => '2015-05-10',
            'color_primario' => '#FFFFFF',
            'color_secundario' => '#000000',
            'logo_url' => '',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $equipo4 = DB::table('equipos')->insertGetId([
            'nombre' => 'Halcones Azules',
            'liga_id' => $liga1,
            'categoria_id' => $categoria2,
            'entrenador_id' => $entrenador4,
            'fecha_fundacion' => '2016-07-15',
            'color_primario' => '#0000FF',
            'color_secundario' => '#FFFFFF',
            'logo_url' => '',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Jugadores (4 registros)
        $jugador1 = DB::table('jugadores')->insertGetId([
            'nombre' => 'Carlos Sánchez',
            'rfc' => 'EKU9003173C9',
            'fecha_nacimiento' => '2005-07-10',
            'edad' => 18,
            'sexo' => 'Masculino',
            'email' => 'carlos.sanchez@example.com',
            'telefono' => '5559876543',
            'direccion' => 'Calle Principal 123',
            'estado_fisico' => 'Óptimo',
            'liga_id' => $liga1,
            'categoria_id' => $categoria1,
            'contacto_emergencia_nombre' => 'Juan Sánchez',
            'contacto_emergencia_telefono' => '5551234567',
            'contacto_emergencia_relacion' => 'Padre',
            'foto_url' => '',
            'activo' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $jugador2 = DB::table('jugadores')->insertGetId([
            'nombre' => 'Ana Martínez',
            'rfc' => 'BRTHRT4353DGF',
            'fecha_nacimiento' => '2004-11-25',
            'edad' => 19,
            'sexo' => 'Femenino',
            'email' => 'ana.martinez@example.com',
            'telefono' => '5556789012',
            'direccion' => 'Avenida Central 456',
            'estado_fisico' => 'Regular',
            'liga_id' => $liga1,
            'categoria_id' => $categoria2,
            'contacto_emergencia_nombre' => 'Luisa Martínez',
            'contacto_emergencia_telefono' => '5557654321',
            'contacto_emergencia_relacion' => 'Madre',
            'foto_url' => '',
            'activo' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $jugador3 = DB::table('jugadores')->insertGetId([
            'nombre' => 'Miguel Ángel Rodríguez',
            'rfc' => 'MARH890612ABC',
            'fecha_nacimiento' => '2006-06-12',
            'edad' => 17,
            'sexo' => 'Masculino',
            'email' => 'miguel.rodriguez@example.com',
            'telefono' => '5558765432',
            'direccion' => 'Calle Oriente 123',
            'estado_fisico' => 'Óptimo',
            'liga_id' => $liga1,
            'categoria_id' => $categoria3,
            'contacto_emergencia_nombre' => 'Ana Rodríguez',
            'contacto_emergencia_telefono' => '5551234567',
            'contacto_emergencia_relacion' => 'Madre',
            'foto_url' => '',
            'activo' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $jugador4 = DB::table('jugadores')->insertGetId([
            'nombre' => 'Lucía Fernández',
            'rfc' => 'LUFE920514DEF',
            'fecha_nacimiento' => '2007-05-14',
            'edad' => 16,
            'sexo' => 'Femenino',
            'email' => 'lucia.fernandez@example.com',
            'telefono' => '5559876543',
            'direccion' => 'Avenida Norte 456',
            'estado_fisico' => 'Óptimo',
            'liga_id' => $liga1,
            'categoria_id' => $categoria4,
            'contacto_emergencia_nombre' => 'Carlos Fernández',
            'contacto_emergencia_telefono' => '5552345678',
            'contacto_emergencia_relacion' => 'Padre',
            'foto_url' => '',
            'activo' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Árbitros (4 registros)
        $arbitro1 = DB::table('arbitros')->insertGetId([
            'nombre' => 'Roberto Jiménez',
            'edad' => 35,
            'correo' => 'roberto.jimenez@example.com',
            'telefono' => '5552345678',
            'direccion' => 'Calle Secundaria 789',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $arbitro2 = DB::table('arbitros')->insertGetId([
            'nombre' => 'Laura Fernández',
            'edad' => 28,
            'correo' => 'laura.fernandez@example.com',
            'telefono' => '5553456789',
            'direccion' => 'Boulevard Norte 321',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $arbitro3 = DB::table('arbitros')->insertGetId([
            'nombre' => 'Pedro Ramírez',
            'edad' => 42,
            'correo' => 'pedro.ramirez@example.com',
            'telefono' => '5554567890',
            'direccion' => 'Avenida Sur 654',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $arbitro4 = DB::table('arbitros')->insertGetId([
            'nombre' => 'María González',
            'edad' => 31,
            'correo' => 'maria.gonzalez@example.com',
            'telefono' => '5555678901',
            'direccion' => 'Calle Poniente 987',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Canchas (4 registros)
        $cancha1 = DB::table('canchas')->insertGetId([
            'nombre' => 'Cancha Principal',
            'direccion' => 'Polideportivo Municipal, Calle Deportes 1',
            'capacidad' => 500,
            'tipo_superficie' => 'Sintética',
            'iluminacion' => 1,
            'techada' => 0,
            'tarifa_por_hora' => 1500.00,
            'equipamiento' => 'Aros profesionales, marcador electrónico',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cancha2 = DB::table('canchas')->insertGetId([
            'nombre' => 'Cancha Secundaria',
            'direccion' => 'Polideportivo Municipal, Calle Deportes 1',
            'capacidad' => 200,
            'tipo_superficie' => 'Cemento',
            'iluminacion' => 1,
            'techada' => 0,
            'tarifa_por_hora' => 800.00,
            'equipamiento' => 'Aros profesionales',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cancha3 = DB::table('canchas')->insertGetId([
            'nombre' => 'Cancha Cubierta',
            'direccion' => 'Gimnasio Municipal, Av. Central 500',
            'capacidad' => 300,
            'tipo_superficie' => 'Parquet',
            'iluminacion' => 1,
            'techada' => 1,
            'tarifa_por_hora' => 2000.00,
            'equipamiento' => 'Aros profesionales, marcador electrónico, gradas',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cancha4 = DB::table('canchas')->insertGetId([
            'nombre' => 'Cancha Escolar',
            'direccion' => 'Escuela Secundaria Técnica, Av. Educación 100',
            'capacidad' => 150,
            'tipo_superficie' => 'Cemento',
            'iluminacion' => 0,
            'techada' => 0,
            'tarifa_por_hora' => 500.00,
            'equipamiento' => 'Aros básicos',
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);



        // Agrega esto al final de tu método run(), después de crear los jugadores y equipos

        // Posiciones posibles
        $posiciones = ['Base (PG)', 'Escolta (SG)', 'Alero (SF)', 'Ala-Pívot (PF)', 'Centro (C)'];

        // Asignar jugadores a equipos para la temporada actual
        DB::table('equipo_jugadores')->insert([
            [
                'equipo_id' => $equipo1, // Águilas Doradas, categoría Sub-18
                'jugador_id' => $jugador1, // Carlos Sánchez, categoría Sub-18
                'temporada_id' => $temporada1,
                'fecha_ingreso' => '2023-09-01',
                'numero_camiseta' => 10,
                'posicion_principal' => $posiciones[0], // Base
                'posicion_secundaria' => $posiciones[1], // Escolta
                'es_capitan' => false,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipo_id' => $equipo2, // Leones Rojos, categoría Sub-21
                'jugador_id' => $jugador2, // Ana Martínez, categoría Sub-21
                'temporada_id' => $temporada1,
                'fecha_ingreso' => '2023-09-01',
                'numero_camiseta' => 23,
                'posicion_principal' => $posiciones[2], // Alero
                'posicion_secundaria' => $posiciones[3], // Ala-Pívot
                'es_capitan' => false,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipo_id' => $equipo3, // Tigres Blancos, categoría Sub-15
                'jugador_id' => $jugador3, // Miguel Ángel Rodríguez, categoría Sub-15
                'temporada_id' => $temporada1,
                'fecha_ingreso' => '2023-09-01',
                'numero_camiseta' => 7,
                'posicion_principal' => $posiciones[4], // Centro
                'posicion_secundaria' => $posiciones[3], // Ala-Pívot
                'es_capitan' => true,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipo_id' => $equipo4, // Halcones Azules, categoría Sub-21
                'jugador_id' => $jugador4, // Lucía Fernández, categoría Senior
                'temporada_id' => $temporada1,
                'fecha_ingreso' => '2023-09-01',
                'numero_camiseta' => 14,
                'posicion_principal' => $posiciones[1], // Escolta
                'posicion_secundaria' => $posiciones[0], // Base
                'es_capitan' => false,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        // También puedes asignar algunos jugadores a equipos en la temporada pasada para demostrar historial
        DB::table('equipo_jugadores')->insert([
            [
                'equipo_id' => $equipo3,
                'jugador_id' => $jugador1,
                'temporada_id' => $temporada4, // Temporada pasada
                'fecha_ingreso' => '2022-09-01',
                'fecha_salida' => '2023-06-30',
                'numero_camiseta' => 5,
                'posicion_principal' => $posiciones[1], // Escolta
                'posicion_secundaria' => null,
                'es_capitan' => false,
                'activo' => false, // Inactivo porque es de temporada pasada
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipo_id' => $equipo4,
                'jugador_id' => $jugador2,
                'temporada_id' => $temporada4, // Temporada pasada
                'fecha_ingreso' => '2022-09-01',
                'fecha_salida' => '2023-06-30',
                'numero_camiseta' => 8,
                'posicion_principal' => $posiciones[3], // Ala-Pívot
                'posicion_secundaria' => $posiciones[4], // Centro
                'es_capitan' => false,
                'activo' => false, // Inactivo porque es de temporada pasada
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    
}