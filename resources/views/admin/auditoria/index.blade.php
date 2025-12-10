@extends('layouts.admin')

@section('title', 'IBBP - Auditoría')
@section('page-title', 'Auditoría del Sistema')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Filtros</h2>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="text" name="email" value="{{ request('email') }}" class="w-full rounded-md border-gray-300 shadow-sm"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Método</label>
                <select name="method" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Todos</option>
                    @foreach(['POST','PUT','PATCH','DELETE'] as $m)
                        <option value="{{ $m }}" {{ request('method') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ruta</label>
                <input type="text" name="route" value="{{ request('route') }}" class="w-full rounded-md border-gray-300 shadow-sm"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">IP</label>
                <input type="text" name="ip" value="{{ request('ip') }}" class="w-full rounded-md border-gray-300 shadow-sm"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-md border-gray-300 shadow-sm"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-md border-gray-300 shadow-sm"/>
            </div>
            <div class="md:col-span-3 lg:col-span-6 flex gap-2 justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filtrar</button>
                <a href="{{ route('admin.auditoria.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Registros</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ruta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción/URL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detalle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agente</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->user_email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ $log->method }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $log->route }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">{{ $log->action }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">
                                @if($log->model_type)
                                    <div class="text-gray-900 font-semibold">{{ class_basename($log->model_type) }} #{{ $log->model_id }} ({{ $log->event }})</div>
                                    @php
                                        $changesCount = is_array($log->changes_after) ? count($log->changes_after) : 0;
                                    @endphp
                                    <div class="text-gray-600">Cambios: {{ $changesCount }}</div>
                                @else
                                    <div class="text-gray-600">Sin modelo asociado</div>
                                @endif
                                <div class="mt-1">
                                    <a href="{{ route('admin.auditoria.show', $log) }}" class="text-blue-600 hover:text-blue-800">Ver detalle</a>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $log->ip_address }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">{{ Str::limit($log->user_agent, 60) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Sin registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
