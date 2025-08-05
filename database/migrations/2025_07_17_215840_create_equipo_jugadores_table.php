<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('equipo_jugadores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equipo_id');
            $table->unsignedBigInteger('jugador_id');
            $table->unsignedBigInteger('temporada_id')->nullable();
            $table->unsignedBigInteger('torneo_id')->nullable();
            $table->date('fecha_ingreso');
            $table->date('fecha_salida')->nullable();
            $table->unsignedSmallInteger('numero_camiseta');
            $table->enum('posicion_principal', ['Base (PG)', 'Escolta (SG)', 'Alero (SF)', 'Ala-Pívot (PF)', 'Centro (C)']);
            $table->enum('posicion_secundaria', ['Base (PG)', 'Escolta (SG)', 'Alero (SF)', 'Ala-Pívot (PF)', 'Centro (C)'])->nullable();
            $table->boolean('es_capitan')->default(false);
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Claves foráneas
            $table->foreign('equipo_id')->references('id')->on('equipos');
            $table->foreign('jugador_id')->references('id')->on('jugadores');
            $table->foreign('temporada_id')->references('id')->on('temporadas');
            $table->foreign('torneo_id')->references('id')->on('torneos');

            // Índices únicos
            $table->unique(['jugador_id', 'equipo_id', 'temporada_id', 'deleted_at'], 'unique_jugador_equipo_temporada');
            $table->unique(['equipo_id', 'numero_camiseta', 'temporada_id', 'deleted_at'], 'unique_numero_equipo_temporada');

            // Otros índices
            $table->index(['equipo_id', 'temporada_id'], 'idx_equipo_temporada');
            $table->index(['jugador_id', 'activo'], 'idx_jugador_activo');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Primero recrear la columna equipo_id en jugadores
        Schema::table('jugadores', function (Blueprint $table) {
            $table->unsignedBigInteger('equipo_id')->nullable()->after('categoria_id');
            $table->foreign('equipo_id')->references('id')->on('equipos');
        });

        // Luego eliminar la tabla equipo_jugadores
        Schema::dropIfExists('equipo_jugadores');
    }
};