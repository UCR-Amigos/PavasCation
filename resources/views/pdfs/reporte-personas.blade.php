<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Personas</title>
    <style>
        @page { size: landscape; margin: 15mm; }
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 15px;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #7c3aed;
            padding-bottom: 10px;
        }
        .header img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }
        .header-text {
            flex: 1;
        }
        .header-text h1 {
            margin: 0;
            color: #7c3aed;
            font-size: 18px;
        }
        .header-text h2 {
            margin: 3px 0 0 0;
            color: #6b7280;
            font-size: 11px;
            font-weight: normal;
        }
        .periodo {
            background: linear-gradient(135deg, #f3e8ff 0%, #fce7f3 100%);
            padding: 8px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            border: 1px solid #e9d5ff;
        }
        .periodo p {
            margin: 0;
            color: #6b21a8;
            font-weight: bold;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8px;
        }
        table th {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: white;
            padding: 8px 4px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #6b21a8;
            font-size: 8px;
        }
        table td {
            padding: 6px 4px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .nombre-col {
            text-align: left;
            font-weight: bold;
            color: #1f2937;
        }
        .cumple {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: bold;
        }
        .no-cumple {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: bold;
        }
        .total-row {
            background: #ede9fe !important;
            font-weight: bold;
            color: #6b21a8;
        }
        .resumen-final {
            margin-top: 15px;
            padding: 10px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 6px;
            border: 2px solid #86efac;
        }
        .resumen-final h3 {
            margin: 0 0 8px 0;
            color: #166534;
            font-size: 12px;
        }
        .resumen-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }
        .resumen-item {
            background: white;
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #bbf7d0;
            text-align: center;
        }
        .resumen-item label {
            display: block;
            font-size: 8px;
            color: #166534;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .resumen-item value {
            display: block;
            font-size: 10px;
            color: #15803d;
            font-weight: bold;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 7px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Logo2.png'))) }}" alt="Logo IBBSC">
        <div class="header-text">
            <h1>Reporte de Personas</h1>
            <h2>Iglesia Bautista Bíblica Shekinah de la Ciudad</h2>
        </div>
    </div>

    <div class="periodo">
        <p>Período: {{ $tituloPeriodo }}</p>
    </div>

    @if($personas->count() > 0)
        @php
            // Obtener todas las categorías únicas
            $categorias = [];
            foreach ($personas as $persona) {
                foreach ($persona->promesas_periodo as $categoria => $datos) {
                    if (!in_array($categoria, $categorias)) {
                        $categorias[] = $categoria;
                    }
                }
            }
            sort($categorias);
        @endphp

        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width: 12%;">NOMBRE</th>
                    @foreach($categorias as $categoria)
                        <th colspan="2">{{ strtoupper($categoria) }}</th>
                    @endforeach
                    <th rowspan="2" style="width: 7%;">TOTAL<br>ESPERADO</th>
                    <th rowspan="2" style="width: 7%;">TOTAL<br>DADO</th>
                    <th rowspan="2" style="width: 7%;">DIFERENCIA</th>
                    <th rowspan="2" style="width: 5%;">%</th>
                </tr>
                <tr>
                    @foreach($categorias as $categoria)
                        <th style="font-size: 7px; padding: 4px 2px;">Esperado</th>
                        <th style="font-size: 7px; padding: 4px 2px;">Dado</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $totalesEsperado = array_fill_keys($categorias, 0);
                    $totalesDado = array_fill_keys($categorias, 0);
                    $granTotalEsperado = 0;
                    $granTotalDado = 0;
                @endphp

                @foreach($personas as $persona)
                    @php
                        $totalPersonaEsperado = 0;
                        $totalPersonaDado = 0;
                    @endphp
                    <tr>
                        <td class="nombre-col">{{ $persona->nombre }}</td>
                        @foreach($categorias as $categoria)
                            @php
                                $datos = $persona->promesas_periodo[$categoria] ?? ['esperado' => 0, 'dado' => 0];
                                $esperado = $datos['esperado'];
                                $dado = $datos['dado'];
                                $totalPersonaEsperado += $esperado;
                                $totalPersonaDado += $dado;
                                $totalesEsperado[$categoria] += $esperado;
                                $totalesDado[$categoria] += $dado;
                                
                                $cumple = $dado >= $esperado && $esperado > 0;
                            @endphp
                            <td class="{{ $esperado > 0 ? ($cumple ? 'cumple' : 'no-cumple') : '' }}">
                                ${{ number_format($esperado, 2) }}
                            </td>
                            <td class="{{ $esperado > 0 ? ($cumple ? 'cumple' : 'no-cumple') : '' }}">
                                ${{ number_format($dado, 2) }}
                            </td>
                        @endforeach
                        @php
                            $granTotalEsperado += $totalPersonaEsperado;
                            $granTotalDado += $totalPersonaDado;
                            $diferencia = $totalPersonaDado - $totalPersonaEsperado;
                            $porcentaje = $totalPersonaEsperado > 0 ? ($totalPersonaDado / $totalPersonaEsperado * 100) : 0;
                        @endphp
                        <td style="font-weight: bold;">${{ number_format($totalPersonaEsperado, 2) }}</td>
                        <td style="font-weight: bold;">${{ number_format($totalPersonaDado, 2) }}</td>
                        <td class="{{ $diferencia >= 0 ? 'cumple' : 'no-cumple' }}" style="font-weight: bold;">
                            ${{ number_format($diferencia, 2) }}
                        </td>
                        <td class="{{ $porcentaje >= 100 ? 'cumple' : ($porcentaje >= 75 ? '' : 'no-cumple') }}" style="font-weight: bold;">
                            {{ number_format($porcentaje, 0) }}%
                        </td>
                    </tr>
                @endforeach

                <!-- Fila de Totales -->
                <tr class="total-row">
                    <td>TOTALES</td>
                    @foreach($categorias as $categoria)
                        <td>${{ number_format($totalesEsperado[$categoria], 2) }}</td>
                        <td>${{ number_format($totalesDado[$categoria], 2) }}</td>
                    @endforeach
                    <td>${{ number_format($granTotalEsperado, 2) }}</td>
                    <td>${{ number_format($granTotalDado, 2) }}</td>
                    @php
                        $diferenciaTotal = $granTotalDado - $granTotalEsperado;
                        $porcentajeTotal = $granTotalEsperado > 0 ? ($granTotalDado / $granTotalEsperado * 100) : 0;
                    @endphp
                    <td>${{ number_format($diferenciaTotal, 2) }}</td>
                    <td>{{ number_format($porcentajeTotal, 0) }}%</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="no-data">
            No hay personas con promesas registradas en este período
        </div>
    @endif

    @if($personas->count() > 0)
    <div class="resumen-final">
        <h3>Resumen General</h3>
        
        @php
            // Calcular el total dado desde las promesas_periodo
            $totalDadoReal = 0;
            foreach ($personas as $p) {
                foreach ($p->promesas_periodo as $cat => $datos) {
                    $totalDadoReal += $datos['dado'];
                }
            }
        @endphp
        
        <div class="resumen-grid">
            <div class="resumen-item">
                <label>PERSONAS</label>
                <value>{{ $personas->count() }}</value>
            </div>
            <div class="resumen-item">
                <label>TOTAL ESPERADO</label>
                <value>${{ number_format($totalPrometidoGeneral, 2) }}</value>
            </div>
            <div class="resumen-item">
                <label>TOTAL RECIBIDO</label>
                <value>${{ number_format($totalDadoReal, 2) }}</value>
            </div>
            <div class="resumen-item">
                <label>CUMPLIMIENTO GLOBAL</label>
                <value>{{ $totalPrometidoGeneral > 0 ? number_format(($totalDadoReal / $totalPrometidoGeneral * 100), 1) : 0 }}%</value>
            </div>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Generado el {{ \Carbon\Carbon::now()->locale('es')->translatedFormat('d \d\e F \d\e Y \a \l\a\s h:i A') }}</p>
        <p>IBBSC - Sistema de Administración</p>
    </div>
</body>
</html>
