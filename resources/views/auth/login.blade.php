<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium" />
            <x-text-input id="email" class="input-gemini mt-2" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="tu@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-6">
            <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />
            <x-text-input id="password" class="input-gemini mt-2"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-6">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gemini-300 text-gemini-600 shadow-sm focus:ring-gemini-500 transition-all" name="remember">
                <span class="ms-2 text-sm text-gray-600 group-hover:text-gemini-600 transition-colors">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-gemini-600 hover:text-gemini-700 font-medium transition-colors" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="mt-8">
            <button type="submit" class="btn-gemini w-full justify-center group">
                <span class="relative z-10">{{ __('Log in') }}</span>
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </button>
        </div>
    </form>

    <!-- Separador elegante -->
    <div class="relative my-8">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white text-gray-500 font-medium">o continúa con</span>
        </div>
    </div>

    <!-- Botón Invitado con estilo Gemini -->
    <form method="POST" action="{{ route('login.guest') }}" id="guestForm">
        @csrf
        <button type="submit" class="w-full flex items-center justify-center px-6 py-3 bg-white border-2 border-gemini-200 rounded-xl font-semibold text-sm text-gray-700 hover:bg-gemini-50 hover:border-gemini-400 focus:outline-none focus:ring-4 focus:ring-gemini-500/20 transition-all duration-300 shadow-glass group">
            <svg class="w-5 h-5 mr-3 text-gemini-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
            <span>Entrar como Invitado</span>
        </button>
    </form>

    <!-- Loading Overlay con transición del logo -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gradient-to-br from-gemini-600 via-purple-600 to-blue-600 z-[9999] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-700">
        <!-- Pattern de fondo -->
        <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
        
        <!-- Gradientes flotantes -->
        <div class="absolute top-20 right-20 w-64 h-64 bg-gradient-radial from-white/20 to-transparent blur-3xl animate-float"></div>
        <div class="absolute bottom-20 left-20 w-72 h-72 bg-gradient-radial from-purple-400/20 to-transparent blur-3xl animate-float" style="animation-delay: 1s;"></div>
        
        <div class="text-center relative z-10">
            <!-- Logo con múltiples animaciones -->
            <div class="mb-12 relative">
                <!-- Círculos concéntricos animados -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-32 h-32 border-4 border-white/30 rounded-full animate-ping-slow"></div>
                </div>
                <div class="absolute inset-0 flex items-center justify-center" style="animation-delay: 0.5s;">
                    <div class="w-40 h-40 border-4 border-white/20 rounded-full animate-ping-slow"></div>
                </div>
                
                <!-- Logo central -->
                <div class="relative animate-logo-entry">
                    <div class="absolute inset-0 bg-white/30 blur-2xl rounded-full animate-pulse-glow"></div>
                    <img src="{{ asset('images/Logo.png') }}" alt="IBBSC" class="relative w-28 h-28 mx-auto drop-shadow-2xl">
                </div>
            </div>
            
            <!-- Spinner con diseño Gemini -->
            <div class="relative w-16 h-16 mx-auto mb-8">
                <div class="absolute top-0 left-0 w-full h-full">
                    <div class="w-full h-full border-4 border-white/20 rounded-full"></div>
                </div>
                <div class="absolute top-0 left-0 w-full h-full">
                    <div class="w-full h-full border-4 border-t-white border-r-white/50 border-b-transparent border-l-transparent rounded-full animate-spin-smooth"></div>
                </div>
            </div>
            
            <!-- Texto con animación de entrada -->
            <div class="space-y-3">
                <h2 class="text-3xl font-display font-bold text-white animate-text-reveal">¡Bienvenido!</h2>
                <p class="text-xl text-white/90 animate-text-reveal" style="animation-delay: 0.3s;">Preparando tu panel de control...</p>
                
                <!-- Barra de progreso animada -->
                <div class="w-64 h-1 bg-white/20 rounded-full mx-auto mt-6 overflow-hidden">
                    <div class="h-full bg-white rounded-full animate-progress-bar shadow-glow-white"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes ping-slow {
            0% {
                transform: scale(1);
                opacity: 0.8;
            }
            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        @keyframes pulse-glow {
            0%, 100% {
                opacity: 0.3;
                transform: scale(0.95);
            }
            50% {
                opacity: 0.6;
                transform: scale(1.05);
            }
        }

        @keyframes logo-entry {
            0% {
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }
            60% {
                transform: scale(1.2) rotate(20deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        @keyframes spin-smooth {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes text-reveal {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes progress-bar {
            0% {
                width: 0%;
            }
            100% {
                width: 100%;
            }
        }

        .animate-ping-slow {
            animation: ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        .animate-logo-entry {
            animation: logo-entry 1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .animate-spin-smooth {
            animation: spin-smooth 1.5s linear infinite;
        }

        .animate-text-reveal {
            animation: text-reveal 0.8s ease-out forwards;
            opacity: 0;
        }

        .animate-progress-bar {
            animation: progress-bar 2.5s ease-in-out infinite;
        }

        .shadow-glow-white {
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.8);
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
