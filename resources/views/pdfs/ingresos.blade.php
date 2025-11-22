<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ingresos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        h1 { text-align: center; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: right; }
        th { background-color: #10b981; color: white; font-size: 9px; }
        td:first-child, th:first-child { text-align: left; }
        .total-row { font-weight: bold; background-color: #d1fae5; }
    </style>
</head>
<body>
    <h1>Reporte de Ingresos - {{ ucfirst($tipoReporte) }}</h1>
    <p><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Fecha/Periodo</th>
                <th>Diezmo</th>
                <th>Misiones</th>
                <th>Seminario</th>
                <th>Camp.</th>
                <th>Const.</th>
                <th>Prést.</th>
                <th>Micro</th>
                <th>Suelto</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totales = [
                    'diezmo' => 0,
                    'misiones' => 0,
                    'seminario' => 0,
                    'campa' => 0,
                    'construccion' => 0,
                    'prestamo' => 0,
                    'micro' => 0,
                    'suelto' => 0,
                    'total' => 0
                ];
            @endphp
            @foreach($registros as $registro)
            <tr>
                <td style="text-align: left;">{{ $registro['fecha'] }}</td>
                <td>${{ number_format($registro['diezmo'], 2) }}</td>
                <td>${{ number_format($registro['misiones'], 2) }}</td>
                <td>${{ number_format($registro['seminario'], 2) }}</td>
                <td>${{ number_format($registro['campa'], 2) }}</td>
                <td>${{ number_format($registro['construccion'], 2) }}</td>
                <td>${{ number_format($registro['prestamo'], 2) }}</td>
                <td>${{ number_format($registro['micro'], 2) }}</td>
                <td>${{ number_format($registro['suelto'], 2) }}</td>
                <td style="font-weight: bold;">${{ number_format($registro['total'], 2) }}</td>
            </tr>
            @php
                $totales['diezmo'] += $registro['diezmo'];
                $totales['misiones'] += $registro['misiones'];
                $totales['seminario'] += $registro['seminario'];
                $totales['campa'] += $registro['campa'];
                $totales['construccion'] += $registro['construccion'];
                $totales['prestamo'] += $registro['prestamo'];
                $totales['micro'] += $registro['micro'];
                $totales['suelto'] += $registro['suelto'];
                $totales['total'] += $registro['total'];
            @endphp
            @endforeach
            <tr class="total-row">
                <td style="text-align: left;">TOTALES</td>
                <td>${{ number_format($totales['diezmo'], 2) }}</td>
                <td>${{ number_format($totales['misiones'], 2) }}</td>
                <td>${{ number_format($totales['seminario'], 2) }}</td>
                <td>${{ number_format($totales['campa'], 2) }}</td>
                <td>${{ number_format($totales['construccion'], 2) }}</td>
                <td>${{ number_format($totales['prestamo'], 2) }}</td>
                <td>${{ number_format($totales['micro'], 2) }}</td>
                <td>${{ number_format($totales['suelto'], 2) }}</td>
                <td>${{ number_format($totales['total'], 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
