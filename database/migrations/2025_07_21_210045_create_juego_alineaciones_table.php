<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('juego_alineaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('juego_id')->constrained('juegos');
            $table->foreignId('jugador_id')->constrained('jugadores');
            $table->foreignId('equipo_id')->constrained('equipos');
            $table->enum('tipo_equipo', ['Local', 'Visitante']);
            $table->boolean('es_titular')->default(false);
            $table->unsignedSmallInteger('numero_camiseta');
            $table->enum('posicion_jugada', [
                'Base (PG)', 
                'Escolta (SG)', 
                'Alero (SF)', 
                'Ala-Pívot (PF)', 
                'Centro (C)'
            ]);
            
            // Estadísticas
            $table->unsignedSmallInteger('minutos_jugados')->default(0);
            $table->unsignedSmallInteger('puntos')->default(0);
            $table->unsignedSmallInteger('tiros_libres_anotados')->default(0);
            $table->unsignedSmallInteger('tiros_libres_intentados')->default(0);
            $table->unsignedSmallInteger('tiros_2pts_anotados')->default(0);
            $table->unsignedSmallInteger('tiros_2pts_intentados')->default(0);
            $table->unsignedSmallInteger('tiros_3pts_anotados')->default(0);
            $table->unsignedSmallInteger('tiros_3pts_intentados')->default(0);
            $table->unsignedSmallInteger('asistencias')->default(0);
            $table->unsignedSmallInteger('rebotes_defensivos')->default(0);
            $table->unsignedSmallInteger('rebotes_ofensivos')->default(0);
            $table->unsignedSmallInteger('rebotes_totales')->default(0);
            $table->unsignedSmallInteger('robos')->default(0);
            $table->unsignedSmallInteger('bloqueos')->default(0);
            $table->unsignedSmallInteger('perdidas')->default(0);
            $table->unsignedSmallInteger('faltas_personales')->default(0);
            $table->unsignedSmallInteger('faltas_tecnicas')->default(0);
            $table->unsignedSmallInteger('faltas_descalificantes')->default(0);
            
            // Control de tiempo
            $table->boolean('esta_en_cancha')->default(false);
            $table->unsignedSmallInteger('minuto_entrada')->nullable();
            $table->unsignedSmallInteger('minuto_salida')->nullable();
            
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Índices
            $table->unique(['juego_id', 'jugador_id']);
            $table->index(['juego_id', 'equipo_id']);
            $table->index(['jugador_id', 'puntos']);
            $table->index(['es_titular', 'juego_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('juego_alineaciones');
    }
};