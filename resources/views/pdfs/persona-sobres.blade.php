<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sobres de Persona</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 0; }
        .header { display: flex; align-items: center; margin-bottom: 16px; border-bottom: 2px solid #3b82f6; padding-bottom: 8px; }
        .header img { width: 50px; height: 50px; margin-right: 12px; }
        .header-text h1 { margin: 0; font-size: 18px; color: #1f2937; }
        .header-text p { margin: 2px 0 0 0; color: #6b7280; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; font-size: 10px; }
        th { background-color: #3b82f6; color: white; text-transform: uppercase; font-weight: bold; }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        .total-row { font-weight: bold; background-color: #dbeafe !important; }
    </style>
</head>
<body>
    <div class="header">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Logo2.png'))) }}" alt="Logo IBBP">
        <div class="header-text">
            <h1>Sobres de {{ $persona->nombre }}</h1>
            <p>Período: {{ $periodo }} • Generado: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha Culto</th>
                <th>N° Sobre</th>
                <th>Método</th>
                <th>Detalle</th>
                <th>Total Declarado</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($sobres as $sobre)
            <tr>
                <td>{{ optional($sobre->culto->fecha)->format('d/m/Y') }}</td>
                <td>#{{ $sobre->numero_sobre }}</td>
                <td>{{ ucfirst($sobre->metodo_pago) }}</td>
                <td>
                    @php
                        $labels = [
                            'diezmo' => 'Diezmo',
                            'misiones' => 'Misiones',
                            'seminario' => 'Seminario',
                            'campamento' => 'Campamento',
                            'pro-templo' => 'Pro-Templo',
                            'ofrenda especial' => 'Ofrenda Especial',
                        ];
                        $rubros = array_keys($labels);
                        $montos = array_fill_keys($rubros, 0);

                        foreach ($sobre->detalles as $detalle) {
                            $catRaw = strtolower(trim($detalle->categoria));
                            $catNorm = str_replace(['_', '  '], [' ', ' '], $catRaw);
                            if (in_array($catNorm, ['ofrenda especial', 'ofrenda_especial'])) { $catNorm = 'ofrenda especial'; }
                            if (in_array($catNorm, ['pro-templo', 'pro templo', 'protemplo'])) { $catNorm = 'pro-templo'; }
                            if (isset($montos[$catNorm])) { $montos[$catNorm] = $detalle->monto; }
                        }
                    @endphp
                    @foreach($rubros as $i => $rubro)
                        {{ $labels[$rubro] }}: {{ number_format($montos[$rubro], 2) }}@if($i < count($rubros) - 1), @endif
                    @endforeach
                </td>
                <td>{{ number_format($sobre->total_declarado, 2) }}</td>
            </tr>
            @php $total += $sobre->total_declarado; @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="4">TOTAL</td>
                <td>{{ number_format($total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
