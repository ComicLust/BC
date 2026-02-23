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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                dark: {
                    'bg-primary': '#1a1a1a',
                    'bg-secondary': '#2d2d2d',
                    'text-primary': '#ffffff',
                    'text-secondary': '#a0aec0',
                },
            },
        },
    },

    plugins: [forms, require('daisyui')],
};
