<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('juegos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('liga_id')->constrained('ligas')->onDelete('cascade');
            $table->foreignId('equipo_local_id')->nullable()->constrained('equipos')->onDelete('cascade');
            $table->foreignId('equipo_visitante_id')->nullable()->constrained('equipos')->onDelete('cascade');
            $table->date('fecha');
            $table->time('hora');
            $table->foreignId('cancha_id')->constrained('canchas')->onDelete('cascade');
            $table->string('arbitro_principal', 150)->nullable();
            $table->string('arbitro_auxiliar', 150)->nullable();
            $table->string('mesa_control', 150)->nullable();
            $table->integer('duracion_cuarto')->unsigned()->default(10);
            $table->integer('duracion_descanso')->unsigned()->default(5);
            $table->enum('estado', ['Programado', 'En Curso', 'Finalizado', 'Cancelado', 'Suspendido'])->default('Programado');
            $table->enum('fase', ['Fase Regular','Desempate','Cuartos de Final','Semifinal','Final','Tercer Puesto'])->default('Fase Regular');
            $table->integer('puntos_local')->unsigned()->nullable();
            $table->integer('puntos_visitante')->unsigned()->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('temporada_id')->nullable()->constrained('temporadas')->onDelete('set null');
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['liga_id', 'fecha']);
            $table->index(['cancha_id', 'fecha', 'hora']);
            $table->index(['equipo_local_id', 'fecha']);
            $table->index(['equipo_visitante_id', 'fecha']);
            $table->index('estado');
            $table->index(['fecha', 'hora']);
        });

        // Agrega esto después de crear la tabla:
        DB::statement('
            ALTER TABLE juegos 
            ADD CONSTRAINT chk_equipos_diferentes 
            CHECK (equipo_local_id != equipo_visitante_id)
        ');
    }

    public function down(): void {
        Schema::dropIfExists('juegos');
    }
};