<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('expedientes_medicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_id')->unique()->constrained('jugadores')->onDelete('cascade');
            $table->enum('grupo_sanguineo', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->text('alergias')->nullable();
            $table->text('vacunas')->nullable();
            $table->text('enfermedades_cronicas')->nullable();
            $table->text('medicamentos_permanentes')->nullable();
            $table->string('coordinador_autorizado', 150)->nullable();
            $table->text('restricciones_medicas')->nullable();
            $table->string('constancia_medica_url')->nullable();
            $table->date('fecha_expedicion');
            $table->string('medico_responsable', 150);
            $table->integer('vigencia_meses')->unsigned()->default(12);
            $table->string('firma_responsable', 150)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->integer('version')->unsigned()->default(1);
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['activo', 'fecha_vencimiento']);
            $table->index('medico_responsable');
            $table->index('fecha_expedicion');
        });
    }

    public function down(): void {
        Schema::dropIfExists('expedientes_medicos');
    }
};