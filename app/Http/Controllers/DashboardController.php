<?php

namespace App\Http\Controllers;

use App\Models\Culto;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Últimos 10 cultos para el gráfico de barras
        $cultosRecientes = Culto::with('totales')
            ->orderBy('fecha', 'desc')
            ->take(10)
            ->get()
            ->reverse()
            ->values();

        // Totales de la semana actual
        $inicioSemana = Carbon::now()->startOfWeek();
        $finSemana = Carbon::now()->endOfWeek();

        $totalesSemana = Culto::whereBetween('fecha', [$inicioSemana, $finSemana])
            ->with('totales')
            ->get()
            ->reduce(function ($carry, $culto) {
                if ($culto->totales) {
                    $carry['total_general'] += $culto->totales->total_general;
                    $carry['total_diezmo'] += $culto->totales->total_diezmo;
                    $carry['total_misiones'] += $culto->totales->total_misiones;
                    $carry['total_construccion'] += $culto->totales->total_construccion;
                }
                return $carry;
            }, [
                'total_general' => 0,
                'total_diezmo' => 0,
                'total_misiones' => 0,
                'total_construccion' => 0,
            ]);

        // Distribución por categorías (mes actual)
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        $distribucion = Culto::whereBetween('fecha', [$inicioMes, $finMes])
            ->with('totales')
            ->get()
            ->reduce(function ($carry, $culto) {
                if ($culto->totales) {
                    $carry['diezmo'] += $culto->totales->total_diezmo;
                    $carry['misiones'] += $culto->totales->total_misiones;
                    $carry['seminario'] += $culto->totales->total_seminario;
                    $carry['campa'] += $culto->totales->total_campa;
                    $carry['prestamo'] += $culto->totales->total_prestamo;
                    $carry['construccion'] += $culto->totales->total_construccion;
                    $carry['micro'] += $culto->totales->total_micro;
                    $carry['suelto'] += $culto->totales->total_suelto;
                }
                return $carry;
            }, [
                'diezmo' => 0,
                'misiones' => 0,
                'seminario' => 0,
                'campa' => 0,
                'prestamo' => 0,
                'construccion' => 0,
                'micro' => 0,
                'suelto' => 0,
            ]);

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
                $montoPagado = $persona->sobres()
                    ->whereHas('detalles', function ($query) use ($promesa) {
                        $query->where('categoria', $promesa->categoria);
                    })
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->get()
                    ->sum(function ($sobre) use ($promesa) {
                        return $sobre->detalles()
                            ->where('categoria', $promesa->categoria)
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
            'totalesSemana',
            'distribucion',
            'asistencias',
            'promesasStatus'
        ));
    }
}
