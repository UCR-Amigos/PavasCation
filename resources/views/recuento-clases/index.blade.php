@extends('layouts.admin')

@section('title', 'Pavas - Recuento por Clases')
@section('page-title', 'Recuento por Clases')

@section('content')
<div class="space-y-6">
    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('recuento-clases.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="culto_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccionar Culto
                    </label>
                    <select name="culto_id" id="culto_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            onchange="this.form.submit()">
                        <option value="">-- Seleccione un culto --</option>
                        @foreach($cultos as $culto)
                            <option value="{{ $culto->id }}" {{ request('culto_id') == $culto->id ? 'selected' : '' }}>
                                {{ $culto->fecha->format('d/m/Y') }} - {{ ucfirst($culto->tipo_culto) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="clase_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccionar Clase
                    </label>
                    <select name="clase_id" id="clase_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            onchange="this.form.submit()" {{ !$cultoSeleccionado ? 'disabled' : '' }}>
                        <option value="">-- Seleccione una clase --</option>
                        @foreach($clases as $clase)
                            <option value="{{ $clase->id }}"
                                    {{ request('clase_id') == $clase->id ? 'selected' : '' }}
                                    data-color="{{ $clase->color }}">
                                {{ $clase->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    @if($cultoSeleccionado && $claseSeleccionada)
    <!-- Resumen de la Clase -->
    <div class="bg-white rounded-lg shadow p-6" style="border-left: 4px solid {{ $claseSeleccionada->color }};">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full" style="background-color: {{ $claseSeleccionada->color }};"></div>
                <h2 class="text-xl font-bold text-gray-800">Clase: {{ $claseSeleccionada->nombre }}</h2>
            </div>
            <div class="text-sm text-gray-600">
                {{ $cultoSeleccionado->fecha->format('d/m/Y') }} - {{ ucfirst($cultoSeleccionado->tipo_culto) }}
            </div>
        </div>

        @if($resumen)
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Total</p>
                <p class="text-2xl font-bold text-blue-600">₡{{ number_format($resumen['total'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Sobres</p>
                <p class="text-2xl font-bold text-green-600">{{ $resumen['cantidad_sobres'] }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Efectivo</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $resumen['efectivo'] }}</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Transferencias</p>
                <p class="text-2xl font-bold text-purple-600">{{ $resumen['transferencias'] }}</p>
            </div>
        </div>
        @endif

        <!-- Botones de acción -->
        <div class="flex flex-wrap gap-3 mb-6">
            @if(!$cultoSeleccionado->cerrado)
            <a href="{{ route('recuento-clases.create', ['culto_id' => $cultoSeleccionado->id, 'clase_id' => $claseSeleccionada->id]) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Agregar Sobre
            </a>
            @endif
            @if($sobres->count() > 0)
            <a href="{{ route('recuento-clases.pdf', [$cultoSeleccionado->id, $claseSeleccionada->id]) }}"
               class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                PDF de esta Clase
            </a>
            @endif
            @if($cultoSeleccionado->cerrado)
            <div class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md border border-gray-300 flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                </svg>
                Culto Cerrado
            </div>
            @endif
        </div>

        <!-- Tabla de Sobres -->
        @if($sobres->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persona</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Diezmo</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ofr. Esp.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Misiones</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Semin.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Camp.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Prést.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Const.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Micro</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $totales = [
                            'diezmo' => 0,
                            'ofrenda_especial' => 0,
                            'misiones' => 0,
                            'seminario' => 0,
                            'campa' => 0,
                            'prestamo' => 0,
                            'construccion' => 0,
                            'micro' => 0,
                        ];
                        $totalGeneral = 0;
                    @endphp
                    @foreach($sobres as $sobre)
                    @php
                        $detallesPorCategoria = $sobre->detalles->keyBy('categoria');
                        $diezmo = $detallesPorCategoria->get('diezmo')->monto ?? 0;
                        $ofrendaEspecial = $detallesPorCategoria->get('ofrenda_especial')->monto ?? 0;
                        $misiones = $detallesPorCategoria->get('misiones')->monto ?? 0;
                        $seminario = $detallesPorCategoria->get('seminario')->monto ?? 0;
                        $campa = $detallesPorCategoria->get('campa')->monto ?? 0;
                        $prestamo = $detallesPorCategoria->get('prestamo')->monto ?? 0;
                        $construccion = $detallesPorCategoria->get('construccion')->monto ?? 0;
                        $micro = $detallesPorCategoria->get('micro')->monto ?? 0;

                        $totales['diezmo'] += $diezmo;
                        $totales['ofrenda_especial'] += $ofrendaEspecial;
                        $totales['misiones'] += $misiones;
                        $totales['seminario'] += $seminario;
                        $totales['campa'] += $campa;
                        $totales['prestamo'] += $prestamo;
                        $totales['construccion'] += $construccion;
                        $totales['micro'] += $micro;
                        $totalGeneral += $sobre->total_declarado;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $sobre->numero_sobre }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $sobre->persona ? $sobre->persona->nombre : 'Anónimo' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $sobre->metodo_pago == 'transferencia' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($sobre->metodo_pago) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($diezmo, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($ofrendaEspecial, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($misiones, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($seminario, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($campa, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($prestamo, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($construccion, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($micro, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-blue-600">{{ number_format($sobre->total_declarado, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if(!$cultoSeleccionado->cerrado)
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('recuento-clases.edit', $sobre->id) }}"
                                   class="text-blue-600 hover:text-blue-800" title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('recuento-clases.destroy', $sobre->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('¿Estás seguro de eliminar este sobre?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-blue-50">
                    <tr class="font-bold">
                        <td colspan="3" class="px-4 py-3 text-right text-sm text-gray-700">TOTALES</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ number_format($totales['diezmo'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ number_format($totales['ofrenda_especial'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ number_format($totales['misiones'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ number_format($totales['seminario'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ number_format($totales['campa'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ number_format($totales['prestamo'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ number_format($totales['construccion'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ number_format($totales['micro'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-sm text-blue-700">{{ number_format($totalGeneral, 0, ',', '.') }}</td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-lg">No hay sobres registrados para esta clase en este culto.</p>
            @if(!$cultoSeleccionado->cerrado)
            <a href="{{ route('recuento-clases.create', ['culto_id' => $cultoSeleccionado->id, 'clase_id' => $claseSeleccionada->id]) }}"
               class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                Agregar primer sobre
            </a>
            @endif
        </div>
        @endif
    </div>
    @elseif($cultoSeleccionado && !$claseSeleccionada)
    <!-- Mensaje cuando no hay clase seleccionada -->
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <svg class="w-16 h-16 mx-auto mb-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Seleccione una Clase</h3>
        <p class="text-gray-500">Seleccione una clase para ver los sobres registrados.</p>
    </div>
    @else
    <!-- Mensaje cuando no hay culto seleccionado -->
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Seleccione un Culto</h3>
        <p class="text-gray-500">Seleccione un culto para comenzar a registrar sobres por clase.</p>
    </div>
    @endif
</div>
@endsection
