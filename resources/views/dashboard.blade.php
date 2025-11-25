@extends('layouts.admin')

@section('title', 'IBBSC - Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    <!-- Selector de Mes con estilo Gemini -->
    <div class="glass-card p-6">
        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="mes" class="block text-sm font-semibold text-gradient mb-2">📅 Mes</label>
                <select name="mes" id="mes" class="input-gemini">
                    @foreach(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $index => $nombreMes)
                        <option value="{{ $index + 1 }}" {{ ($index + 1) == $mes ? 'selected' : '' }}>{{ $nombreMes }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label for="año" class="block text-sm font-semibold text-gradient mb-2">📆 Año</label>
                <select name="año" id="año" class="input-gemini">
                    @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}" {{ $y == $año ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <button type="submit" class="btn-gemini flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Actualizar
                </button>
            </div>
        </form>
    </div>

    <!-- Estadísticas Rápidas con animaciones escalonadas -->
    <div>
        <h3 class="text-xl font-display font-bold text-gradient mb-4">💰 Estadísticas del Mes</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            <!-- Total Mensual -->
            <div class="stat-card delay-100">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-gemini-purple opacity-10 rounded-full blur-3xl"></div>
                <div class="relative z-10 flex flex-col h-full">
                    <div class="bg-gradient-gemini-purple rounded-2xl p-3 shadow-glow-purple w-fit mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <dl class="flex-1">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Total Mensual</dt>
                        <dd class="text-lg font-display font-bold text-gradient">₡{{ number_format($totalesMes['total_general'], 2) }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Diezmos -->
            <div class="stat-card delay-200">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500 opacity-10 rounded-full blur-3xl"></div>
                <div class="relative z-10 flex flex-col h-full">
                    <div class="bg-gradient-gemini-blue rounded-2xl p-3 shadow-glow-blue w-fit mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <dl class="flex-1">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Diezmos</dt>
                        <dd class="text-lg font-display font-bold bg-gradient-gemini-blue bg-clip-text text-transparent">₡{{ number_format($totalesMes['total_diezmo'], 2) }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Misiones -->
            <div class="stat-card delay-300">
                <div class="absolute top-0 right-0 w-32 h-32 bg-green-500 opacity-10 rounded-full blur-3xl"></div>
                <div class="relative z-10 flex flex-col h-full">
                    <div class="bg-gradient-gemini-success rounded-2xl p-3 shadow-glass w-fit mb-3">
                        <svg class="h-6 w-6 text-green-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <dl class="flex-1">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Misiones</dt>
                        <dd class="text-lg font-display font-bold text-green-600">₡{{ number_format($totalesMes['total_misiones'], 2) }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Construcción -->
            <div class="stat-card delay-400">
                <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500 opacity-10 rounded-full blur-3xl"></div>
                <div class="relative z-10 flex flex-col h-full">
                    <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl p-3 shadow-glass w-fit mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <dl class="flex-1">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Construcción</dt>
                        <dd class="text-lg font-display font-bold text-orange-600">₡{{ number_format($totalesMes['total_construccion'], 2) }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Suelto -->
            <div class="stat-card delay-500">
                <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500 opacity-10 rounded-full blur-3xl"></div>
                <div class="relative z-10 flex flex-col h-full">
                    <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl p-3 shadow-glow-pink w-fit mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <dl class="flex-1">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Suelto</dt>
                        <dd class="text-lg font-display font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">₡{{ number_format($totalesMes['total_suelto'], 2) }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Stats adicionales -->
            <div class="stat-card delay-600">
                <div class="relative z-10 flex flex-col h-full">
                    <div class="bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl p-3 shadow-glass w-fit mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <dl class="flex-1">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Seminario</dt>
                        <dd class="text-lg font-display font-bold text-orange-600">₡{{ number_format($totalesMes['total_seminario'], 2) }}</dd>
                    </dl>
                </div>
            </div>

            <div class="stat-card delay-700">
                <div class="relative z-10 flex flex-col h-full">
                    <div class="bg-gradient-to-br from-pink-500 to-rose-500 rounded-2xl p-3 shadow-glow-pink w-fit mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                        </svg>
                    </div>
                    <dl class="flex-1">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Campamento</dt>
                        <dd class="text-lg font-display font-bold text-pink-600">₡{{ number_format($totalesMes['total_campa'], 2) }}</dd>
                    </dl>
                </div>
            </div>

            <div class="stat-card delay-800">
                <div class="relative z-10 flex flex-col h-full">
                    <div class="bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl p-3 shadow-glass w-fit mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <dl class="flex-1">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Préstamo</dt>
                        <dd class="text-lg font-display font-bold text-red-600">₡{{ number_format($totalesMes['total_prestamo'], 2) }}</dd>
                    </dl>
                </div>
            </div>

            <div class="stat-card delay-900">
                <div class="relative z-10 flex flex-col h-full">
                    <div class="bg-gradient-to-br from-teal-400 to-cyan-500 rounded-2xl p-3 shadow-glow-blue w-fit mb-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <dl class="flex-1">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Micro</dt>
                        <dd class="text-lg font-display font-bold text-teal-600">₡{{ number_format($totalesMes['total_micro'], 2) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div>
        <h3 class="text-xl font-display font-bold text-gradient mb-4">📊 Análisis Visual</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Gráfico de Barras - Ingresos por Culto -->
            <div class="glass-card p-6 animate-scale-in">
                <h3 class="text-lg font-display font-semibold text-gradient mb-4">Ingresos por Culto (Últimos 10)</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="ingresosChart"></canvas>
                </div>
            </div>

            <!-- Gráfico Circular - Distribución por Categorías -->
            <div class="glass-card p-6 animate-scale-in" style="animation-delay: 0.2s;">
                <h3 class="text-lg font-display font-semibold text-gradient mb-4">Distribución por Categorías (Mes Actual)</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="distribucionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Asistencia y Promesas -->
    <div>
        <h3 class="text-xl font-display font-bold text-gradient mb-4">📈 Tendencias</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Línea de Tiempo - Asistencia -->
            <div class="glass-card p-6 animate-scale-in" style="animation-delay: 0.3s;">
                <h3 class="text-lg font-display font-semibold text-gradient mb-4">Tendencia de Asistencia</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="asistenciaChart"></canvas>
                </div>
            </div>

            <!-- Promesas Cumplidas vs Pendientes -->
            <div class="glass-card p-6 animate-scale-in" style="animation-delay: 0.4s;">
                <h3 class="text-lg font-display font-semibold text-gradient mb-4">Estado de Promesas (Mes Actual)</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="promesasChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuración de colores Gemini
    const geminiColors = {
        purple: 'rgba(102, 126, 234, 0.8)',
        purpleBorder: 'rgba(102, 126, 234, 1)',
        pink: 'rgba(240, 147, 251, 0.8)',
        pinkBorder: 'rgba(240, 147, 251, 1)',
        blue: 'rgba(79, 172, 254, 0.8)',
        blueBorder: 'rgba(79, 172, 254, 1)',
        green: 'rgba(150, 230, 161, 0.8)',
        greenBorder: 'rgba(150, 230, 161, 1)',
        gradient: (ctx) => {
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(102, 126, 234, 0.8)');
            gradient.addColorStop(0.5, 'rgba(118, 75, 162, 0.6)');
            gradient.addColorStop(1, 'rgba(240, 147, 251, 0.4)');
            return gradient;
        }
    };

    // Gráfico de Ingresos por Culto
    const ingresosCtx = document.getElementById('ingresosChart').getContext('2d');
    new Chart(ingresosCtx, {
        type: 'bar',
        data: {
            labels: @json($cultosRecientes->map(fn($c) => $c->fecha->format('d/m'))),
            datasets: [{
                label: 'Ingresos Totales',
                data: @json($cultosRecientes->map(fn($c) => $c->totales ? $c->totales->total_general : 0)),
                backgroundColor: geminiColors.gradient(ingresosCtx),
                borderColor: geminiColors.purpleBorder,
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: geminiColors.purple,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(102, 126, 234, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
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
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(14, 165, 233, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });

    // Gráfico de Asistencia
    const asistenciaCtx = document.getElementById('asistenciaChart').getContext('2d');
    const asistenciaGradient = asistenciaCtx.createLinearGradient(0, 0, 0, 300);
    asistenciaGradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
    asistenciaGradient.addColorStop(1, 'rgba(16, 185, 129, 0)');
    
    new Chart(asistenciaCtx, {
        type: 'line',
        data: {
            labels: @json($asistencias->pluck('fecha')),
            datasets: [{
                label: 'Asistencia Total',
                data: @json($asistencias->pluck('total')),
                borderColor: 'rgba(16, 185, 129, 1)',
                backgroundColor: asistenciaGradient,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(16, 185, 129, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
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
                    'rgba(150, 230, 161, 0.9)',
                    'rgba(240, 147, 251, 0.9)'
                ],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });
</script>
@endpush
