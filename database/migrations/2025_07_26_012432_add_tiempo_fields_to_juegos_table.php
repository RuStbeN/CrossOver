<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('juegos', function (Blueprint $table) {
            $table->integer('cuarto_actual')->default(1)->after('puntos_visitante');
            $table->enum('estado_tiempo', ['pausado', 'corriendo', 'descanso'])->default('pausado')->after('cuarto_actual');
            $table->integer('tiempo_restante')->nullable()->after('estado_tiempo'); // en segundos
            $table->timestamp('ultimo_cambio_tiempo')->nullable()->after('tiempo_restante');
            $table->boolean('en_descanso')->default(false)->after('ultimo_cambio_tiempo');
        });
    }

    public function down()
    {
        Schema::table('juegos', function (Blueprint $table) {
            $table->dropColumn(['cuarto_actual', 'estado_tiempo', 'tiempo_restante', 'ultimo_cambio_tiempo', 'en_descanso']);
        });
    }
};