<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>IBBSC - Iniciar Sesión</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Logo.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/Logo.png') }}">
        <link rel="shortcut icon" href="{{ asset('images/Logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-6 sm:pt-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/Banner.png') }}');">
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white/85 backdrop-blur-md shadow-2xl overflow-hidden rounded-xl">
                <div class="mb-6 text-center">
                    <img src="{{ asset('images/Logo.png') }}" alt="IBBSC" class="w-16 h-16 mx-auto mb-3">
                    <h1 class="text-2xl font-bold text-gray-800">IBBSC Admin</h1>
                    <p class="text-sm text-gray-600 mt-1">Sistema de Administración</p>
                </div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
