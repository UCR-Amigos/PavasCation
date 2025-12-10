<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>IBBP - Iniciar Sesión</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Logo.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/Logo.png') }}">
        <link rel="shortcut icon" href="{{ asset('images/Logo.png') }}">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <!-- Background con imagen del banner -->
        <div class="fixed inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/Banner.png') }}');"></div>
        
        <!-- Overlay con gradientes animados -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none bg-gradient-to-br from-gemini-900/40 via-purple-900/30 to-blue-900/40">
            <!-- Gradientes flotantes -->
            <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-gradient-radial from-gemini-400/10 via-purple-400/5 to-transparent blur-3xl animate-float" style="animation-delay: 0s;"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-gradient-radial from-blue-400/10 via-cyan-400/5 to-transparent blur-3xl animate-float" style="animation-delay: 2s;"></div>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-6 sm:pt-0 relative z-10">
            <!-- Logo principal con animación -->
            <div class="mb-8 text-center animate-fade-in">
                <div class="relative inline-block">
                    <!-- Glow effect detrás del logo -->
                    <div class="absolute inset-0 bg-gradient-gemini-purple blur-3xl opacity-30 animate-glow"></div>
                    <img src="{{ asset('images/Logo.png') }}" alt="IBBP" class="relative w-20 h-20 mx-auto drop-shadow-2xl animate-scale-in">
                </div>
                <!-- Texto con fondo semitransparente y sombras fuertes -->
                <div class="mt-4 inline-block px-6 py-3 rounded-2xl bg-white/95 backdrop-blur-xl shadow-2xl animate-fade-in" style="animation-delay: 0.2s;">
                    <h1 class="text-3xl font-display font-bold text-gradient">IBBP Admin</h1>
                    <p class="text-gray-700 font-medium mt-1">Sistema de Administración</p>
                </div>
            </div>

            <!-- Tarjeta de login con glassmorphism -->
            <div class="w-full sm:max-w-md animate-slide-up" style="animation-delay: 0.4s;">
                <div class="glass-card p-8 shadow-gemini-lg">
                    <!-- Borde superior con gradiente -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-gemini-purple rounded-t-2xl"></div>
                    
                    {{ $slot }}
                </div>

                <!-- Footer -->
                <div class="text-center mt-6 text-sm text-gray-500 animate-fade-in" style="animation-delay: 0.6s;">
                    <p>© {{ date('Y') }} IBBP. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>

        <style>
            @keyframes float {
                0%, 100% {
                    transform: translate(0, 0) scale(1);
                }
                33% {
                    transform: translate(30px, -30px) scale(1.1);
                }
                66% {
                    transform: translate(-20px, 20px) scale(0.9);
                }
            }

            @keyframes glow {
                0%, 100% {
                    opacity: 0.3;
                    transform: scale(1);
                }
                50% {
                    opacity: 0.6;
                    transform: scale(1.2);
                }
            }

            @keyframes fade-in {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes scale-in {
                from {
                    opacity: 0;
                    transform: scale(0.8);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            @keyframes slide-up {
                from {
                    opacity: 0;
                    transform: translateY(40px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-float {
                animation: float 20s ease-in-out infinite;
            }

            .animate-glow {
                animation: glow 3s ease-in-out infinite;
            }

            .animate-fade-in {
                animation: fade-in 0.8s ease-out forwards;
            }

            .animate-scale-in {
                animation: scale-in 0.6s ease-out forwards;
            }

            .animate-slide-up {
                animation: slide-up 0.8s ease-out forwards;
            }

            .bg-grid-pattern {
                background-image: 
                    linear-gradient(to right, rgba(0,0,0,0.1) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(0,0,0,0.1) 1px, transparent 1px);
                background-size: 40px 40px;
            }

            .bg-gradient-radial {
                background: radial-gradient(circle, var(--tw-gradient-stops));
            }
        </style>
    </body>
</html>
