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
        // Renombrar categorías en sobre_detalles
        DB::table('sobre_detalles')->where('categoria', 'campa')->update(['categoria' => 'campamento']);
        DB::table('sobre_detalles')->where('categoria', 'prestamo')->update(['categoria' => 'pro_templo']);
        DB::table('sobre_detalles')->where('categoria', 'construccion')->delete();
        DB::table('sobre_detalles')->where('categoria', 'micro')->delete();

        // Renombrar categorías en promesas
        DB::table('promesas')->where('categoria', 'campa')->update(['categoria' => 'campamento']);
        DB::table('promesas')->where('categoria', 'prestamo')->update(['categoria' => 'pro_templo']);
        DB::table('promesas')->where('categoria', 'construccion')->delete();
        DB::table('promesas')->where('categoria', 'micro')->delete();

        // Renombrar categorías en compromisos
        DB::table('compromisos')->where('categoria', 'campa')->update(['categoria' => 'campamento']);
        DB::table('compromisos')->where('categoria', 'prestamo')->update(['categoria' => 'pro_templo']);
        DB::table('compromisos')->where('categoria', 'construccion')->delete();
        DB::table('compromisos')->where('categoria', 'micro')->delete();
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
