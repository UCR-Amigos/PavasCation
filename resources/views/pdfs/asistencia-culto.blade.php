<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Asistencia - {{ $culto->fecha->format('d/m/Y') }}</title>
    <style>
        @page { size: landscape; margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 0; }
        .header { display: flex; align-items: center; margin-bottom: 15px; border-bottom: 3px solid #3b82f6; padding-bottom: 10px; }
        .header img { width: 60px; height: 60px; margin-right: 15px; }
        .header-text { flex: 1; }
        .header-text h1 { margin: 0; color: #1f2937; font-size: 18px; }
        .header-text h2 { margin: 5px 0 0 0; color: #3b82f6; font-size: 12px; font-weight: normal; }
        .info { background-color: #f3f4f6; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .info p { margin: 5px 0; font-size: 10px; }
        h3 { color: #1f2937; font-size: 13px; margin-top: 15px; margin-bottom: 8px; border-bottom: 2px solid #3b82f6; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; font-size: 10px; }
        th { background-color: #3b82f6; color: white; text-align: left; font-weight: bold; }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        .total { font-weight: bold; background-color: #dbeafe; text-align: center; }
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Logo2.png'))) }}" alt="Logo IBBSC">
        <div class="header-text">
            <h1>IBBSC - Iglesia Bíblica Bautista en Santa Cruz</h1>
            <h2>Reporte de Asistencia</h2>
        </div>
    </div>
    
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
    
    <div class="footer">
        <p>Sistema de Administración - IBBSC - Iglesia Bíblica Bautista en Santa Cruz</p>
    </div>
</body>
</html>
