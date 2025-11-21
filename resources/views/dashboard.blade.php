@extends('layouts.admin')

@section('title', 'Dashboard - Sistema de Iglesia')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Semanal</dt>
                        <dd class="text-lg font-semibold text-gray-900">${{ number_format($totalesSemana['total_general'], 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Diezmos</dt>
                        <dd class="text-lg font-semibold text-gray-900">${{ number_format($totalesSemana['total_diezmo'], 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Misiones</dt>
                        <dd class="text-lg font-semibold text-gray-900">${{ number_format($totalesSemana['total_misiones'], 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Construcción</dt>
                        <dd class="text-lg font-semibold text-gray-900">${{ number_format($totalesSemana['total_construccion'], 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfico de Barras - Ingresos por Culto -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Ingresos por Culto (Últimos 10)</h3>
            <canvas id="ingresosChart"></canvas>
        </div>

        <!-- Gráfico Circular - Distribución por Categorías -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Distribución por Categorías (Mes Actual)</h3>
            <canvas id="distribucionChart"></canvas>
        </div>
    </div>

    <!-- Asistencia y Promesas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Línea de Tiempo - Asistencia -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Tendencia de Asistencia</h3>
            <canvas id="asistenciaChart"></canvas>
        </div>

        <!-- Promesas Cumplidas vs Pendientes -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Estado de Promesas (Mes Actual)</h3>
            <canvas id="promesasChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Ingresos por Culto
    const ingresosCtx = document.getElementById('ingresosChart').getContext('2d');
    new Chart(ingresosCtx, {
        type: 'bar',
        data: {
            labels: @json($cultosRecientes->map(fn($c) => $c->fecha->format('d/m'))),
            datasets: [{
                label: 'Ingresos Totales',
                data: @json($cultosRecientes->map(fn($c) => $c->totales ? $c->totales->total_general : 0)),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            height: 300,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Distribución por Categorías
    const distribucionCtx = document.getElementById('distribucionChart').getContext('2d');
    new Chart(distribucionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Diezmo', 'Misiones', 'Seminario', 'Campamento', 'Préstamo', 'Construcción', 'Micro', 'Suelto'],
            datasets: [{
                data: [
                    {{ $distribucion['diezmo'] }},
                    {{ $distribucion['misiones'] }},
                    {{ $distribucion['seminario'] }},
                    {{ $distribucion['campa'] }},
                    {{ $distribucion['prestamo'] }},
                    {{ $distribucion['construccion'] }},
                    {{ $distribucion['micro'] }},
                    {{ $distribucion['suelto'] }}
                ],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(14, 165, 233, 0.8)',
                    'rgba(156, 163, 175, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            height: 300
        }
    });

    // Gráfico de Asistencia
    const asistenciaCtx = document.getElementById('asistenciaChart').getContext('2d');
    new Chart(asistenciaCtx, {
        type: 'line',
        data: {
            labels: @json($asistencias->pluck('fecha')),
            datasets: [{
                label: 'Asistencia Total',
                data: @json($asistencias->pluck('total')),
                borderColor: 'rgba(16, 185, 129, 1)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            height: 300,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de Promesas
    const promesasCtx = document.getElementById('promesasChart').getContext('2d');
    new Chart(promesasCtx, {
        type: 'pie',
        data: {
            labels: ['Cumplidas', 'Pendientes'],
            datasets: [{
                data: [{{ $promesasStatus['cumplidas'] }}, {{ $promesasStatus['pendientes'] }}],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            height: 300
        }
    });
</script>
@endpush
