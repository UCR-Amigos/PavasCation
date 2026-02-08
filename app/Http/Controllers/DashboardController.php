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

        // Totales del mes seleccionado (antes era semanal, ahora es mensual)
        $totalesMes = Culto::whereBetween('fecha', [$inicioMes, $finMes])
            ->with('totales')
            ->get()
            ->reduce(function ($carry, $culto) {
                if ($culto->totales) {
                    $carry['total_general'] += $culto->totales->total_general;
                    $carry['total_diezmo'] += $culto->totales->total_diezmo;
                    $carry['total_ofrenda_especial'] += $culto->totales->total_ofrenda_especial;
                    $carry['total_misiones'] += $culto->totales->total_misiones;
                    $carry['total_seminario'] += $culto->totales->total_seminario;
                    $carry['total_campamento'] += $culto->totales->total_campamento;
                    $carry['total_pro_templo'] += $culto->totales->total_pro_templo;
                    $carry['total_suelto'] += $culto->totales->total_suelto;
                }
                return $carry;
            }, [
                'total_general' => 0,
                'total_diezmo' => 0,
                'total_ofrenda_especial' => 0,
                'total_misiones' => 0,
                'total_seminario' => 0,
                'total_campamento' => 0,
                'total_pro_templo' => 0,
                'total_suelto' => 0,
            ]);

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
