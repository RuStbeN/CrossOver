<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('juegos', function (Blueprint $table) {
            $table->foreignId('torneo_id')
                  ->nullable()
                  ->constrained('torneos')
                  ->onDelete('set null')
                  ->after('liga_id');
            
            // Agregar índice para optimizar consultas
            $table->index(['torneo_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('juegos', function (Blueprint $table) {
            $table->dropForeign(['torneo_id']);
            $table->dropIndex(['torneo_id', 'fecha']);
            $table->dropColumn('torneo_id');
        });
    }
};