<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('liga_id')->constrained('ligas')->onDelete('cascade');
            $table->foreignId('entrenador_id')->nullable()->constrained('entrenadores')->onDelete('set null');
            $table->string('logo_url')->nullable();
            $table->string('color_primario', 7)->nullable(); // Hex color
            $table->string('color_secundario', 7)->nullable(); // Hex color
            $table->date('fecha_fundacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['categoria_id', 'activo']);
            $table->index(['liga_id', 'activo']);
            $table->index('entrenador_id');
            $table->unique(['categoria_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
