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
        Schema::table('ofrenda_suelta', function (Blueprint $table) {
            $table->foreignId('clase_id')->nullable()->after('culto_id')->constrained('clases_asistencia')->nullOnDelete();
            $table->string('descripcion', 500)->nullable()->after('metodo_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ofrenda_suelta', function (Blueprint $table) {
            $table->dropForeign(['clase_id']);
            $table->dropColumn(['clase_id', 'descripcion']);
        });
    }
};
