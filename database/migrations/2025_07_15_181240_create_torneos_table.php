<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('torneos', function (Blueprint $table) {
            $table->id();
            
            // Información básica
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->enum('tipo', [
                'eliminacion_directa', 
                'doble_eliminacion', 
                'round_robin', 
                'grupos_eliminacion',
                'por_puntos'
            ]);
            
            // Relaciones
            $table->foreignId('liga_id')->constrained('ligas');
            $table->foreignId('temporada_id')->constrained('temporadas');
            $table->foreignId('categoria_id')->constrained('categorias');
            
            // Configuración de tiempo
            $table->unsignedSmallInteger('duracion_cuarto_minutos')->default(12);
            $table->unsignedSmallInteger('duracion_descanso_minutos')->default(10);
            $table->unsignedSmallInteger('tiempo_entre_partidos_minutos')->default(15);
            
            // Configuración general
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->decimal('premio_total', 10, 2)->default(0);
            
            // Configuración específica para torneos por puntos
            $table->unsignedTinyInteger('puntos_por_victoria')->default(3);
            $table->unsignedTinyInteger('puntos_por_empate')->default(1);
            $table->unsignedTinyInteger('puntos_por_derrota')->default(0);
            $table->boolean('usa_playoffs')->default(false);
            $table->unsignedTinyInteger('equipos_playoffs')->nullable()->default(null);
            
            // Estado
            $table->enum('estado', ['Programado', 'En Curso', 'Finalizado', 'Cancelado', 'Suspendido'])->default('Programado');

            // Metadata
            $table->boolean('activo')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla pivote torneo_cancha (para torneos que usen múltiples canchas)
        Schema::create('torneo_cancha', function (Blueprint $table) {
            $table->foreignId('torneo_id')->constrained('torneos')->onDelete('cascade');
            $table->foreignId('cancha_id')->constrained('canchas')->onDelete('cascade');
            $table->primary(['torneo_id', 'cancha_id']);
            
            // Campos adicionales para relación cancha-torneo
            $table->boolean('es_principal')->default(false);
            $table->unsignedSmallInteger('orden_prioridad')->default(1);
            $table->text('notas')->nullable();
            
            $table->timestamps();
        });

        // Tabla pivote torneo_equipo
        Schema::create('torneo_equipo', function (Blueprint $table) {
            $table->foreignId('torneo_id')->constrained('torneos')->onDelete('cascade');
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
            $table->primary(['torneo_id', 'equipo_id']);
            $table->unsignedSmallInteger('grupo')->nullable(); // Para torneos por grupos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('torneo_equipo');
        Schema::dropIfExists('torneo_cancha');
        Schema::dropIfExists('torneos');
    }
};