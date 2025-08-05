<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('evaluaciones_fisicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_medico_id')->constrained('expedientes_medicos')->onDelete('cascade');
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('talla', 5, 2)->nullable();
            $table->decimal('grasa_corporal', 4, 1)->nullable();
            $table->decimal('imc', 4, 1)->nullable();
            $table->integer('frecuencia_cardiaca_reposo')->unsigned()->nullable();
            $table->integer('presion_arterial_sistolica')->unsigned()->nullable();
            $table->integer('presion_arterial_diastolica')->unsigned()->nullable();
            $table->text('observaciones')->nullable();
            $table->date('fecha_evaluacion');
            $table->string('evaluador', 150)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->index(['expediente_medico_id', 'fecha_evaluacion']);
            $table->index('fecha_evaluacion');
        });
    }

    public function down(): void {
        Schema::dropIfExists('evaluaciones_fisicas');
    }
};