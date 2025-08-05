<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('canchas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->text('direccion')->nullable();
            $table->integer('capacidad')->unsigned()->nullable();
            $table->enum('tipo_superficie', ['Sintética', 'Natural', 'Cemento', 'Parquet', 'Otros'])->default('Sintética');
            $table->boolean('techada')->default(false);
            $table->boolean('iluminacion')->default(false);
            $table->text('equipamiento')->nullable();
            $table->decimal('tarifa_por_hora', 8, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['activo', 'created_at']);
            $table->index('capacidad');
            $table->index('tipo_superficie');
        });
    }

    public function down(): void {
        Schema::dropIfExists('canchas');
    }
};