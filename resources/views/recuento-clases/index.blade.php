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
            <button type="button" onclick="document.getElementById('modalSuelto').classList.remove('hidden')"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Dinero Suelto
            </button>
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
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pro T.</th>
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
                            'campamento' => 0,
                            'pro_templo' => 0,
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
                        $campamento = $detallesPorCategoria->get('campamento')->monto ?? 0;
                        $pro_templo = $detallesPorCategoria->get('pro_templo')->monto ?? 0;

                        $totales['diezmo'] += $diezmo;
                        $totales['ofrenda_especial'] += $ofrendaEspecial;
                        $totales['misiones'] += $misiones;
                        $totales['seminario'] += $seminario;
                        $totales['campamento'] += $campamento;
                        $totales['pro_templo'] += $pro_templo;
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
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($campamento, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($pro_templo, 0, ',', '.') }}</td>
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

                    <!-- Filas de Dinero Suelto -->
                    @foreach($ofrendasSueltas as $ofrenda)
                    @php
                        $totalGeneral += $ofrenda->monto;
                    @endphp
                    <tr class="hover:bg-green-50 bg-green-50/30">
                        <td class="px-4 py-3 text-sm" colspan="2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-medium text-green-700">Dinero Suelto</span>
                                    @if($ofrenda->descripcion)
                                    <span class="text-xs text-gray-500 block">{{ $ofrenda->descripcion }}</span>
                                    @endif
                                </div>
                                @if(!$cultoSeleccionado->cerrado)
                                <div class="flex gap-2 ml-2">
                                    <button onclick="editarSuelto({{ $ofrenda->id }}, {{ $ofrenda->monto }}, '{{ $ofrenda->descripcion }}')"
                                            class="text-blue-600 hover:text-blue-900 text-xs">
                                        Editar
                                    </button>
                                    @if(in_array(auth()->user()->rol, ['admin', 'tesorero']))
                                    <button type="button" onclick="mostrarModalEliminarSuelto({{ $ofrenda->id }}, '{{ $ofrenda->descripcion }}')" class="text-red-600 hover:text-red-900 text-xs">
                                        Eliminar
                                    </button>
                                    <form id="form-eliminar-suelto-{{ $ofrenda->id }}" action="{{ route('recuento-clases.destroy-suelto', $ofrenda) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Efectivo</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700" colspan="6">-</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">{{ number_format($ofrenda->monto, 0, ',', '.') }}</td>
                        <td class="px-4 py-3"></td>
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
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ number_format($totales['campamento'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-900">{{ number_format($totales['pro_templo'], 0, ',', '.') }}</td>
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

<!-- Modal para Dinero Suelto -->
<div id="modalSuelto" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Dinero Suelto</h3>
            <form action="{{ route('recuento-clases.store-suelto') }}" method="POST">
                @csrf
                <input type="hidden" name="culto_id" value="{{ $cultoSeleccionado?->id }}">
                <input type="hidden" name="clase_id" value="{{ $claseSeleccionada?->id }}">

                <div class="mb-4">
                    <label for="monto_suelto" class="block text-sm font-medium text-gray-700 mb-2">Monto (₡) *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">₡</span>
                        <input type="number" name="monto" id="monto_suelto" min="0.01" step="0.01" required
                               class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="descripcion_suelto" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="descripcion" id="descripcion_suelto" rows="2"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modalSuelto').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Dinero Suelto -->
<div id="modalEditarSuelto" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Dinero Suelto</h3>
            <form id="formEditarSuelto" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="monto_suelto_edit" class="block text-sm font-medium text-gray-700 mb-2">Monto (₡) *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">₡</span>
                        <input type="number" name="monto" id="monto_suelto_edit" min="0.01" step="0.01" required
                               class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="descripcion_suelto_edit" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="descripcion" id="descripcion_suelto_edit" rows="2"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modalEditarSuelto').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Eliminar Dinero Suelto -->
<div id="modalEliminarSuelto" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-red-900">⚠️ Eliminar Dinero Suelto</h3>
                <button onclick="cerrarModalEliminarSuelto()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-yellow-800 mb-2">
                    ¿Estás seguro de que deseas eliminar <strong id="descripcionSuelto"></strong>?
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button"
                        onclick="cerrarModalEliminarSuelto()"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="button"
                        onclick="confirmarEliminacionSuelto()"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Sí, Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function editarSuelto(id, monto, descripcion) {
    document.getElementById('formEditarSuelto').action = `/recuento-clases/suelto/${id}`;
    document.getElementById('monto_suelto_edit').value = monto;
    document.getElementById('descripcion_suelto_edit').value = descripcion || '';
    document.getElementById('modalEditarSuelto').classList.remove('hidden');
}

let sueltoIdEliminar = null;

function mostrarModalEliminarSuelto(id, descripcion) {
    sueltoIdEliminar = id;
    document.getElementById('descripcionSuelto').textContent = descripcion || 'este dinero suelto';
    document.getElementById('modalEliminarSuelto').classList.remove('hidden');
}

function cerrarModalEliminarSuelto() {
    document.getElementById('modalEliminarSuelto').classList.add('hidden');
    sueltoIdEliminar = null;
}

function confirmarEliminacionSuelto() {
    if (sueltoIdEliminar) {
        document.getElementById('form-eliminar-suelto-' + sueltoIdEliminar).submit();
    }
}
</script>
@endsection
