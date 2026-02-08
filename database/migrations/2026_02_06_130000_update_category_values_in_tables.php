<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Actualizar los valores de categoría en sobre_detalles, promesas y compromisos
     * para que coincidan con los nuevos nombres: campa→campamento, prestamo→pro_templo
     * y eliminar registros de categorías removidas (construccion, micro).
     */
    public function up(): void
    {
        $tablas = ['sobre_detalles', 'promesas', 'compromisos'];

        foreach ($tablas as $tabla) {
            // Normalizar todas las variantes a los nombres canónicos
            DB::table($tabla)->where('categoria', 'campa')->update(['categoria' => 'campamento']);
            DB::table($tabla)->where('categoria', 'prestamo')->update(['categoria' => 'pro_templo']);
            DB::table($tabla)->where('categoria', 'pro-templo')->update(['categoria' => 'pro_templo']);
            DB::table($tabla)->where('categoria', 'ofrenda-especial')->update(['categoria' => 'ofrenda_especial']);
            DB::table($tabla)->where('categoria', 'construccion')->delete();
            DB::table($tabla)->where('categoria', 'micro')->delete();
        }
    }

    public function down(): void
    {
        // Revertir nombres de categorías
        DB::table('sobre_detalles')->where('categoria', 'campamento')->update(['categoria' => 'campa']);
        DB::table('sobre_detalles')->where('categoria', 'pro_templo')->update(['categoria' => 'prestamo']);

        DB::table('promesas')->where('categoria', 'campamento')->update(['categoria' => 'campa']);
        DB::table('promesas')->where('categoria', 'pro_templo')->update(['categoria' => 'prestamo']);

        DB::table('compromisos')->where('categoria', 'campamento')->update(['categoria' => 'campa']);
        DB::table('compromisos')->where('categoria', 'pro_templo')->update(['categoria' => 'prestamo']);

        // Nota: los registros de construccion y micro eliminados no se pueden restaurar
    }
};
