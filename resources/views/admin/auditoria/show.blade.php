@extends('layouts.admin')

@section('title', 'IBBP - Detalle Auditoría')
@section('page-title', 'Detalle de Auditoría')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $log->method }} • {{ $log->route ?? $log->action }}</h2>
                <p class="text-sm text-gray-600">{{ $log->created_at->format('d/m/Y H:i:s') }} • IP: {{ $log->ip_address }}</p>
                <p class="text-sm text-gray-600">Usuario: {{ $log->user_email ?? 'N/A' }}</p>
            </div>
            <a href="{{ route('admin.auditoria.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Volver</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Modelo</h3>
            </div>
            <div class="p-6">
                @if($log->model_type)
                    <p class="text-sm"><span class="font-semibold">Tipo:</span> {{ class_basename($log->model_type) }}</p>
                    <p class="text-sm"><span class="font-semibold">ID:</span> {{ $log->model_id }}</p>
                    <p class="text-sm"><span class="font-semibold">Evento:</span> {{ ucfirst($log->event) }}</p>
                @else
                    <p class="text-sm text-gray-600">Sin modelo asociado</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Payload</h3>
            </div>
            <div class="p-6">
                @if(is_array($log->payload) && count($log->payload))
                    <pre class="text-xs bg-gray-100 p-3 rounded">{{ json_encode($log->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <p class="text-sm text-gray-600">Sin payload</p>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Cambios</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Antes</h4>
                @if(is_array($log->changes_before) && count($log->changes_before))
                    <pre class="text-xs bg-gray-100 p-3 rounded">{{ json_encode($log->changes_before, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <p class="text-sm text-gray-600">Sin datos</p>
                @endif
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Después</h4>
                @if(is_array($log->changes_after) && count($log->changes_after))
                    <pre class="text-xs bg-gray-100 p-3 rounded">{{ json_encode($log->changes_after, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <p class="text-sm text-gray-600">Sin datos</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
