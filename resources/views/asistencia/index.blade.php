@extends('layouts.admin')

@section('title', 'IBBSC - Asistencia')
@section('page-title', 'Registro de Asistencia')

@section('content')
<div class="space-y-6">
    <!-- Mensajes de éxito/error -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex justify-end">
        <a href="{{ route('asistencia.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
            + Nueva Asistencia
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo Culto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Asistencia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capilla</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clases</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
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
                               $culto->asistencia->chapel_jovenes_masculinos + $culto->asistencia->chapel_jovenes_femeninas }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($culto->asistencia)
                            {{ $culto->asistencia->total_asistencia - 
                               ($culto->asistencia->chapel_hombres + $culto->asistencia->chapel_mujeres + 
                                $culto->asistencia->chapel_adultos_mayores + $culto->asistencia->chapel_adultos + 
                                $culto->asistencia->chapel_jovenes_masculinos + $culto->asistencia->chapel_jovenes_femeninas) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if($culto->asistencia)
                            @if(!$culto->asistencia->cerrado)
                            <a href="{{ route('asistencia.edit', $culto->asistencia) }}" class="text-blue-600 hover:text-blue-900 mr-3">Editar</a>
                            <button type="button" onclick="cerrarAsistencia({{ $culto->asistencia->id }})" class="text-orange-600 hover:text-orange-900 mr-3">Cerrar</button>
                            <button type="button" onclick="mostrarModalEliminar({{ $culto->asistencia->id }}, '{{ $culto->fecha }}')" class="text-red-600 hover:text-red-900">
                                Eliminar
                            </button>
                            <form id="form-eliminar-{{ $culto->asistencia->id }}" action="{{ route('asistencia.destroy', $culto->asistencia) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                            @else
                            <span class="text-gray-400 text-xs flex items-center justify-end gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                Cerrado
                            </span>
                            @endif
                        @else
                            <a href="{{ route('asistencia.create', ['culto_id' => $culto->id]) }}" class="text-green-600 hover:text-green-900">Registrar</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        No hay cultos disponibles
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $cultos->links() }}
    </div>

    <!-- Lista de Asistencias Cerradas (Archivo) -->
    @if($asistenciasCerradas->count() > 0)
    <div class="mt-12 pt-8 border-t-2 border-gray-300">
        <div class="flex items-center gap-3 mb-6">
            <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"></path>
            </svg>
            <h2 class="text-2xl font-bold text-gray-800">Asistencias Cerradas (Archivo)</h2>
            <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-sm font-semibold">{{ $asistenciasCerradas->count() }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($asistenciasCerradas as $asistencia)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow border border-gray-200">
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $asistencia->culto->fecha->format('d/m/Y') }}</h3>
                            <p class="text-sm text-gray-600">{{ ucfirst($asistencia->culto->tipo_culto) }}</p>
                        </div>
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-medium">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            Cerrado
                        </span>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-3 mb-3">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs text-gray-600">Total Asistencia</span>
                            <span class="text-lg font-bold text-blue-600">{{ $asistencia->total_asistencia }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                            <div class="flex justify-between">
                                <span>Capilla:</span>
                                <span class="font-semibold">{{ $asistencia->chapel_hombres + $asistencia->chapel_mujeres + $asistencia->chapel_adultos_mayores + $asistencia->chapel_adultos + $asistencia->chapel_jovenes_masculinos + $asistencia->chapel_jovenes_femeninas }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Clases:</span>
                                <span class="font-semibold">{{ $asistencia->total_asistencia - ($asistencia->chapel_hombres + $asistencia->chapel_mujeres + $asistencia->chapel_adultos_mayores + $asistencia->chapel_adultos + $asistencia->chapel_jovenes_masculinos + $asistencia->chapel_jovenes_femeninas) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-xs text-gray-500 mb-3">
                        <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        Cerrado: {{ $asistencia->cerrado_at->format('d/m/Y H:i') }}
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('asistencia.edit', $asistencia) }}" class="flex-1 text-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                            </svg>
                            Ver
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
function cerrarAsistencia(asistenciaId) {
    if (confirm('¿Cerrar esta asistencia? Ya no podrás editarla después de cerrarla.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/asistencia/${asistenciaId}/cerrar`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

let asistenciaIdEliminar = null;

function mostrarModalEliminar(id, fecha) {
    asistenciaIdEliminar = id;
    document.getElementById('fechaAsistencia').textContent = fecha;
    document.getElementById('modalEliminar').classList.remove('hidden');
}

function cerrarModalEliminar() {
    document.getElementById('modalEliminar').classList.add('hidden');
    asistenciaIdEliminar = null;
}

function confirmarEliminacion() {
    if (asistenciaIdEliminar) {
        document.getElementById('form-eliminar-' + asistenciaIdEliminar).submit();
    }
}
</script>

<!-- Modal: Confirmar Eliminación -->
<div id="modalEliminar" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-red-900">⚠️ Eliminar Asistencia</h3>
                <button onclick="cerrarModalEliminar()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-yellow-800 mb-2">
                    ¿Estás seguro de que deseas eliminar la asistencia del <strong id="fechaAsistencia"></strong>?
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" 
                        onclick="cerrarModalEliminar()"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="button" 
                        onclick="confirmarEliminacion()"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Sí, Eliminar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
