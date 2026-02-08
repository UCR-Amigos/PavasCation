<?php

namespace App\Http\Controllers;

use App\Models\Culto;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el mes y año desde la request o usar el actual
        $mes = $request->input('mes', Carbon::now()->month);
        $año = $request->input('año', Carbon::now()->year);
        
        $inicioMes = Carbon::createFromDate($año, $mes, 1)->startOfMonth();
        $finMes = Carbon::createFromDate($año, $mes, 1)->endOfMonth();

        // Últimos 10 cultos para el gráfico de barras
        $cultosRecientes = Culto::with('totales')
            ->orderBy('fecha', 'desc')
            ->take(10)
            ->get()
            ->reverse()
            ->values();

        // Totales del mes seleccionado - calculados directamente de sobres/detalles
        $cultosMes = Culto::whereBetween('fecha', [$inicioMes, $finMes])
            ->with(['sobres.detalles', 'ofrendasSueltas', 'egresos'])
            ->get();

        $totalesMes = [
            'total_general' => 0,
            'total_diezmo' => 0,
            'total_ofrenda_especial' => 0,
            'total_misiones' => 0,
            'total_seminario' => 0,
            'total_campamento' => 0,
            'total_pro_templo' => 0,
            'total_suelto' => 0,
        ];

        foreach ($cultosMes as $culto) {
            foreach ($culto->sobres as $sobre) {
                foreach ($sobre->detalles as $detalle) {
                    $key = 'total_' . $detalle->categoria;
                    if (isset($totalesMes[$key])) {
                        $totalesMes[$key] += $detalle->monto;
                    }
                }
            }
            $totalesMes['total_suelto'] += $culto->ofrendasSueltas->sum('monto');
        }

        $totalesMes['total_general'] = $totalesMes['total_diezmo']
            + $totalesMes['total_ofrenda_especial']
            + $totalesMes['total_misiones']
            + $totalesMes['total_seminario']
            + $totalesMes['total_campamento']
            + $totalesMes['total_pro_templo']
            + $totalesMes['total_suelto'];

        // Distribución por categorías (mismo mes)
        $distribucion = [
            'diezmo' => $totalesMes['total_diezmo'],
            'ofrenda_especial' => $totalesMes['total_ofrenda_especial'],
            'misiones' => $totalesMes['total_misiones'],
            'seminario' => $totalesMes['total_seminario'],
            'campamento' => $totalesMes['total_campamento'],
            'pro_templo' => $totalesMes['total_pro_templo'],
            'suelto' => $totalesMes['total_suelto'],
        ];

        // Asistencia (últimos 10 cultos)
        $asistencias = Culto::with('asistencia')
            ->orderBy('fecha', 'desc')
            ->take(10)
            ->get()
            ->reverse()
            ->values()
            ->map(function ($culto) {
                return [
                    'fecha' => $culto->fecha->format('d/m'),
                    'total' => $culto->asistencia ? $culto->asistencia->total_asistencia : 0,
                ];
            });

        // Promesas cumplidas vs pendientes
        $personas = Persona::with(['promesas', 'sobres.detalles'])->get();
        
        $promesasStatus = [
            'cumplidas' => 0,
            'pendientes' => 0,
        ];

        foreach ($personas as $persona) {
            foreach ($persona->promesas as $promesa) {
                // Excluir promesas de diezmo: el diezmo no forma parte de compromisos/promesas
                if (strtolower($promesa->categoria) === 'diezmo') {
                    continue;
                }
                $catVariants = \App\Models\SobreDetalle::categoriaVariants($promesa->categoria);
                $montoPagado = $persona->sobres()
                    ->whereHas('detalles', function ($query) use ($catVariants) {
                        $query->whereIn('categoria', $catVariants);
                    })
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->get()
                    ->sum(function ($sobre) use ($catVariants) {
                        return $sobre->detalles()
                            ->whereIn('categoria', $catVariants)
                            ->sum('monto');
                    });

                if ($montoPagado >= $promesa->monto) {
                    $promesasStatus['cumplidas']++;
                } else {
                    $promesasStatus['pendientes']++;
                }
            }
        }

        return view('dashboard', compact(
            'cultosRecientes',
            'totalesMes',
            'distribucion',
            'asistencias',
            'promesasStatus',
            'mes',
            'año',
            'inicioMes',
            'finMes'
        ));
    }
}
