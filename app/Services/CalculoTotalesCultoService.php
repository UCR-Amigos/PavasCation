<?php

namespace App\Services;

use App\Models\Culto;
use App\Models\TotalesCulto;

class CalculoTotalesCultoService
{
    public function recalcular(Culto $culto): TotalesCulto
    {
        $sobres = $culto->sobres()->with('detalles')->get();
        $ofrendasSueltas = $culto->ofrendasSueltas;

        $totales = [
            // Nuevos rubros mapeados a columnas existentes
            // Campamento -> total_campa
            // Pro-Templo -> total_construccion
            // Ofrenda Especial -> total_micro (reutilizada)
            'total_diezmo' => 0,
            'total_misiones' => 0,
            'total_seminario' => 0,
            'total_campa' => 0,
            'total_construccion' => 0,
            'total_micro' => 0,
            // Retirado: 'prestamo' ya no se usa
            'total_prestamo' => 0,
            'total_suelto' => 0,
            'cantidad_sobres' => $sobres->count(),
            'cantidad_transferencias' => 0,
        ];

        // Calcular totales de sobres por categoría
        foreach ($sobres as $sobre) {
            if ($sobre->metodo_pago === 'transferencia') {
                $totales['cantidad_transferencias']++;
            }

            foreach ($sobre->detalles as $detalle) {
                $categoria = strtolower($detalle->categoria);
                switch ($categoria) {
                    case 'diezmo':
                        $totales['total_diezmo'] += $detalle->monto;
                        break;
                    case 'misiones':
                        $totales['total_misiones'] += $detalle->monto;
                        break;
                    case 'seminario':
                        $totales['total_seminario'] += $detalle->monto;
                        break;
                    case 'campamento':
                        // Mapea a columna existente 'total_campa'
                        $totales['total_campa'] += $detalle->monto;
                        break;
                    case 'pro-templo':
                    case 'pro templo':
                    case 'protemplo':
                        // Mapea a columna existente 'total_construccion'
                        $totales['total_construccion'] += $detalle->monto;
                        break;
                    case 'ofrenda especial':
                        // Reutiliza 'total_micro' para ofrenda especial
                        $totales['total_micro'] += $detalle->monto;
                        break;
                    default:
                        // Ignorar categorías antiguas no usadas (prestamo, micro_old, etc.)
                        break;
                }
            }
        }

        // Calcular total de ofrendas sueltas
        foreach ($ofrendasSueltas as $ofrenda) {
            $totales['total_suelto'] += $ofrenda->monto;
        }

        // Calcular total general
        $totales['total_general'] = array_sum([
            $totales['total_diezmo'],
            $totales['total_misiones'],
            $totales['total_seminario'],
            $totales['total_campa'],
            // Prestamo removido de cálculo general (ya no se usa)
            $totales['total_construccion'],
            $totales['total_micro'],
            $totales['total_suelto'],
        ]);

        // Actualizar o crear registro de totales
        return $culto->totales()->updateOrCreate(
            ['culto_id' => $culto->id],
            $totales
        );
    }
}
