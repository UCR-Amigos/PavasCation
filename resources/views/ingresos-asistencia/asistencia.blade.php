@extends('layouts.admin')

@section('title', 'IBBSC - Reportes de Asistencia')
@section('page-title', 'Reportes de Asistencia')

@section('content')
<div class="space-y-6">
    <!-- Filtros y Búsqueda -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('ingresos-asistencia.asistencia') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Cultos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Registros de Asistencia</h3>
            <a href="{{ route('ingresos-asistencia.pdf-asistencia', ['fecha_inicio' => request('fecha_inicio'), 'fecha_fin' => request('fecha_fin')]) }}" 
               class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors" target="_blank">
                Descargar PDF
            </a>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo Culto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Asistencia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capilla</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clases</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">PDF</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($cultos as $culto)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $culto->fecha->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($culto->tipo_culto) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($culto->asistencia)
                            <span class="text-lg font-bold text-green-600">{{ $culto->asistencia->total_asistencia }}</span>
                        @else
                            <span class="text-sm text-gray-400">Sin registro</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($culto->asistencia)
                            {{ $culto->asistencia->chapel_hombres + $culto->asistencia->chapel_mujeres + 
                               $culto->asistencia->chapel_adultos_mayores + $culto->asistencia->chapel_adultos + 
                               $culto->asistencia->chapel_jovenes }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($culto->asistencia)
                            {{ $culto->asistencia->total_asistencia - 
                               ($culto->asistencia->chapel_hombres + $culto->asistencia->chapel_mujeres + 
                                $culto->asistencia->chapel_adultos_mayores + $culto->asistencia->chapel_adultos + 
                                $culto->asistencia->chapel_jovenes) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        @if($culto->asistencia)
                            <a href="{{ route('ingresos-asistencia.pdf-asistencia-culto', $culto) }}" 
                               class="text-red-600 hover:text-red-900" target="_blank">
                                Descargar
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        No hay registros de asistencia
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Descargas por Mes -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Descargas Mensuales</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($mesesDisponibles as $mes)
            <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 transition-colors">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">{{ $mes['nombre'] }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $mes['año'] }}</p>
                    </div>
                    <a href="{{ route('ingresos-asistencia.pdf-asistencia-mes', ['mes' => $mes['numero'], 'año' => $mes['año']]) }}" 
                       class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm" target="_blank">
                        Descargar
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
