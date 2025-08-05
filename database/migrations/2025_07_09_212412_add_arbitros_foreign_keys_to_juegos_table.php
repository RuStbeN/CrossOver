<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('juegos', function (Blueprint $table) {
            if (Schema::hasColumn('juegos', 'arbitro_principal')) {
                $table->dropColumn('arbitro_principal');
            }
            if (Schema::hasColumn('juegos', 'arbitro_auxiliar')) {
                $table->dropColumn('arbitro_auxiliar');
            }
            if (Schema::hasColumn('juegos', 'mesa_control')) {
                $table->dropColumn('mesa_control');
            }

            $table->foreignId('arbitro_principal_id')
                ->nullable()
                ->constrained('arbitros')
                ->onDelete('set null')
                ->after('cancha_id');

            $table->foreignId('arbitro_auxiliar_id')
                ->nullable()
                ->constrained('arbitros')
                ->onDelete('set null')
                ->after('arbitro_principal_id');

            $table->foreignId('mesa_control_id')
                ->nullable()
                ->constrained('arbitros')
                ->onDelete('set null')
                ->after('arbitro_auxiliar_id');
        });

        // ❌ OMITIMOS el CHECK que causa el error
    }

    public function down(): void {
        Schema::table('juegos', function (Blueprint $table) {
            $table->dropForeign(['arbitro_principal_id']);
            $table->dropForeign(['arbitro_auxiliar_id']);
            $table->dropForeign(['mesa_control_id']);

            $table->dropColumn([
                'arbitro_principal_id',
                'arbitro_auxiliar_id',
                'mesa_control_id',
            ]);

            $table->string('arbitro_principal', 150)->nullable();
            $table->string('arbitro_auxiliar', 150)->nullable();
            $table->string('mesa_control', 150)->nullable();
        });
    }
};
