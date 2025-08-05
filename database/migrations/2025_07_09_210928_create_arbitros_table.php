<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('arbitros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->integer('edad')->unsigned()->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 100)->nullable();
            $table->string('foto', 255)->nullable()->comment('Ruta de la foto del árbitro');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('nombre');
        });
    }

    public function down(): void {
        Schema::dropIfExists('arbitros');
    }
};