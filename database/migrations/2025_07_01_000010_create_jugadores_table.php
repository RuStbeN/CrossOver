<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('jugadores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('rfc', 20);
            $table->integer('edad')->unsigned();
            $table->enum('sexo', ['Masculino', 'Femenino']);
            $table->string('direccion', 255)->nullable();
            $table->enum('estado_fisico', ['Óptimo', 'Regular', 'Lesionado'])->default('Óptimo');
            $table->string('telefono', 15)->nullable();
            $table->string('email', 255);
            $table->string('foto_url')->nullable();
            $table->foreignId('liga_id')->constrained('ligas')->onDelete('restrict');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('restrict');
            $table->string('contacto_emergencia_nombre', 255);
            $table->string('contacto_emergencia_telefono', 15);
            $table->string('contacto_emergencia_relacion', 100);
            $table->date('fecha_nacimiento');
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices y restricciones
            $table->index(['categoria_id', 'activo']);
            $table->index(['liga_id', 'activo']);
            $table->index('estado_fisico');
            $table->unique('rfc');
        });
    }

    public function down(): void {
        Schema::dropIfExists('jugadores');
    }
};