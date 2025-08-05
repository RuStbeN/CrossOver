<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('lesiones_previas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_medico_id')->constrained('expedientes_medicos')->onDelete('cascade');
            $table->string('tipo', 200);
            $table->date('fecha_lesion');
            $table->text('tratamiento')->nullable();
            $table->string('tiempo_recuperacion', 100)->nullable();
            $table->enum('gravedad', ['Leve', 'Moderada', 'Severa'])->nullable();
            $table->string('medico_tratante', 150)->nullable();
            $table->boolean('recuperacion_completa')->default(false);
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->index(['expediente_medico_id', 'fecha_lesion']);
            $table->index('tipo');
            $table->index('gravedad');
        });
    }

    public function down(): void {
        Schema::dropIfExists('lesiones_previas');
    }
};