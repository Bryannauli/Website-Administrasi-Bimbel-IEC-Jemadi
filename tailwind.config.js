import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: false,
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./resources/**/*.js",
    ],

    theme: {
        extend: {
           fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'brand-blue': '#4263EB', // Biru cerah untuk tombol dan teks
                'brand-red': '#FF5050',  // Merah untuk frame
                'bg-start': '#FFF5F6', // Gradien background (pink-ish)
                'bg-end': '#F3F4FE',   // Gradien background (blue-ish)
            }
        },
    },

    plugins: [forms],
};
