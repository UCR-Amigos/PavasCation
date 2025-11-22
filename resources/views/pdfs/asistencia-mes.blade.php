<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Asistencia - {{ $nombreMes }} {{ $año }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1 { text-align: center; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #3b82f6; color: white; font-size: 10px; }
        .total-row { font-weight: bold; background-color: #dbeafe; }
    </style>
</head>
<body>
    <h1>Reporte de Asistencia - {{ $nombreMes }} {{ $año }}</h1>
    <p><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Total</th>
                <th>Capilla</th>
                <th>0-1</th>
                <th>2-6</th>
                <th>7-8</th>
                <th>9-11</th>
            </tr>
        </thead>
        <tbody>
            @php $totalMes = 0; @endphp
            @foreach($cultos as $culto)
            @if($culto->asistencia)
            <tr>
                <td>{{ $culto->fecha->format('d/m/Y') }}</td>
                <td>{{ ucfirst($culto->tipo_culto) }}</td>
                <td style="font-weight: bold;">{{ $culto->asistencia->total_asistencia }}</td>
                <td>{{ $culto->asistencia->chapel_adultos_hombres + $culto->asistencia->chapel_adultos_mujeres + 
                       $culto->asistencia->chapel_adultos_mayores_hombres + $culto->asistencia->chapel_adultos_mayores_mujeres + 
                       $culto->asistencia->chapel_jovenes_masculinos + $culto->asistencia->chapel_jovenes_femeninas + 
                       $culto->asistencia->chapel_maestros_hombres }}</td>
                <td>{{ $culto->asistencia->clase_0_1_hombres + $culto->asistencia->clase_0_1_mujeres + 
                       $culto->asistencia->clase_0_1_maestros_hombres + $culto->asistencia->clase_0_1_maestros_mujeres }}</td>
                <td>{{ $culto->asistencia->clase_2_6_hombres + $culto->asistencia->clase_2_6_mujeres + 
                       $culto->asistencia->clase_2_6_maestros_hombres + $culto->asistencia->clase_2_6_maestros_mujeres }}</td>
                <td>{{ $culto->asistencia->clase_7_8_hombres + $culto->asistencia->clase_7_8_mujeres + 
                       $culto->asistencia->clase_7_8_maestros_hombres + $culto->asistencia->clase_7_8_maestros_mujeres }}</td>
                <td>{{ $culto->asistencia->clase_9_11_hombres + $culto->asistencia->clase_9_11_mujeres + 
                       $culto->asistencia->clase_9_11_maestros_hombres + $culto->asistencia->clase_9_11_maestros_mujeres }}</td>
            </tr>
            @php $totalMes += $culto->asistencia->total_asistencia; @endphp
            @endif
            @endforeach
            <tr class="total-row">
                <td colspan="2">TOTAL DEL MES</td>
                <td>{{ $totalMes }}</td>
                <td colspan="5">Promedio: {{ $cultos->count() > 0 ? round($totalMes / $cultos->count(), 2) : 0 }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
