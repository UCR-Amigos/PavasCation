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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Teléfono</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Correo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden xl:table-cell">Sobres</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden xl:table-cell">Promesas</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($personas as $persona)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $persona->nombre }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                        {{ $persona->telefono ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                        {{ $persona->correo ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $persona->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $persona->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                        {{ $persona->sobres_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                        {{ $persona->promesas_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <!-- Desktop actions (solo pantallas grandes) -->
                        <div class="hidden lg:flex lg:items-center lg:justify-end lg:gap-3">
                            <a href="{{ route('personas.show', $persona) }}" class="text-green-600 hover:text-green-900">Ver</a>
                            <a href="{{ route('compromisos.show', $persona) }}" class="text-purple-600 hover:text-purple-900">Compromiso</a>
                            <a href="{{ route('personas.edit', $persona) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                            <button type="button" onclick="mostrarModalEliminar({{ $persona->id }}, '{{ $persona->nombre }}')" class="text-red-600 hover:text-red-900">
                                Eliminar
                            </button>
                        </div>
                        
                        <!-- Mobile/Tablet dropdown -->
                        <div class="relative lg:hidden">
                            <button type="button" onclick="togglePersonaDropdown({{ $persona->id }})" class="p-2 hover:bg-gray-100 rounded-full">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                </svg>
                            </button>
                            <div id="persona-dropdown-{{ $persona->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                <div class="py-1">
                                    <a href="{{ route('personas.show', $persona) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Ver
                                        </span>
                                    </a>
                                    <a href="{{ route('compromisos.show', $persona) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                            </svg>
                                            Compromiso
                                        </span>
                                    </a>
                                    <a href="{{ route('personas.edit', $persona) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                                                <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Editar
                                        </span>
                                    </a>
                                    <button type="button" onclick="mostrarModalEliminar({{ $persona->id }}, '{{ $persona->nombre }}'); togglePersonaDropdown({{ $persona->id }})" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-red-50">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            Eliminar
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
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

function togglePersonaDropdown(id) {
    const dropdown = document.getElementById('persona-dropdown-' + id);
    const allDropdowns = document.querySelectorAll('[id^="persona-dropdown-"]');
    
    // Cierra todos los otros dropdowns
    allDropdowns.forEach(d => {
        if (d !== dropdown) {
            d.classList.add('hidden');
        }
    });
    
    // Toggle el dropdown actual
    dropdown.classList.toggle('hidden');
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="togglePersonaDropdown"]') && !event.target.closest('[id^="persona-dropdown-"]')) {
        const allDropdowns = document.querySelectorAll('[id^="persona-dropdown-"]');
        allDropdowns.forEach(d => d.classList.add('hidden'));
    }
});
</script>
@endsection
