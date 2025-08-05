<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('torneo_clasificacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained('torneos')->onDelete('cascade');
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
            
            // Estadísticas básicas
            $table->unsignedInteger('partidos_jugados')->default(0);
            $table->unsignedInteger('partidos_ganados')->default(0);
            $table->unsignedInteger('partidos_empatados')->default(0);
            $table->unsignedInteger('partidos_perdidos')->default(0);
            
            // Puntos y estadísticas avanzadas
            $table->integer('puntos_totales')->default(0);
            $table->bigInteger('puntos_favor')->default(0);
            $table->bigInteger('puntos_contra')->default(0);
            $table->integer('diferencia_puntos')->default(0);
            
            // Posición (se puede calcular dinámicamente pero se almacena para mejor performance)
            $table->unsignedInteger('posicion')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Clave única compuesta
            $table->unique(['torneo_id', 'equipo_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('torneo_clasificacion');
    }
};