<!-- Información del Culto -->
@php
    $isAdmin = auth()->check() && (method_exists(auth()->user(), 'isAdmin') ? auth()->user()->isAdmin() : (auth()->user()->rol ?? null) === 'admin');
@endphp
<div class="bg-gradient-to-r from-gray-100 to-gray-200 rounded-lg p-6 border-l-4 border-gray-500 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h4 class="text-xl font-bold text-gray-800">{{ $culto->fecha->format('d/m/Y') }} - {{ ucfirst($culto->tipo_culto) }}</h4>
            <p class="text-sm text-gray-600 mt-1">
                <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                </svg>
                Cerrado el {{ $culto->cerrado_at->format('d/m/Y \a \l\a\s H:i') }}
                @if($culto->cerradoPor)
                por {{ $culto->cerradoPor->name }}
                @endif
            </p>
        </div>
        @if($culto->totales)
        <div class="text-right">
            <p class="text-sm text-gray-600">Total General</p>
            <p class="text-3xl font-bold text-blue-600">₡{{ number_format($culto->totales->total_general, 2) }}</p>
        </div>
        @endif
    </div>
</div>

<!-- Resumen Estadístico -->
@if($culto->totales)
@php
    // Calcular totales por método de pago
    $sobresEfectivo = $sobres->where('metodo_pago', 'efectivo')->sum('total_declarado');
    $sobresTransferencias = $sobres->where('metodo_pago', 'transferencia')->sum('total_declarado');
    $totalSuelto = $ofrendasSueltas->sum('monto');
    $totalEgresosCerrado = $culto->egresos->sum('monto');
    $totalEfectivoCerrado = $sobresEfectivo + $totalSuelto - $totalEgresosCerrado;
    $totalTransferenciasCerrado = $sobresTransferencias;
@endphp
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
        <p class="text-sm text-gray-600">Total Efectivo</p>
        <p class="text-2xl font-bold text-green-600">₡{{ number_format($totalEfectivoCerrado, 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Sobres + Suelto - Egresos</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
        <p class="text-sm text-gray-600">Total Transferencias</p>
        <p class="text-2xl font-bold text-blue-600">₡{{ number_format($totalTransferenciasCerrado, 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Sobres transferencia</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
        <p class="text-sm text-gray-600">Cantidad de Sobres</p>
        <p class="text-2xl font-bold text-orange-600">{{ $culto->totales->cantidad_sobres }}</p>
    </div>
</div>
@endif

<!-- Tabla Resumen Detallado -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h4 class="text-lg font-semibold text-gray-900">Resumen Detallado por Categorías</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">N° Sobre</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase">Diezmo</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase">Of. Especial</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase">Misiones</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase">Seminario</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase">Camp.</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase">Pro-Templo</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase font-bold">Subtotal</th>
                    @if($isAdmin)
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-700 uppercase">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $totales = [
                        'diezmo' => 0,
                        'ofrenda_especial' => 0,
                        'misiones' => 0,
                        'seminario' => 0,
                        'campamento' => 0,
                        'pro_templo' => 0,
                        'subtotal' => 0
                    ];
                @endphp
                @foreach($sobres as $sobre)
                @php
                    $mapeoCategoria = ['campa' => 'campamento', 'prestamo' => 'pro_templo', 'pro-templo' => 'pro_templo', 'ofrenda-especial' => 'ofrenda_especial'];
                    $detallesPorCategoria = $sobre->detalles->keyBy(fn($d) => $mapeoCategoria[strtolower($d->categoria)] ?? strtolower($d->categoria));
                    $diezmo = $detallesPorCategoria->get('diezmo')->monto ?? 0;
                    $ofrenda_especial = $detallesPorCategoria->get('ofrenda_especial')->monto ?? 0;
                    $misiones = $detallesPorCategoria->get('misiones')->monto ?? 0;
                    $seminario = $detallesPorCategoria->get('seminario')->monto ?? 0;
                    $campamento = $detallesPorCategoria->get('campamento')->monto ?? 0;
                    $pro_templo = $detallesPorCategoria->get('pro_templo')->monto ?? 0;
                    $subtotal = $sobre->total_declarado;

                    $totales['diezmo'] += $diezmo;
                    $totales['ofrenda_especial'] += $ofrenda_especial;
                    $totales['misiones'] += $misiones;
                    $totales['seminario'] += $seminario;
                    $totales['campamento'] += $campamento;
                    $totales['pro_templo'] += $pro_templo;
                    $totales['subtotal'] += $subtotal;
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                        #{{ $sobre->numero_sobre }}
                        @if($isAdmin)
                        <a href="{{ route('recuento.edit', $sobre) }}" class="ml-2 inline-block text-blue-600 hover:text-blue-800 font-medium">Editar</a>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-right text-gray-700">₡{{ number_format($diezmo, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-700">₡{{ number_format($ofrenda_especial, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-700">₡{{ number_format($misiones, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-700">₡{{ number_format($seminario, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-700">₡{{ number_format($campamento, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-700">₡{{ number_format($pro_templo, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-blue-600">₡{{ number_format($subtotal, 2) }}</td>
                    @if($isAdmin)
                    <td class="px-4 py-3 text-sm text-right">
                        <a href="{{ route('recuento.edit', $sobre) }}" class="text-blue-600 hover:text-blue-800 font-medium">Editar</a>
                    </td>
                    @endif
                </tr>
                @endforeach
                
                <!-- Filas de Dinero Suelto -->
                @foreach($ofrendasSueltas as $ofrenda)
                @php
                    $totales['subtotal'] += $ofrenda->monto;
                @endphp
                <tr class="hover:bg-green-50 bg-green-50/30">
                    <td class="px-4 py-3 text-sm">
                        <span class="font-medium text-green-700">Dinero Suelto</span>
                        @if($ofrenda->descripcion)
                        <span class="text-xs text-gray-500 block">{{ $ofrenda->descripcion }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-green-600">₡{{ number_format($ofrenda->monto, 2) }}</td>
                    @if($isAdmin)
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    @endif
                </tr>
                @endforeach

                @foreach($culto->egresos as $egreso)
                @php
                    $totales['subtotal'] -= $egreso->monto;
                @endphp
                <tr class="hover:bg-red-50 bg-red-50/30">
                    <td class="px-4 py-3 text-sm">
                        <span class="font-medium text-red-700">Egreso</span>
                        @if($egreso->descripcion)
                        <span class="text-xs text-gray-500 block">{{ $egreso->descripcion }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-red-600">₡{{ number_format($egreso->monto, 2) }}</td>
                    @if($isAdmin)
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    @endif
                </tr>
                @endforeach

                <!-- Fila de Totales -->
                <tr class="bg-blue-50 border-t-2 border-blue-200">
                    <td class="px-4 py-3 text-sm font-bold text-gray-900">TOTALES</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-blue-700">₡{{ number_format($totales['diezmo'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-blue-700">₡{{ number_format($totales['ofrenda_especial'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-blue-700">₡{{ number_format($totales['misiones'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-blue-700">₡{{ number_format($totales['seminario'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-blue-700">₡{{ number_format($totales['campamento'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-blue-700">₡{{ number_format($totales['pro_templo'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-green-700 text-lg">₡{{ number_format($totales['subtotal'], 2) }}</td>
                    @if($isAdmin)
                    <td class="px-4 py-3 text-sm text-right text-gray-400">-</td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
</div>
