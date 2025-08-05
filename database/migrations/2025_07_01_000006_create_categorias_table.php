<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->integer('edad_minima')->unsigned();
            $table->integer('edad_maxima')->unsigned();
            $table->foreignId('liga_id')->constrained('ligas')->onDelete('cascade');
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['liga_id', 'activo']);
            $table->index(['edad_minima', 'edad_maxima']);
            $table->unique(['liga_id', 'nombre']);
        
        });
        
        // Check constraint para validar edades
        DB::statement('ALTER TABLE categorias ADD CONSTRAINT chk_edad CHECK (edad_maxima > edad_minima);');
        
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};