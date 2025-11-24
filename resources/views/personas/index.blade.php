@extends('layouts.admin')

@section('title', 'IBBSC - Personas')
@section('page-title', 'Gestión de Personas')

@section('content')
<div class="space-y-6">
    <!-- Buscador -->
    <div class="bg-white rounded-lg shadow-md p-4">
        <form method="GET" action="{{ route('personas.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           name="buscar" 
                           value="{{ request('buscar') }}" 
                           placeholder="Buscar por nombre..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Buscar
                </button>
                @if(request('buscar'))
                <a href="{{ route('personas.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Limpiar
                </a>
                @endif
            </div>
        </form>
    </div>

    <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-3">
        <div class="flex flex-col sm:flex-row flex-wrap gap-2">
            @if($personasInactivas > 0)
            <form action="{{ route('personas.limpiar-inactivas') }}" method="POST" class="inline-block"
                  onsubmit="return confirm('¿Estás seguro de eliminar TODAS las personas inactivas y sus promesas? Los sobres se mantendrán.');">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors text-sm whitespace-nowrap">
                    🗑️ Limpiar Inactivas ({{ $personasInactivas }})
                </button>
            </form>
            @endif
            
            <form action="{{ route('personas.resetear-promesas') }}" method="POST" class="inline-block"
                  onsubmit="return confirm('⚠️ ATENCIÓN: Esto eliminará TODAS las promesas y compromisos de TODAS las personas. Los sobres dados se mantienen como historial. ¿Estás seguro?');">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-3 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors text-sm whitespace-nowrap">
                    🔄 Resetear Promesas
                </button>
            </form>

            <button onclick="mostrarModalReporte()" class="w-full sm:w-auto px-3 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors text-sm whitespace-nowrap flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                📊 Reporte PDF
            </button>
        </div>
        <a href="{{ route('personas.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-center whitespace-nowrap">
            + Nueva Persona
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Correo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sobres</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Promesas</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($personas as $persona)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $persona->nombre }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $persona->telefono ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $persona->correo ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $persona->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $persona->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $persona->sobres_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $persona->promesas_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('personas.show', $persona) }}" class="text-green-600 hover:text-green-900 mr-3">Ver</a>
                        <a href="{{ route('compromisos.show', $persona) }}" class="text-purple-600 hover:text-purple-900 mr-3">Compromiso</a>
                        <a href="{{ route('personas.edit', $persona) }}" class="text-blue-600 hover:text-blue-900 mr-3">Editar</a>
                        <button type="button" onclick="mostrarModalEliminar({{ $persona->id }}, '{{ $persona->nombre }}')" class="text-red-600 hover:text-red-900">
                            Eliminar
                        </button>
                        <form id="form-eliminar-{{ $persona->id }}" action="{{ route('personas.destroy', $persona) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        No hay personas registradas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $personas->links() }}
    </div>
</div>

<!-- Modal: Confirmar Eliminación -->
<div id="modalEliminar" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-red-900">⚠️ Eliminar Persona</h3>
                <button onclick="cerrarModalEliminar()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-yellow-800 mb-2">
                    ¿Estás seguro de que deseas eliminar a <strong id="nombrePersona"></strong>?
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

<!-- Modal: Reporte PDF -->
<div id="modalReporte" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-purple-900 flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Reporte de Personas PDF
                </h3>
                <button onclick="cerrarModalReporte()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-6">
                <!-- Filtro 1: Por Rango de Meses del Año Actual -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-5 border border-purple-200">
                    <h4 class="font-semibold text-purple-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Opción 1: Filtrar por Meses ({{ date('Y') }})
                    </h4>
                    <form action="{{ route('personas.reporte-pdf') }}" method="GET" target="_blank">
                        <input type="hidden" name="tipo_filtro" value="meses">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mes de Inicio</label>
                                <select name="mes_inicio" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Selecciona mes...</option>
                                    @for($i = 1; $i <= (int)date('m'); $i++)
                                        <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->locale('es')->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mes de Fin</label>
                                <select name="mes_fin" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Selecciona mes...</option>
                                    @for($i = 1; $i <= (int)date('m'); $i++)
                                        <option value="{{ $i }}" {{ $i == (int)date('m') ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->locale('es')->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" name="accion" value="ver" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Ver PDF
                            </button>
                            <button type="submit" name="accion" value="descargar" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Descargar PDF
                            </button>
                        </div>
                    </form>
                </div>

                <div class="text-center text-gray-500 font-medium">
                    - O -
                </div>

                <!-- Filtro 2: Por Rango de Fechas Exactas -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-5 border border-blue-200">
                    <h4 class="font-semibold text-blue-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Opción 2: Filtrar por Fechas Exactas
                    </h4>
                    <form action="{{ route('personas.reporte-pdf') }}" method="GET" target="_blank">
                        <input type="hidden" name="tipo_filtro" value="fechas">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inicio</label>
                                <input type="date" name="fecha_inicio" required max="{{ date('Y-m-d') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Fin</label>
                                <input type="date" name="fecha_fin" required max="{{ date('Y-m-d') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" name="accion" value="ver" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Ver PDF
                            </button>
                            <button type="submit" name="accion" value="descargar" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Descargar PDF
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <strong>💡 Nota:</strong> El reporte incluirá todas las personas activas que hayan registrado sobres en el período seleccionado.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let personaIdEliminar = null;

function mostrarModalEliminar(id, nombre) {
    personaIdEliminar = id;
    document.getElementById('nombrePersona').textContent = nombre;
    document.getElementById('modalEliminar').classList.remove('hidden');
}

function cerrarModalEliminar() {
    document.getElementById('modalEliminar').classList.add('hidden');
    personaIdEliminar = null;
}

function confirmarEliminacion() {
    if (personaIdEliminar) {
        document.getElementById('form-eliminar-' + personaIdEliminar).submit();
    }
}

function mostrarModalReporte() {
    document.getElementById('modalReporte').classList.remove('hidden');
}

function cerrarModalReporte() {
    document.getElementById('modalReporte').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalReporte')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalReporte();
    }
});

document.getElementById('modalEliminar')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalEliminar();
    }
});
</script>
@endsection
