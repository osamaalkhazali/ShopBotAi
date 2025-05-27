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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Extending Tailwind's color palette with our CSS variables
                'primary': {
                    DEFAULT: 'var(--primary)',
                    'dark': 'var(--primary-dark)',
                    'light': 'var(--primary-light)',
                    'lighter': 'var(--primary-lighter)',
                    'lightest': 'var(--primary-lightest)',
                },
                'secondary': {
                    DEFAULT: 'var(--secondary)',
                    'dark': 'var(--secondary-dark)',
                    'light': 'var(--secondary-light)',
                    'lighter': 'var(--secondary-lighter)',
                },
                'accent': {
                    DEFAULT: 'var(--accent)',
                    'dark': 'var(--accent-dark)',
                    'light': 'var(--accent-light)',
                },
                'app': {
                    'card': 'var(--card-bg)',
                    'card-light': 'var(--card-bg-light)',
                    'border': 'var(--card-border)',
                    'border-hover': 'var(--card-border-hover)',
                    'input': 'var(--input-bg)',
                    'input-border': 'var(--input-border)',
                }
            },
            textColor: {
                'app': {
                    'light': 'var(--text-light)',
                    'dark': 'var(--text-dark)',
                    'muted': 'var(--text-muted)',
                    'stars': 'var(--stars-color)',
                }
            },
            backgroundColor: {
                'app': {
                    'card': 'var(--card-bg)',
                    'card-light': 'var(--card-bg-light)',
                    'input': 'var(--input-bg)',
                }
            },
            borderColor: {
                'app': {
                    'card': 'var(--card-border)',
                    'card-hover': 'var(--card-border-hover)',
                    'input': 'var(--input-border)',
                }
            },
            boxShadow: {
                'app': '0 4px 6px var(--shadow-color)',
                'app-lg': '0 10px 15px var(--shadow-color-darker)',
                'primary': '0 4px 6px var(--primary-shadow)',
            },
        },
    },

    plugins: [forms],
};
