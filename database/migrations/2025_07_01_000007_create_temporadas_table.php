<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temporadas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->time('horario_inicio');
            $table->time('horario_fin');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys para auditoría
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            // Índices
            $table->index(['activo', 'fecha_inicio']);
            $table->index(['fecha_inicio', 'fecha_fin']);
            $table->index('created_by');
            $table->index('updated_by');
        });

        // Constraints
        DB::statement('ALTER TABLE temporadas ADD CONSTRAINT chk_fechas CHECK (fecha_fin > fecha_inicio)');
        DB::statement('ALTER TABLE temporadas ADD CONSTRAINT chk_horarios CHECK (horario_fin > horario_inicio)');
    }

    public function down(): void
    {
        Schema::dropIfExists('temporadas');
    }
};