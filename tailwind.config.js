import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#eef4ff',
                    100: '#dae6ff',
                    200: '#b9d0ff',
                    300: '#8ab1ff',
                    400: '#5687ff',
                    500: '#2f63fa',
                    600: '#1a48e3',
                    700: '#1639b5',
                    800: '#173392',
                    900: '#172c70',
                },
            },
        },
    },

    plugins: [forms],
};
