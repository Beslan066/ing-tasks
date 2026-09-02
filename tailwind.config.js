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
                primary: {
                    "50": "rgba(16, 185, 129, 0.05)",
                    "100": "rgba(16, 185, 129, 0.10)",
                    "200": "rgba(16, 185, 129, 0.20)",
                    "300": "rgba(16, 185, 129, 0.40)",
                    "400": "rgba(16, 185, 129, 0.60)",
                    "500": "rgba(16, 185, 129, 0.80)",
                    "600": "#10b981",
                    "700": "#059669",
                    "800": "#047857",
                    "900": "#065f46"
                },
                purple: {
                    "50": "#f0fdf4",
                    "100": "#dcfce7",
                    "200": "#bbf7d0",
                    "300": "#86efac",
                    "400": "#4ade80",
                    "500": "#16a34a",
                    "600": "#15803d",
                    "700": "#166534",
                    "800": "#14532d",
                    "900": "#052e16",
                },
                brown: {
                    "50": "#fdf7f2",
                    "100": "#f9eee5",
                    "200": "#f2d9c5",
                    "300": "#e7bd9a",
                    "400": "#da9c6a",
                    "500": "#c47c45",
                    "600": "#a66238",
                    "700": "#854e2e",
                    "800": "#6a3e25",
                    "900": "#3d2416",
                }
            }
        },
    },

    plugins: [
        require("@tailwindcss/forms")({
            strategy: 'class',
        }),
    ],
};
