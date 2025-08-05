<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrenadores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('telefono', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('cedula_profesional', 50)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->text('experiencia')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['activo', 'created_at']);
            $table->index('email');
            $table->index('telefono');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrenadores');
    }
};