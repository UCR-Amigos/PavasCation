@extends('layouts.admin')

@section('title', 'IBBSC - Personas')
@section('page-title', 'Gestión de Personas')

@section('content')
<div class="space-y-6">
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
</script>
@endsection
