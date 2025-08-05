<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('juego_titulares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('juego_id')->constrained('juegos')->onDelete('cascade');
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->enum('tipo_equipo', ['Local', 'Visitante']);
            $table->boolean('es_titular')->default(true);
            $table->integer('minutos_jugados')->unsigned()->default(0);
            $table->integer('puntos')->unsigned()->default(0);
            $table->integer('asistencias')->unsigned()->default(0);
            $table->integer('rebotes')->unsigned()->default(0);
            $table->integer('faltas')->unsigned()->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index(['juego_id', 'tipo_equipo']);
            $table->index(['jugador_id', 'juego_id']);
            $table->unique(['juego_id', 'jugador_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('juego_titulares');
    }
};