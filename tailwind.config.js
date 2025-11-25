import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Google Sans', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                gemini: {
                    50: '#f0f4ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#667eea',
                    600: '#5568d3',
                    700: '#4c51bf',
                    800: '#434190',
                    900: '#3c366b',
                },
                purple: {
                    50: '#faf5ff',
                    100: '#f3e8ff',
                    200: '#e9d5ff',
                    300: '#d8b4fe',
                    400: '#c084fc',
                    500: '#764ba2',
                    600: '#653d8b',
                    700: '#5b3472',
                    800: '#4c2a5e',
                    900: '#3f2149',
                },
                pink: {
                    50: '#fdf2f8',
                    100: '#fce7f3',
                    200: '#fbcfe8',
                    300: '#f9a8d4',
                    400: '#f472b6',
                    500: '#f093fb',
                    600: '#db6fe5',
                    700: '#be4bdb',
                    800: '#9c36b5',
                    900: '#7e2a92',
                },
                cyan: {
                    50: '#ecfeff',
                    100: '#cffafe',
                    200: '#a5f3fc',
                    300: '#67e8f9',
                    400: '#22d3ee',
                    500: '#4facfe',
                    600: '#00b8d4',
                    700: '#0097a7',
                    800: '#00838f',
                    900: '#006064',
                },
            },
            backgroundImage: {
                'gradient-gemini': 'linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%)',
                'gradient-gemini-purple': 'linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%)',
                'gradient-gemini-blue': 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                'gradient-gemini-pink': 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                'gradient-gemini-success': 'linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%)',
                'gradient-shine': 'linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent)',
            },
            animation: {
                'fade-in': 'fadeIn 0.6s ease-out',
                'fade-in-up': 'fadeInUp 0.6s ease-out',
                'slide-in-right': 'slideInRight 0.5s ease-out',
                'scale-in': 'scaleIn 0.8s ease-out',
                'bounce-slow': 'bounceSlow 2s ease-in-out infinite',
                'pulse-slow': 'pulseSlow 4s ease-in-out infinite',
                'shine': 'shine 3s infinite',
                'float': 'float 8s ease-in-out infinite',
                'background-pulse': 'backgroundPulse 20s ease-in-out infinite',
                'glow': 'glow 2s ease-in-out infinite alternate',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(30px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInRight: {
                    '0%': { opacity: '0', transform: 'translateX(-30px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.9)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                bounceSlow: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
                pulseSlow: {
                    '0%, 100%': { opacity: '0.5', transform: 'scale(1)' },
                    '50%': { opacity: '0.8', transform: 'scale(1.2)' },
                },
                shine: {
                    '0%': { left: '-100%' },
                    '50%, 100%': { left: '100%' },
                },
                float: {
                    '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
                    '50%': { transform: 'translate(-20px, -20px) scale(1.1)' },
                },
                backgroundPulse: {
                    '0%, 100%': { opacity: '0.3', transform: 'scale(1)' },
                    '50%': { opacity: '0.6', transform: 'scale(1.1)' },
                },
                glow: {
                    '0%': { boxShadow: '0 0 20px rgba(102, 126, 234, 0.3), 0 0 40px rgba(102, 126, 234, 0.2)' },
                    '100%': { boxShadow: '0 0 30px rgba(102, 126, 234, 0.5), 0 0 60px rgba(102, 126, 234, 0.3)' },
                },
            },
            boxShadow: {
                'gemini': '0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1) inset',
                'gemini-lg': '0 30px 80px rgba(102, 126, 234, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1) inset',
                'glass': '0 8px 32px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.2) inset',
                'glow-purple': '0 8px 32px rgba(102, 126, 234, 0.4)',
                'glow-pink': '0 8px 32px rgba(240, 147, 251, 0.4)',
                'glow-blue': '0 8px 32px rgba(79, 172, 254, 0.4)',
            },
            backdropBlur: {
                xs: '2px',
            },
        },
    },

    plugins: [
        forms,
        require('tailwind-scrollbar')({ nocompatible: true }),
    ],
};
