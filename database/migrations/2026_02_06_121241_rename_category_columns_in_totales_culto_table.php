<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('totales_culto', function (Blueprint $table) {
            $table->renameColumn('total_campa', 'total_campamento');
            $table->renameColumn('total_prestamo', 'total_pro_templo');
            $table->dropColumn(['total_construccion', 'total_micro']);
        });
    }

    public function down(): void
    {
        Schema::table('totales_culto', function (Blueprint $table) {
            $table->renameColumn('total_campamento', 'total_campa');
            $table->renameColumn('total_pro_templo', 'total_prestamo');
            $table->decimal('total_construccion', 10, 2)->default(0);
            $table->decimal('total_micro', 10, 2)->default(0);
        });
    }
};
