import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            container: {
                center: true,
                padding: {
                    DEFAULT: '1.5rem',
                    sm: '2rem',
                },
            },
            colors: {
                brand: '#2563EB',
                'brand-hover': '#1D4ED8',
                canvas: '#F8FAFC',
                surface: '#FFFFFF',
                border: '#E5E7EB',
                text: '#0F172A',
                success: '#16A34A',
                warning: '#D97706',
                danger: '#DC2626',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                soft: '0 20px 45px -20px rgba(15, 23, 42, 0.15)',
            },
        },
    },

    plugins: [forms],
};
