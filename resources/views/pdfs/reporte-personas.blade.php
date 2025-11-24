<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Personas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #7c3aed;
            padding-bottom: 15px;
        }
        .header img {
            width: 60px;
            height: 60px;
            margin-right: 15px;
        }
        .header-text {
            flex: 1;
        }
        .header-text h1 {
            margin: 0;
            color: #7c3aed;
            font-size: 24px;
        }
        .header-text h2 {
            margin: 5px 0 0 0;
            color: #6b7280;
            font-size: 14px;
            font-weight: normal;
        }
        .periodo {
            background: linear-gradient(135deg, #f3e8ff 0%, #fce7f3 100%);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #e9d5ff;
        }
        .periodo p {
            margin: 0;
            color: #6b21a8;
            font-weight: bold;
            font-size: 12px;
        }
        .persona-card {
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .persona-header {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12px;
        }
        .persona-body {
            padding: 10px;
            background: #f9fafb;
        }
        .promesas-section {
            background: #fef3c7;
            border: 2px solid #fbbf24;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .promesas-section h4 {
            margin: 0 0 8px 0;
            color: #92400e;
            font-size: 11px;
            font-weight: bold;
        }
        .promesa-item {
            display: flex;
            justify-content: space-between;
            padding: 5px;
            background: white;
            border-radius: 4px;
            margin-bottom: 5px;
            font-size: 9px;
        }
        .promesa-item.cumple {
            border-left: 4px solid #10b981;
        }
        .promesa-item.no-cumple {
            border-left: 4px solid #ef4444;
        }
        .cumplimiento-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }
        .cumplimiento-badge.excelente {
            background: #d1fae5;
            color: #065f46;
        }
        .cumplimiento-badge.bueno {
            background: #fef3c7;
            color: #92400e;
        }
        .cumplimiento-badge.regular {
            background: #fee2e2;
            color: #991b1b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table th {
            background: #f3f4f6;
            padding: 6px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
        }
        table td {
            padding: 5px 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-persona {
            background: #ede9fe;
            padding: 6px;
            font-weight: bold;
            color: #6b21a8;
            text-align: right;
            border-radius: 4px;
            font-size: 11px;
        }
        .resumen-final {
            margin-top: 20px;
            padding: 15px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 8px;
            border: 2px solid #86efac;
            page-break-inside: avoid;
        }
        .resumen-final h3 {
            margin: 0 0 10px 0;
            color: #166534;
            font-size: 14px;
        }
        .resumen-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }
        .resumen-item {
            background: white;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #bbf7d0;
        }
        .resumen-item label {
            display: block;
            font-size: 9px;
            color: #166534;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .resumen-item value {
            display: block;
            font-size: 12px;
            color: #15803d;
            font-weight: bold;
        }
        .total-general {
            background: #166534;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .no-sobres {
            color: #9ca3af;
            font-style: italic;
            text-align: center;
            padding: 10px;
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

    @forelse($personas as $persona)
    <div class="persona-card">
        <div class="persona-header">
            {{ $persona->nombre }}
            @if($persona->telefono)
                | {{ $persona->telefono }}
            @endif
            @if($persona->correo)
                | {{ $persona->correo }}
            @endif
            @if(count($persona->promesas_periodo) > 0)
                @php
                    $cumplimiento = $persona->cumplimiento_global;
                    $badgeClass = $cumplimiento >= 100 ? 'excelente' : ($cumplimiento >= 75 ? 'bueno' : 'regular');
                @endphp
                | <span class="cumplimiento-badge {{ $badgeClass }}">{{ number_format($cumplimiento, 1) }}% Cumplimiento</span>
            @endif
        </div>
        <div class="persona-body">
            @if(count($persona->promesas_periodo) > 0)
                <div class="promesas-section">
                    <h4>Promesas del Período ({{ $mesesEnPeriodo }} {{ $mesesEnPeriodo == 1 ? 'mes' : 'meses' }})</h4>
                    @foreach($persona->promesas_periodo as $categoria => $datos)
                        <div class="promesa-item {{ $datos['cumple'] ? 'cumple' : 'no-cumple' }}">
                            <div>
                                <strong>{{ $categoria }}</strong><br>
                                Esperado: ${{ number_format($datos['esperado'], 2) }} | 
                                Dado: ${{ number_format($datos['dado'], 2) }}
                            </div>
                            <div style="text-align: right;">
                                @if($datos['cumple'])
                                    <span style="color: #10b981; font-weight: bold;">✓ CUMPLE</span>
                                @else
                                    <span style="color: #ef4444; font-weight: bold;">✗ FALTA: ${{ number_format(abs($datos['diferencia']), 2) }}</span>
                                @endif
                                <br>
                                <span style="color: #6b7280;">{{ number_format($datos['porcentaje'], 1) }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($persona->sobres->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width: 20%;">Fecha</th>
                            <th style="width: 15%;">Culto</th>
                            <th style="width: 30%;">Categoría</th>
                            <th style="width: 20%;" class="text-right">Monto</th>
                            <th style="width: 15%;" class="text-center">Sobres</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalPersona = 0;
                            $detallesAgrupados = [];
                            
                            // Agrupar detalles por fecha y culto
                            foreach ($persona->sobres as $sobre) {
                                $fecha = $sobre->created_at->format('d/m/Y');
                                $culto = $sobre->culto ? $sobre->culto->tipo : 'Sin culto';
                                $key = $fecha . '|' . $culto;
                                
                                if (!isset($detallesAgrupados[$key])) {
                                    $detallesAgrupados[$key] = [
                                        'fecha' => $fecha,
                                        'culto' => $culto,
                                        'categorias' => []
                                    ];
                                }
                                
                                foreach ($sobre->detalles as $detalle) {
                                    if (!isset($detallesAgrupados[$key]['categorias'][$detalle->categoria])) {
                                        $detallesAgrupados[$key]['categorias'][$detalle->categoria] = 0;
                                    }
                                    $detallesAgrupados[$key]['categorias'][$detalle->categoria] += $detalle->monto;
                                    $totalPersona += $detalle->monto;
                                }
                            }
                        @endphp
                        
                        @foreach($detallesAgrupados as $grupo)
                            @php
                                $primeraCategoria = true;
                                $categoriasCount = count($grupo['categorias']);
                            @endphp
                            @foreach($grupo['categorias'] as $categoria => $monto)
                                <tr>
                                    @if($primeraCategoria)
                                        <td rowspan="{{ $categoriasCount }}">{{ $grupo['fecha'] }}</td>
                                        <td rowspan="{{ $categoriasCount }}">{{ $grupo['culto'] }}</td>
                                    @endif
                                    <td>{{ $categoria }}</td>
                                    <td class="text-right">${{ number_format($monto, 2) }}</td>
                                    @if($primeraCategoria)
                                        <td rowspan="{{ $categoriasCount }}" class="text-center">{{ $categoriasCount }}</td>
                                    @endif
                                </tr>
                                @php $primeraCategoria = false; @endphp
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                <div class="total-persona">
                    Total de {{ $persona->nombre }}: ${{ number_format($totalPersona, 2) }}
                </div>
            @else
                <p class="no-sobres">Sin sobres registrados en este período</p>
            @endif
        </div>
    </div>
    @empty
    <div class="persona-card">
        <div class="persona-body">
            <p class="no-sobres">No hay personas con sobres registrados en este período</p>
        </div>
    </div>
    @endforelse

    @if($personas->count() > 0)
    <div class="resumen-final">
        <h3>Resumen General</h3>
        
        <div class="resumen-grid">
            <div class="resumen-item">
                <label>TOTAL DE PERSONAS</label>
                <value>{{ $personas->count() }}</value>
            </div>
            <div class="resumen-item">
                <label>TOTAL DE SOBRES</label>
                <value>{{ $personas->sum(function($p) { return $p->sobres->count(); }) }}</value>
            </div>
            <div class="resumen-item">
                <label>TOTAL PROMETIDO</label>
                <value>${{ number_format($totalPrometidoGeneral, 2) }}</value>
            </div>
            <div class="resumen-item">
                <label>TOTAL RECIBIDO</label>
                <value>${{ number_format($totalGeneral, 2) }}</value>
            </div>
        </div>

        @if(count($totalesPorCategoria) > 0)
        <div style="margin: 15px 0;">
            <label style="display: block; font-size: 10px; color: #166534; font-weight: bold; margin-bottom: 8px;">
                TOTALES POR CATEGORÍA:
            </label>
            <div class="resumen-grid">
                @foreach($totalesPorCategoria as $categoria => $total)
                <div class="resumen-item">
                    <label>{{ strtoupper($categoria) }}</label>
                    <value>${{ number_format($total, 2) }}</value>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="total-general">
            TOTAL GENERAL: ${{ number_format($totalGeneral, 2) }}
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Generado el {{ \Carbon\Carbon::now()->locale('es')->translatedFormat('d \d\e F \d\e Y \a \l\a\s h:i A') }}</p>
        <p>IBBSC - Sistema de Administración</p>
    </div>
</body>
</html>
