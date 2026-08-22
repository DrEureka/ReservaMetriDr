import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            colors: {
                ink: {
                    900: '#0F1614',
                    800: '#1A211E',
                    700: '#231E1B',
                    600: '#2A3530',
                },
                bone: {
                    100: '#F0EBE0',
                    200: '#D9D2C5',
                    300: '#B8B0A2',
                    400: '#8A9089',
                },
                brass: {
                    DEFAULT: '#D4A35C',
                    400: '#E0B470',
                    600: '#B8893F',
                },
            },
            fontFamily: {
                display: ['Fraunces', 'Georgia', 'serif'],
                sans: ['Sora', 'Inter', 'system-ui', 'sans-serif'],
                mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
            },
            fontSize: {
                'display-xl': ['3.75rem',   { lineHeight: '1.05', letterSpacing: '-0.025em' }],
                'display-lg': ['2.75rem',   { lineHeight: '1.1',  letterSpacing: '-0.02em'  }],
                'display-md': ['2rem',      { lineHeight: '1.15', letterSpacing: '-0.015em' }],
                'display-sm': ['1.5rem',    { lineHeight: '1.2',  letterSpacing: '-0.01em'  }],
            },
            borderRadius: {
                'sm-1': '2px',
                'sm-2': '4px',
            },
            boxShadow: {
                'card': '0 1px 0 0 rgba(240, 235, 224, 0.04), 0 8px 24px -8px rgba(0, 0, 0, 0.6)',
                'inset-hairline': 'inset 0 0 0 1px rgba(240, 235, 224, 0.08)',
            },
            transitionTimingFunction: {
                'brass': 'cubic-bezier(0.2, 0.7, 0.2, 1)',
            },
        },
    },

    plugins: [forms],
};