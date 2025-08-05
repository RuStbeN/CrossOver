<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('cirugias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_medico_id')->constrained('expedientes_medicos')->onDelete('cascade');
            $table->string('tipo', 200);
            $table->string('lugar', 200);
            $table->date('fecha_cirugia');
            $table->string('cirujano', 150)->nullable();
            $table->text('motivo')->nullable();
            $table->text('complicaciones')->nullable();
            $table->boolean('exitosa')->default(true);
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->index(['expediente_medico_id', 'fecha_cirugia']);
            $table->index('tipo');
            $table->index('lugar');
        });
    }

    public function down(): void {
        Schema::dropIfExists('cirugias');
    }
};