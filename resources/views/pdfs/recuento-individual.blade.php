<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recuento Individual - {{ $culto->fecha->format('d/m/Y') }}</title>
    <style>
        @page { size: landscape; margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 0; }
        .header { display: flex; align-items: center; margin-bottom: 15px; border-bottom: 3px solid #3b82f6; padding-bottom: 10px; }
        .header img { width: 60px; height: 60px; margin-right: 15px; }
        .header-text { flex: 1; }
        .header-text h1 { margin: 0; color: #1f2937; font-size: 18px; }
        .header-text h2 { margin: 5px 0 0 0; color: #3b82f6; font-size: 12px; font-weight: normal; }
        .info-box { background-color: #f3f4f6; padding: 8px; border-radius: 5px; margin-bottom: 12px; font-size: 9px; }
        .info-box p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #3b82f6; color: white; font-size: 9px; text-transform: uppercase; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .sobre-row { background-color: #ffffff; }
        .sobre-row:hover { background-color: #f9fafb; }
        .suelto-row { background-color: #dcfce7; }
        .egreso-row { background-color: #fee2e2; }
        .total-row { font-weight: bold; background-color: #dbeafe; font-size: 11px; }
        .subtotal { font-weight: bold; color: #1e40af; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; }
        .badge-efectivo { background-color: #d1fae5; color: #065f46; }
        .badge-transferencia { background-color: #dbeafe; color: #1e40af; }
        
        /* Resumen por categorías */
        .resumen-box { margin-top: 20px; padding: 10px; background-color: #fef3c7; border-radius: 5px; }
        .resumen-box h3 { margin: 0 0 10px 0; font-size: 11px; color: #92400e; }
        .categoria-item { display: inline-block; margin: 5px 10px 5px 0; font-size: 9px; }
        .categoria-label { font-weight: bold; }
        
        /* Summary cards */
        .summary-grid { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .summary-card { flex: 1; margin: 0 5px; padding: 10px; border: 1px solid #e5e7eb; border-radius: 5px; text-align: center; }
        .summary-card .label { font-size: 9px; color: #6b7280; text-transform: uppercase; }
        .summary-card .value { font-size: 14px; font-weight: bold; color: #1f2937; margin-top: 5px; }
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Logo2.png'))) }}" alt="Logo IBBP">
        <div class="header-text">
            <h1>IBBP - Iglesia Bíblica Bautista en Santa Cruz</h1>
            <h2>Recuento de Sobres - {{ $culto->fecha->format('d/m/Y') }} - {{ ucfirst($culto->tipo_culto) }}</h2>
        </div>
    </div>
    
    <div class="info-box">
        <p><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        @if($culto->cerrado)
        <p><strong>Estado:</strong> Cerrado el {{ $culto->cerrado_at->format('d/m/Y H:i') }}</p>
        @endif
    </div>

    @php $transferenciasOnly = isset($transferenciasOnly) && $transferenciasOnly === true; @endphp
    @if($culto->totales)
        @if(!$transferenciasOnly)
        <div class="summary-grid">
            <div class="summary-card">
                <div class="label">Total General</div>
                <div class="value">{{ number_format($culto->totales->total_general, 2) }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Cantidad Sobres</div>
                <div class="value">{{ $culto->totales->cantidad_sobres }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Diezmos</div>
                <div class="value">{{ number_format($culto->totales->total_diezmo, 2) }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Transferencias</div>
                <div class="value">{{ $culto->totales->cantidad_transferencias }}</div>
            </div>
        </div>
        @else
        <div class="summary-grid">
            <div class="summary-card">
                <div class="label">Total Transferencias</div>
                <div class="value">{{ number_format($culto->totales->total_transferencias ?? ($culto->sobres->where('metodo_pago','transferencia')->sum('total_declarado')), 2) }}</div>
            </div>
        </div>
        @endif
    @endif

    <!-- Tabla de Sobres -->
    <h3 style="margin-top: 20px; margin-bottom: 10px; font-size: 11px;">Detalle de Sobres{{ $transferenciasOnly ? ' (solo transferencias)' : ' y Dinero Suelto' }}</h3>
    <table>
        <thead>
            <tr>
                @if(!$transferenciasOnly)
                <th>N° Sobre</th>
                @endif
                <th>Persona</th>
                <th>Método</th>
                <th>Comprobante</th>
                <th>Notas</th>
                <th class="text-right">Diezmo</th>
                <th class="text-right">Ofr. Esp.</th>
                <th class="text-right">Misiones</th>
                <th class="text-right">Semin.</th>
                <th class="text-right">Camp.</th>
                <th class="text-right">Pro-T.</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalGeneral = 0;
                $totalEfectivo = 0;
                $totalTransferencias = 0;
            @endphp
            
            @foreach($culto->sobres->filter(fn($s) => !$transferenciasOnly || $s->metodo_pago === 'transferencia') as $sobre)
            @php
                $mapeoCategoria = ['campa' => 'campamento', 'prestamo' => 'pro_templo', 'pro-templo' => 'pro_templo', 'ofrenda-especial' => 'ofrenda_especial'];
                $detallesPorCategoria = $sobre->detalles->keyBy(fn($d) => $mapeoCategoria[strtolower($d->categoria)] ?? strtolower($d->categoria));
                $diezmo = $detallesPorCategoria->get('diezmo')->monto ?? 0;
                $ofrendaEspecial = $detallesPorCategoria->get('ofrenda_especial')->monto ?? 0;
                $misiones = $detallesPorCategoria->get('misiones')->monto ?? 0;
                $seminario = $detallesPorCategoria->get('seminario')->monto ?? 0;
                $campamento = $detallesPorCategoria->get('campamento')->monto ?? 0;
                $pro_templo = $detallesPorCategoria->get('pro_templo')->monto ?? 0;
                $totalGeneral += $sobre->total_declarado;
                if ($sobre->metodo_pago === 'transferencia') { $totalTransferencias += $sobre->total_declarado; } else { $totalEfectivo += $sobre->total_declarado; }
            @endphp
            <tr class="sobre-row">
                @if(!$transferenciasOnly)
                <td class="text-center"><strong>#{{ $sobre->numero_sobre }}</strong></td>
                @endif
                <td>{{ $sobre->persona ? $sobre->persona->nombre : 'Anónimo' }}</td>
                <td>
                    <span class="badge {{ $sobre->metodo_pago == 'transferencia' ? 'badge-transferencia' : 'badge-efectivo' }}">
                        {{ ucfirst($sobre->metodo_pago) }}
                    </span>
                </td>
                <td>{{ $sobre->metodo_pago === 'transferencia' ? ($sobre->comprobante_numero ?? '-') : '-' }}</td>
                <td>{{ $sobre->notas ? $sobre->notas : '-' }}</td>
                <td class="text-right">{{ number_format($diezmo, 2) }}</td>
                <td class="text-right">{{ number_format($ofrendaEspecial, 2) }}</td>
                <td class="text-right">{{ number_format($misiones, 2) }}</td>
                <td class="text-right">{{ number_format($seminario, 2) }}</td>
                <td class="text-right">{{ number_format($campamento, 2) }}</td>
                <td class="text-right">{{ number_format($pro_templo, 2) }}</td>
                <td class="text-right subtotal">{{ number_format($sobre->total_declarado, 2) }}</td>
            </tr>
            @endforeach

            @if(!$transferenciasOnly)
                @foreach($culto->ofrendasSueltas as $ofrenda)
                @php $totalGeneral += $ofrenda->monto; @endphp
                <tr class="suelto-row">
                    <td class="text-center">-</td>
                    <td>
                        <strong>Dinero Suelto</strong>
                        @if($ofrenda->descripcion)
                        <br><small style="font-size: 8px; color: #6b7280;">{{ $ofrenda->descripcion }}</small>
                        @endif
                    </td>
                    <td>Efectivo</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right subtotal">{{ number_format($ofrenda->monto, 2) }}</td>
                </tr>
                @endforeach
                @foreach($culto->egresos as $egreso)
                @php $totalGeneral -= $egreso->monto; @endphp
                <tr class="egreso-row">
                    <td class="text-center">-</td>
                    <td>
                        <strong>Egreso</strong>
                        @if($egreso->descripcion)
                        <br><small style="font-size: 8px; color: #6b7280;">{{ $egreso->descripcion }}</small>
                        @endif
                    </td>
                    <td>Efectivo</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right subtotal">-{{ number_format($egreso->monto, 2) }}</td>
                </tr>
                @endforeach
            @endif

            <!-- Totales -->
            <tr class="total-row">
                <td colspan="{{ $transferenciasOnly ? 4 : 5 }}" class="text-right">TOTALES</td>
                <td class="text-right">{{ number_format($totalesPorCategoria['diezmo'], 2) }}</td>
                <td class="text-right">{{ number_format($totalesPorCategoria['ofrenda_especial'], 2) }}</td>
                <td class="text-right">{{ number_format($totalesPorCategoria['misiones'], 2) }}</td>
                <td class="text-right">{{ number_format($totalesPorCategoria['seminario'], 2) }}</td>
                <td class="text-right">{{ number_format($totalesPorCategoria['campamento'], 2) }}</td>
                <td class="text-right">{{ number_format($totalesPorCategoria['pro_templo'], 2) }}</td>
                <td class="text-right">{{ number_format($totalGeneral, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if(!$transferenciasOnly)
    <div class="summary-grid" style="margin-top: 10px;">
        <div class="summary-card">
            <div class="label">Total Efectivo</div>
            <div class="value">{{ number_format($totalEfectivo, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Transferencias</div>
            <div class="value">{{ number_format($totalTransferencias, 2) }}</div>
        </div>
    </div>
    @endif

    <!-- Resumen por Categorías -->
    <div class="resumen-box">
        <h3>Resumen por Categorías</h3>
        <div>
            <span class="categoria-item">
                <span class="categoria-label">Diezmo:</span> {{ number_format($totalesPorCategoria['diezmo'], 2) }}
            </span>
            <span class="categoria-item">
                <span class="categoria-label">Ofrenda Especial:</span> {{ number_format($totalesPorCategoria['ofrenda_especial'], 2) }}
            </span>
            <span class="categoria-item">
                <span class="categoria-label">Misiones:</span> {{ number_format($totalesPorCategoria['misiones'], 2) }}
            </span>
            <span class="categoria-item">
                <span class="categoria-label">Seminario:</span> {{ number_format($totalesPorCategoria['seminario'], 2) }}
            </span>
            <span class="categoria-item">
                <span class="categoria-label">Campamento:</span> {{ number_format($totalesPorCategoria['campamento'], 2) }}
            </span>
            <span class="categoria-item">
                <span class="categoria-label">Pro-Templo:</span> {{ number_format($totalesPorCategoria['pro_templo'], 2) }}
            </span>
        </div>
    </div>

    <!-- Firmas -->
    @php
        $pastor = $culto->firma_pastor ?? '';
        $pastorImagen = $culto->firma_pastor_imagen ?? '';
        $tesorerosImagenes = $culto->firmas_tesoreros_imagenes ?? [];
        // Fallback a nombres antiguos si no hay imágenes
        if (empty($tesorerosImagenes)) {
            $tes = $culto->firmas_tesoreros ?? [];
            $tesorerosImagenes = array_map(fn($n) => ['nombre' => $n, 'imagen' => ''], $tes);
        }
    @endphp
    <div style="margin-top:30px; page-break-inside: avoid;">
        <h3 style="text-align:center; font-size:11px; margin-bottom:15px; color:#374151;">FIRMAS DE AUTORIZACIÓN</h3>
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                @foreach($tesorerosImagenes as $t)
                <td style="width:{{ 100 / (count($tesorerosImagenes) + 1) }}%; text-align:center; vertical-align:bottom; padding:10px;">
                    <div style="min-height:60px; display:flex; align-items:flex-end; justify-content:center;">
                        @if(!empty($t['imagen']))
                            <img src="{{ $t['imagen'] }}" style="max-height:50px; max-width:120px;">
                        @endif
                    </div>
                    <div style="border-top:1px solid #000; padding-top:8px; margin-top:5px;">
                        <div style="font-size:10px; font-weight:bold;">{{ $t['nombre'] ?? 'Tesorero' }}</div>
                        <div style="font-size:8px; color:#6b7280;">Tesorero</div>
                    </div>
                </td>
                @endforeach
                <td style="width:{{ 100 / (count($tesorerosImagenes) + 1) }}%; text-align:center; vertical-align:bottom; padding:10px;">
                    <div style="min-height:60px; display:flex; align-items:flex-end; justify-content:center;">
                        @if(!empty($pastorImagen))
                            <img src="{{ $pastorImagen }}" style="max-height:50px; max-width:120px;">
                        @endif
                    </div>
                    <div style="border-top:1px solid #000; padding-top:8px; margin-top:5px;">
                        <div style="font-size:10px; font-weight:bold;">{{ $pastor ?: 'Pastor' }}</div>
                        <div style="font-size:8px; color:#6b7280;">Pastor</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Sistema de Administración - IBBP - Iglesia Bíblica Bautista en Santa Cruz</p>
    </div>
</body>
</html>
