@extends('layouts.admin')

@section('title', 'IBBSC - Nueva Asistencia')
@section('page-title', 'Nueva Asistencia')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('asistencia.store') }}" method="POST" id="asistenciaForm">
            @csrf

            <div class="mb-6">
                <label for="culto_id" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Culto *</label>
                <select name="culto_id" id="culto_id" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">-- Seleccione un culto --</option>
                    @foreach($cultos as $culto)
                        <option value="{{ $culto->id }}" {{ old('culto_id', request('culto_id')) == $culto->id ? 'selected' : '' }}>
                            {{ $culto->fecha->format('d/m/Y') }} - {{ ucfirst($culto->tipo_culto) }}
                        </option>
                    @endforeach
                </select>
                @error('culto_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-6">
                <!-- Capilla -->
                <div class="border rounded-lg overflow-hidden">
                    <button type="button" onclick="toggleSection('capilla')" class="w-full px-4 py-3 bg-blue-50 hover:bg-blue-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-blue-900">Capilla</h3>
                        <svg id="capilla-icon" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="capilla-content" class="p-4 hidden">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adultos Hombres</label>
                            <input type="number" name="chapel_adultos_hombres" min="0" value="{{ old('chapel_adultos_hombres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adultos Mujeres</label>
                            <input type="number" name="chapel_adultos_mujeres" min="0" value="{{ old('chapel_adultos_mujeres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adultos Mayores Hombres</label>
                            <input type="number" name="chapel_adultos_mayores_hombres" min="0" value="{{ old('chapel_adultos_mayores_hombres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adultos Mayores Mujeres</label>
                            <input type="number" name="chapel_adultos_mayores_mujeres" min="0" value="{{ old('chapel_adultos_mayores_mujeres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jóvenes Masculinos</label>
                            <input type="number" name="chapel_jovenes_masculinos" min="0" value="{{ old('chapel_jovenes_masculinos', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jóvenes Femeninas</label>
                            <input type="number" name="chapel_jovenes_femeninas" min="0" value="{{ old('chapel_jovenes_femeninas', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maestros Hombres</label>
                            <input type="number" name="chapel_maestros_hombres" min="0" value="{{ old('chapel_maestros_hombres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Clase 0-1 -->
                <div class="border rounded-lg overflow-hidden">
                    <button type="button" onclick="toggleSection('clase01')" class="w-full px-4 py-3 bg-green-50 hover:bg-green-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-green-900">Clase 0-1 Años</h3>
                        <svg id="clase01-icon" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="clase01-content" class="p-4 hidden">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Niños</label>
                            <input type="number" name="clase_0_1_hombres" min="0" value="{{ old('clase_0_1_hombres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Niñas</label>
                            <input type="number" name="clase_0_1_mujeres" min="0" value="{{ old('clase_0_1_mujeres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maestros (H)</label>
                            <input type="number" name="clase_0_1_maestros_hombres" min="0" value="{{ old('clase_0_1_maestros_hombres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maestras (M)</label>
                            <input type="number" name="clase_0_1_maestros_mujeres" min="0" value="{{ old('clase_0_1_maestros_mujeres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Clase 2-6 -->
                <div class="border rounded-lg overflow-hidden">
                    <button type="button" onclick="toggleSection('clase26')" class="w-full px-4 py-3 bg-yellow-50 hover:bg-yellow-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-yellow-900">Clase 2-6 Años</h3>
                        <svg id="clase26-icon" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="clase26-content" class="p-4 hidden">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Niños</label>
                            <input type="number" name="clase_2_6_hombres" min="0" value="{{ old('clase_2_6_hombres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Niñas</label>
                            <input type="number" name="clase_2_6_mujeres" min="0" value="{{ old('clase_2_6_mujeres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maestros (H)</label>
                            <input type="number" name="clase_2_6_maestros_hombres" min="0" value="{{ old('clase_2_6_maestros_hombres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maestras (M)</label>
                            <input type="number" name="clase_2_6_maestros_mujeres" min="0" value="{{ old('clase_2_6_maestros_mujeres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Clase 7-8 -->
                <div class="border rounded-lg overflow-hidden">
                    <button type="button" onclick="toggleSection('clase78')" class="w-full px-4 py-3 bg-purple-50 hover:bg-purple-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-purple-900">Clase 7-8 Años</h3>
                        <svg id="clase78-icon" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="clase78-content" class="p-4 hidden">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Niños</label>
                            <input type="number" name="clase_7_8_hombres" min="0" value="{{ old('clase_7_8_hombres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Niñas</label>
                            <input type="number" name="clase_7_8_mujeres" min="0" value="{{ old('clase_7_8_mujeres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maestros (H)</label>
                            <input type="number" name="clase_7_8_maestros_hombres" min="0" value="{{ old('clase_7_8_maestros_hombres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maestras (M)</label>
                            <input type="number" name="clase_7_8_maestros_mujeres" min="0" value="{{ old('clase_7_8_maestros_mujeres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Clase 9-11 -->
                <div class="border rounded-lg overflow-hidden">
                    <button type="button" onclick="toggleSection('clase911')" class="w-full px-4 py-3 bg-red-50 hover:bg-red-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-red-900">Clase 9-11 Años</h3>
                        <svg id="clase911-icon" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="clase911-content" class="p-4 hidden">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Niños</label>
                            <input type="number" name="clase_9_11_hombres" min="0" value="{{ old('clase_9_11_hombres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Niñas</label>
                            <input type="number" name="clase_9_11_mujeres" min="0" value="{{ old('clase_9_11_mujeres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maestros (H)</label>
                            <input type="number" name="clase_9_11_maestros_hombres" min="0" value="{{ old('clase_9_11_maestros_hombres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maestras (M)</label>
                            <input type="number" name="clase_9_11_maestros_mujeres" min="0" value="{{ old('clase_9_11_maestros_mujeres', 0) }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 asistencia-input" required>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Total Asistencia -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <label for="total_asistencia" class="text-lg font-semibold text-gray-700">Total Asistencia *</label>
                        <input type="number" name="total_asistencia" id="total_asistencia" min="0" value="{{ old('total_asistencia', 0) }}" 
                               class="w-32 text-2xl font-bold text-center rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required readonly>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('asistencia.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Guardar Asistencia
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleSection(sectionId) {
        const content = document.getElementById(sectionId + '-content');
        const icon = document.getElementById(sectionId + '-icon');
        
        content.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.asistencia-input');
        const totalInput = document.getElementById('total_asistencia');

        function calcularTotal() {
            let total = 0;
            inputs.forEach(input => {
                total += parseInt(input.value) || 0;
            });
            totalInput.value = total;
        }

        inputs.forEach(input => {
            input.addEventListener('input', calcularTotal);
        });

        calcularTotal();
    });
</script>
@endpush
@endsection
