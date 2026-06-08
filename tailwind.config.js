import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', 'Inter', 'SF Pro Display', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                '2xl': '16px',
                '3xl': '20px',
            },
            boxShadow: {
                'card': '0 0 0 0.5px rgba(0,0,0,0.04), 0 1px 4px rgba(0,0,0,0.02), 0 4px 16px rgba(0,0,0,0.02)',
                'card-hover': '0 0 0 0.5px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04), 0 8px 32px rgba(0,0,0,0.03)',
                'dropdown': '0 0 0 0.5px rgba(0,0,0,0.06), 0 20px 60px rgba(0,0,0,0.08)',
                'float': '0 0 0 0.5px rgba(0,0,0,0.06), 0 4px 24px rgba(0,0,0,0.04), 0 8px 48px rgba(0,0,0,0.02)',
            },
            transitionTimingFunction: {
                'spring': 'cubic-bezier(0.16, 1, 0.3, 1)',
            },
        },
    },

    plugins: [forms, typography],
};
