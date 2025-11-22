<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Asistencia - {{ $culto->fecha->format('d/m/Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #1f2937; }
        .info { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #3b82f6; color: white; text-align: left; }
        .total { font-weight: bold; background-color: #dbeafe; }
    </style>
</head>
<body>
    <h1>Reporte de Asistencia</h1>
    
    <div class="info">
        <p><strong>Fecha:</strong> {{ $culto->fecha->format('d/m/Y') }}</p>
        <p><strong>Tipo de Culto:</strong> {{ ucfirst($culto->tipo_culto) }}</p>
        <p><strong>Total Asistencia:</strong> {{ $culto->asistencia->total_asistencia }}</p>
    </div>

    <h3>Capilla</h3>
    <table>
        <tr>
            <th>Adultos Hombres</th>
            <th>Adultos Mujeres</th>
            <th>Adultos Mayores Hombres</th>
            <th>Adultos Mayores Mujeres</th>
            <th>Jóvenes Masculinos</th>
            <th>Jóvenes Femeninas</th>
            <th>Maestros Hombres</th>
        </tr>
        <tr>
            <td>{{ $culto->asistencia->chapel_adultos_hombres }}</td>
            <td>{{ $culto->asistencia->chapel_adultos_mujeres }}</td>
            <td>{{ $culto->asistencia->chapel_adultos_mayores_hombres }}</td>
            <td>{{ $culto->asistencia->chapel_adultos_mayores_mujeres }}</td>
            <td>{{ $culto->asistencia->chapel_jovenes_masculinos }}</td>
            <td>{{ $culto->asistencia->chapel_jovenes_femeninas }}</td>
            <td>{{ $culto->asistencia->chapel_maestros_hombres }}</td>
        </tr>
    </table>

    <h3>Clases</h3>
    <table>
        <thead>
            <tr>
                <th>Clase</th>
                <th>Niños</th>
                <th>Niñas</th>
                <th>Maestros (H)</th>
                <th>Maestras (M)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>0-1 Años</td>
                <td>{{ $culto->asistencia->clase_0_1_hombres }}</td>
                <td>{{ $culto->asistencia->clase_0_1_mujeres }}</td>
                <td>{{ $culto->asistencia->clase_0_1_maestros_hombres }}</td>
                <td>{{ $culto->asistencia->clase_0_1_maestros_mujeres }}</td>
            </tr>
            <tr>
                <td>2-6 Años</td>
                <td>{{ $culto->asistencia->clase_2_6_hombres }}</td>
                <td>{{ $culto->asistencia->clase_2_6_mujeres }}</td>
                <td>{{ $culto->asistencia->clase_2_6_maestros_hombres }}</td>
                <td>{{ $culto->asistencia->clase_2_6_maestros_mujeres }}</td>
            </tr>
            <tr>
                <td>7-8 Años</td>
                <td>{{ $culto->asistencia->clase_7_8_hombres }}</td>
                <td>{{ $culto->asistencia->clase_7_8_mujeres }}</td>
                <td>{{ $culto->asistencia->clase_7_8_maestros_hombres }}</td>
                <td>{{ $culto->asistencia->clase_7_8_maestros_mujeres }}</td>
            </tr>
            <tr>
                <td>9-11 Años</td>
                <td>{{ $culto->asistencia->clase_9_11_hombres }}</td>
                <td>{{ $culto->asistencia->clase_9_11_mujeres }}</td>
                <td>{{ $culto->asistencia->clase_9_11_maestros_hombres }}</td>
                <td>{{ $culto->asistencia->clase_9_11_maestros_mujeres }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
