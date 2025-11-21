<?php

namespace App\Http\Controllers;

use App\Models\Culto;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IngresosAsistenciaController extends Controller
{
    public function index()
    {
        // Totales de la semana
        $inicioSemana = Carbon::now()->startOfWeek();
        $finSemana = Carbon::now()->endOfWeek();

        $cultosSemanales = Culto::whereBetween('fecha', [$inicioSemana, $finSemana])
            ->with(['totales', 'asistencia'])
            ->orderBy('fecha', 'desc')
            ->get();

        $totalSemanal = $cultosSemanales->sum(fn($c) => $c->totales ? $c->totales->total_general : 0);

        // Distribución por categoría
        $categorias = [
            'diezmo' => 0,
            'misiones' => 0,
            'seminario' => 0,
            'campa' => 0,
            'prestamo' => 0,
            'construccion' => 0,
            'micro' => 0,
            'suelto' => 0,
        ];

        foreach ($cultosSemanales as $culto) {
            if ($culto->totales) {
                $categorias['diezmo'] += $culto->totales->total_diezmo;
                $categorias['misiones'] += $culto->totales->total_misiones;
                $categorias['seminario'] += $culto->totales->total_seminario;
                $categorias['campa'] += $culto->totales->total_campa;
                $categorias['prestamo'] += $culto->totales->total_prestamo;
                $categorias['construccion'] += $culto->totales->total_construccion;
                $categorias['micro'] += $culto->totales->total_micro;
                $categorias['suelto'] += $culto->totales->total_suelto;
            }
        }

        return view('ingresos-asistencia.index', compact('cultosSemanales', 'totalSemanal', 'categorias'));
    }
}
