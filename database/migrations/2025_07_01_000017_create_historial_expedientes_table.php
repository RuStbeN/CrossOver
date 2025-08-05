<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('historial_expedientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_medico_id')->constrained('expedientes_medicos')->onDelete('cascade');
            $table->enum('accion', ['Creación', 'Actualización', 'Eliminación', 'Vencimiento']);
            $table->text('cambios')->nullable();
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('usuario', 150)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['expediente_medico_id', 'created_at']);
            $table->index('accion');
            $table->index('usuario');
        });
    }

    public function down(): void {
        Schema::dropIfExists('historial_expedientes');
    }
};