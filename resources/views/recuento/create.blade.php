@extends('layouts.admin')

@section('title', 'IBBSC - Agregar Sobre')
@section('page-title', 'Registrar Nuevo Sobre')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('recuento.store') }}" method="POST" id="sobreForm">
            @csrf

            <input type="hidden" name="culto_id" value="{{ $culto ? $culto->id : request('culto_id') }}">

            @if($culto)
            <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-600">Culto seleccionado:</p>
                <p class="text-lg font-semibold text-blue-900">
                    {{ $culto->fecha->format('d/m/Y') }} - {{ ucfirst($culto->tipo_culto) }}
                </p>
            </div>
            @endif

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="persona_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Persona (Opcional)
                        </label>
                        <div class="space-y-2">
                            <div class="relative">
                                <input type="text" id="buscarPersona" placeholder="Buscar persona..." 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <div id="resultadosBusqueda" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                </div>
                            </div>
                            <select name="persona_id" id="persona_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Anónimo</option>
                                @foreach($personas as $persona)
                                    <option value="{{ $persona->id }}" {{ old('persona_id') == $persona->id ? 'selected' : '' }}>
                                        {{ $persona->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" id="btnAgregarRapida" class="w-full px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm">
                                + Agregar Persona Rápida
                            </button>
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
                            <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        </select>
                        @error('metodo_pago')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Detalles del Sobre</h3>
                    
                    <div class="space-y-3" id="detallesContainer">
                        <!-- Categorías fijas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="diezmo" class="block text-sm font-medium text-gray-700 mb-2">Diezmo</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                    <input type="number" name="detalles[0][monto]" id="diezmo" step="0.01" min="0" value="0"
                                           class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 detalle-monto">
                                    <input type="hidden" name="detalles[0][categoria]" value="diezmo">
                                </div>
                            </div>

                            <div>
                                <label for="misiones" class="block text-sm font-medium text-gray-700 mb-2">Misiones</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                    <input type="number" name="detalles[1][monto]" id="misiones" step="0.01" min="0" value="0"
                                           class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 detalle-monto">
                                    <input type="hidden" name="detalles[1][categoria]" value="misiones">
                                </div>
                            </div>

                            <div>
                                <label for="seminario" class="block text-sm font-medium text-gray-700 mb-2">Seminario</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                    <input type="number" name="detalles[2][monto]" id="seminario" step="0.01" min="0" value="0"
                                           class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 detalle-monto">
                                    <input type="hidden" name="detalles[2][categoria]" value="seminario">
                                </div>
                            </div>

                            <div>
                                <label for="campa" class="block text-sm font-medium text-gray-700 mb-2">Campamento</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                    <input type="number" name="detalles[3][monto]" id="campa" step="0.01" min="0" value="0"
                                           class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 detalle-monto">
                                    <input type="hidden" name="detalles[3][categoria]" value="campa">
                                </div>
                            </div>

                            <div>
                                <label for="prestamo" class="block text-sm font-medium text-gray-700 mb-2">Préstamo</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                    <input type="number" name="detalles[4][monto]" id="prestamo" step="0.01" min="0" value="0"
                                           class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 detalle-monto">
                                    <input type="hidden" name="detalles[4][categoria]" value="prestamo">
                                </div>
                            </div>

                            <div>
                                <label for="construccion" class="block text-sm font-medium text-gray-700 mb-2">Construcción</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                    <input type="number" name="detalles[5][monto]" id="construccion" step="0.01" min="0" value="0"
                                           class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 detalle-monto">
                                    <input type="hidden" name="detalles[5][categoria]" value="construccion">
                                </div>
                            </div>

                            <div>
                                <label for="micro" class="block text-sm font-medium text-gray-700 mb-2">Micro</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                    <input type="number" name="detalles[6][monto]" id="micro" step="0.01" min="0" value="0"
                                           class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 detalle-monto">
                                    <input type="hidden" name="detalles[6][categoria]" value="micro">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-700">Total Declarado:</span>
                            <span class="text-2xl font-bold text-blue-600" id="totalDeclarado">$0.00</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="notas" class="block text-sm font-medium text-gray-700 mb-2">Notas (Opcional)</label>
                    <textarea name="notas" id="notas" rows="3" 
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notas') }}</textarea>
                    @error('notas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('recuento.index', ['culto_id' => $culto ? $culto->id : '']) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Guardar Sobre
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
        const btnAgregarRapida = document.getElementById('btnAgregarRapida');

        function calcularTotal() {
            let total = 0;
            inputs.forEach(input => {
                const valor = parseFloat(input.value) || 0;
                total += valor;
            });
            totalElement.textContent = '$' + total.toFixed(2);
        }

        inputs.forEach(input => {
            input.addEventListener('input', calcularTotal);
        });

        calcularTotal();

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

        // Agregar persona rápida
        btnAgregarRapida.addEventListener('click', function() {
            const nombre = prompt('Ingrese el nombre de la persona:');
            if (nombre && nombre.trim()) {
                fetch('{{ route("personas.quick-store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ nombre: nombre.trim() })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const option = new Option(data.persona.nombre, data.persona.id, true, true);
                        personaSelect.add(option);
                        personaSelect.value = data.persona.id;
                        buscarInput.value = data.persona.nombre;
                        alert('Persona agregada exitosamente');
                    } else {
                        alert('Error al agregar persona');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al agregar persona');
                });
            }
        });
    });
</script>
@endpush
@endsection
