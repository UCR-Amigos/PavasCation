<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencia</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #3b82f6; color: white; }
        .total { font-weight: bold; background-color: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Reporte de Asistencia</h1>
    <p><strong>Fecha de Generación:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo Culto</th>
                <th>Total</th>
                <th>Capilla</th>
                <th>Clases</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cultos as $culto)
            @if($culto->asistencia)
            <tr>
                <td>{{ $culto->fecha->format('d/m/Y') }}</td>
                <td>{{ ucfirst($culto->tipo_culto) }}</td>
                <td class="total">{{ $culto->asistencia->total_asistencia }}</td>
                <td>{{ $culto->asistencia->chapel_adultos_hombres + $culto->asistencia->chapel_adultos_mujeres + 
                       $culto->asistencia->chapel_adultos_mayores_hombres + $culto->asistencia->chapel_adultos_mayores_mujeres + 
                       $culto->asistencia->chapel_jovenes_masculinos + $culto->asistencia->chapel_jovenes_femeninas + 
                       $culto->asistencia->chapel_maestros_hombres }}</td>
                <td>{{ $culto->asistencia->total_asistencia - 
                       ($culto->asistencia->chapel_adultos_hombres + $culto->asistencia->chapel_adultos_mujeres + 
                        $culto->asistencia->chapel_adultos_mayores_hombres + $culto->asistencia->chapel_adultos_mayores_mujeres + 
                        $culto->asistencia->chapel_jovenes_masculinos + $culto->asistencia->chapel_jovenes_femeninas + 
                        $culto->asistencia->chapel_maestros_hombres) }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
