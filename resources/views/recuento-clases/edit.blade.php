@extends('layouts.admin')

@section('title', 'Pavas - Editar Sobre de Clase')
@section('page-title', 'Editar Sobre de Clase')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('recuento-clases.update', $sobre->id) }}" method="POST" id="sobreForm">
            @csrf
            @method('PUT')

            <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-600">Culto:</p>
                <p class="text-lg font-semibold text-blue-900">
                    {{ $sobre->culto->fecha->format('d/m/Y') }} - {{ ucfirst($sobre->culto->tipo_culto) }}
                </p>
                <p class="text-sm text-gray-500 mt-1">Sobre #{{ $sobre->numero_sobre }}</p>
            </div>

            <div class="space-y-4">
                <!-- Selector de Clase -->
                <div>
                    <label for="clase_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Clase *
                    </label>
                    <select name="clase_id" id="clase_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">-- Seleccione una clase --</option>
                        @foreach($clases as $clase)
                            <option value="{{ $clase->id }}"
                                    {{ (old('clase_id', $sobre->clase_id) == $clase->id) ? 'selected' : '' }}
                                    data-color="{{ $clase->color }}">
                                {{ $clase->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('clase_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="persona_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Persona (Opcional)
                        </label>
                        <div class="space-y-2">
                            <div class="relative">
                                <input type="text" id="buscarPersona" placeholder="Buscar persona..."
                                       value="{{ $sobre->persona ? $sobre->persona->nombre : '' }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <div id="resultadosBusqueda" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                </div>
                            </div>
                            <select name="persona_id" id="persona_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Anónimo</option>
                                @foreach($personas as $persona)
                                    <option value="{{ $persona->id }}" {{ old('persona_id', $sobre->persona_id) == $persona->id ? 'selected' : '' }}>
                                        {{ $persona->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('persona_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="metodo_pago" class="block text-sm font-medium text-gray-700 mb-2">
                            Método de Pago *
                        </label>
                        <select name="metodo_pago" id="metodo_pago"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="efectivo" {{ old('metodo_pago', $sobre->metodo_pago) == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="transferencia" {{ old('metodo_pago', $sobre->metodo_pago) == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        </select>
                        @error('metodo_pago')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div id="comprobanteWrapper" class="mt-3 {{ old('metodo_pago', $sobre->metodo_pago) == 'transferencia' ? '' : 'hidden' }}">
                            <label for="comprobante_numero" class="block text-sm font-medium text-gray-700 mb-2">N° Comprobante (Transferencia)</label>
                            <input type="text" name="comprobante_numero" id="comprobante_numero" value="{{ old('comprobante_numero', $sobre->comprobante_numero) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ej: 1234567890">
                            @error('comprobante_numero')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Detalles del Sobre</h3>

                    @php
                        $mapeoCategoria = ['campa' => 'campamento', 'prestamo' => 'pro_templo', 'pro-templo' => 'pro_templo', 'ofrenda-especial' => 'ofrenda_especial'];
                        $detallesPorCategoria = $sobre->detalles->keyBy(fn($d) => $mapeoCategoria[strtolower($d->categoria)] ?? strtolower($d->categoria));
                    @endphp

                    <div class="space-y-3" id="detallesContainer">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="diezmo" class="block text-sm font-medium text-gray-700 mb-2">Diezmo</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₡</span>
                                    <input type="number" name="detalles[0][monto]" id="diezmo" step="0.01" min="0"
                                           value="{{ old('detalles.0.monto', $detallesPorCategoria->get('diezmo')->monto ?? 0) }}"
                                           class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 detalle-monto">
                                    <input type="hidden" name="detalles[0][categoria]" value="diezmo">
                                </div>
                            </div>

                            <div>
                                <label for="ofrenda_especial" class="block text-sm font-medium text-gray-700 mb-2">Ofrenda Especial</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₡</span>
                                    <input type="number" name="detalles[1][monto]" id="ofrenda_especial" step="0.01" min="0"
                                           value="{{ old('detalles.1.monto', $detallesPorCategoria->get('ofrenda_especial')->monto ?? 0) }}"
                                           class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 detalle-monto">
                                    <input type="hidden" name="detalles[1][categoria]" value="ofrenda_especial">
                                </div>
                            </div>

                            <div>
                                <label for="misiones" class="block text-sm font-medium text-gray-700 mb-2">Misiones</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₡</span>
                                    <input type="number" name="detalles[2][monto]" id="misiones" step="0.01" min="0"
                                           value="{{ old('detalles.2.monto', $detallesPorCategoria->get('misiones')->monto ?? 0) }}"
                                           class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 detalle-monto">
                                    <input type="hidden" name="detalles[2][categoria]" value="misiones">
                                </div>
                            </div>

                            <div>
                                <label for="seminario" class="block text-sm font-medium text-gray-700 mb-2">Seminario</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₡</span>
                                    <input type="number" name="detalles[3][monto]" id="seminario" step="0.01" min="0"
                                           value="{{ old('detalles.3.monto', $detallesPorCategoria->get('seminario')->monto ?? 0) }}"
                                           class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 detalle-monto">
                                    <input type="hidden" name="detalles[3][categoria]" value="seminario">
                                </div>
                            </div>

                            <div>
                                <label for="campamento" class="block text-sm font-medium text-gray-700 mb-2">Campamento</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₡</span>
                                    <input type="number" name="detalles[4][monto]" id="campamento" step="0.01" min="0"
                                           value="{{ old('detalles.4.monto', $detallesPorCategoria->get('campamento')->monto ?? 0) }}"
                                           class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 detalle-monto">
                                    <input type="hidden" name="detalles[4][categoria]" value="campamento">
                                </div>
                            </div>

                            <div>
                                <label for="pro_templo" class="block text-sm font-medium text-gray-700 mb-2">Pro-Templo</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₡</span>
                                    <input type="number" name="detalles[5][monto]" id="pro_templo" step="0.01" min="0"
                                           value="{{ old('detalles.5.monto', $detallesPorCategoria->get('pro_templo')->monto ?? 0) }}"
                                           class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 detalle-monto">
                                    <input type="hidden" name="detalles[5][categoria]" value="pro_templo">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-700">Total Declarado:</span>
                            <span class="text-2xl font-bold text-blue-600" id="totalDeclarado">₡0.00</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="notas" class="block text-sm font-medium text-gray-700 mb-2">Notas (Opcional)</label>
                    <textarea name="notas" id="notas" rows="3"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notas', $sobre->notas) }}</textarea>
                    @error('notas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('recuento-clases.index', ['culto_id' => $sobre->culto_id, 'clase_id' => $sobre->clase_id]) }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Actualizar Sobre
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.detalle-monto');
        const totalElement = document.getElementById('totalDeclarado');
        const buscarInput = document.getElementById('buscarPersona');
        const resultadosDiv = document.getElementById('resultadosBusqueda');
        const personaSelect = document.getElementById('persona_id');
        const metodoPagoSelect = document.getElementById('metodo_pago');
        const comprobanteWrapper = document.getElementById('comprobanteWrapper');
        const comprobanteInput = document.getElementById('comprobante_numero');

        function calcularTotal() {
            let total = 0;
            inputs.forEach(input => {
                const valor = parseFloat(input.value) || 0;
                total += valor;
            });
            totalElement.textContent = '₡' + total.toFixed(2);
        }

        inputs.forEach(input => {
            input.addEventListener('input', calcularTotal);
            input.addEventListener('focus', function() {
                this.select();
            });
        });

        calcularTotal();

        function toggleComprobante() {
            if (metodoPagoSelect.value === 'transferencia') {
                comprobanteWrapper.classList.remove('hidden');
                comprobanteInput.required = true;
            } else {
                comprobanteWrapper.classList.add('hidden');
                comprobanteInput.required = false;
            }
        }
        metodoPagoSelect.addEventListener('change', toggleComprobante);
        toggleComprobante();

        // Búsqueda de personas
        buscarInput.addEventListener('input', function() {
            const busqueda = this.value.toLowerCase();
            if (busqueda.length < 2) {
                resultadosDiv.classList.add('hidden');
                return;
            }

            const opciones = Array.from(personaSelect.options).slice(1);
            const resultados = opciones.filter(option =>
                option.text.toLowerCase().includes(busqueda)
            );

            if (resultados.length > 0) {
                resultadosDiv.innerHTML = resultados.map(option => `
                    <div class="px-4 py-2 hover:bg-blue-50 cursor-pointer resultado-item" data-id="${option.value}">
                        ${option.text}
                    </div>
                `).join('');
                resultadosDiv.classList.remove('hidden');

                document.querySelectorAll('.resultado-item').forEach(item => {
                    item.addEventListener('click', function() {
                        personaSelect.value = this.dataset.id;
                        buscarInput.value = this.textContent.trim();
                        resultadosDiv.classList.add('hidden');
                    });
                });
            } else {
                resultadosDiv.innerHTML = '<div class="px-4 py-2 text-gray-500">No se encontraron resultados</div>';
                resultadosDiv.classList.remove('hidden');
            }
        });

        document.addEventListener('click', function(e) {
            if (!buscarInput.contains(e.target) && !resultadosDiv.contains(e.target)) {
                resultadosDiv.classList.add('hidden');
            }
        });
    });
</script>
@endpush
@endsection
