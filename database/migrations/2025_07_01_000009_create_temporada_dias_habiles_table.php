<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('temporada_dias_habiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temporada_id')->constrained('temporadas')->onDelete('cascade');
            $table->tinyInteger('dia_semana'); // 1=Lunes, 2=Martes, ..., 7=Domingo
            $table->time('horario_inicio')->nullable(); // Por si necesitas horarios específicos por día
            $table->time('horario_fin')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Índices
            $table->index('temporada_id');
            $table->index(['temporada_id', 'activo']);
            $table->unique(['temporada_id', 'dia_semana']);
        });
        
        // Constraint para validar días de la semana (1-7)
        DB::statement('ALTER TABLE temporada_dias_habiles ADD CONSTRAINT chk_dia_semana CHECK (dia_semana BETWEEN 1 AND 7)');
    }

    public function down(): void {
        Schema::dropIfExists('temporada_dias_habiles');
    }
};