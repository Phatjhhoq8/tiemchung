import defaultTheme from 'tailwindcss/defaultTheme';
import flowbitePlugin from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './modules/**/*.blade.php',
        './node_modules/flowbite/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Roboto', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#fff1f2',
                    100: '#ffe4e6',
                    200: '#fecdd3',
                    300: '#fda4af',
                    400: '#fb7185',
                    500: '#c8102e', // Medicare Red
                    600: '#a00d24', // Medicare Red Dark Hover
                    700: '#800a1d',
                    800: '#600716',
                    900: '#40050e',
                    950: '#260207',
                },
                medicare: {
                    red: '#c8102e',
                    hover: '#a00d24',
                    light: '#fff1f2',
                    border: 'rgba(200, 16, 46, 0.15)',
                }
            }
        },
    },
    plugins: [
        flowbitePlugin,
    ],
};
