<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Separador -->
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">o</span>
        </div>
    </div>

    <!-- Botón Invitado -->
    <form method="POST" action="{{ route('login.guest') }}" id="guestForm">
        @csrf
        <button type="submit" class="w-full flex items-center justify-center px-4 py-3 bg-gray-100 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
            Entrar como Invitado
        </button>
    </form>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-500">
        <div class="text-center">
            <!-- Logo animado -->
            <div class="mb-8 animate-bounce-slow">
                <img src="{{ asset('images/Logo.png') }}" alt="IBBSC" class="w-24 h-24 mx-auto drop-shadow-2xl">
            </div>
            
            <!-- Spinner personalizado -->
            <div class="relative w-20 h-20 mx-auto mb-6">
                <div class="absolute top-0 left-0 w-full h-full border-4 border-white/30 rounded-full"></div>
                <div class="absolute top-0 left-0 w-full h-full border-4 border-t-white border-r-transparent border-b-transparent border-l-transparent rounded-full animate-spin"></div>
            </div>
            
            <!-- Texto animado -->
            <h2 class="text-2xl font-bold text-white mb-2 animate-fade-in">Bienvenido</h2>
            <p class="text-blue-100 animate-pulse">Cargando tu panel...</p>
        </div>
    </div>

    <style>
        @keyframes bounce-slow {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-bounce-slow {
            animation: bounce-slow 2s ease-in-out infinite;
        }
        
        .animate-fade-in {
            animation: fade-in 1s ease-out;
        }
    </style>

    <script>
        // Transición para login normal
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
        });

        // Transición para invitado
        document.getElementById('guestForm').addEventListener('submit', function(e) {
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
        });
    </script>
</x-guest-layout>
