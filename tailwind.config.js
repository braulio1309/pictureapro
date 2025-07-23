/** @type {import('tailwindcss').Config} */
export default {
    content: [
        // You will probably also need these lines
        "./resources/**/*.php",
        "./resources/**/**/*.php",
        "./resources/**/**/**/*.php",
        "./resources/**/**/**/**/*.php",
        "./resources/**/**/**/**/**/*.php",
        "./resources/**/**/**/**/**/**/*.php",
        // "./resources/**/**/**/**/**/**/**/*.php",
        "./resources/**/**/*.js",
        "./resources/**/*.js",
        "./app/View/Components/**/**/*.php",
        "./app/Livewire/**/**/*.php",

        // Add mary
        "./vendor/robsontenorio/mary/src/View/Components/**/*.php"
    ],
    safelist: [
        'badge-outline', 'badge-error', 'badge-success', 'badge-accent', 'badge-primary', 'badge-secondary', 'badge-warning', 'badge-info', 'badge-neutral', 'badge-details', 'btn-details'
    ],
    theme: {
        fontFamily: {
            'sans': ['"Fredoka"', 'sans-serif'],
        },
        extend: {
            fontFamily: { 
                'fredoka': ['Fredoka', 'sans-serif']
            },
            colors: {
                'pictureapro-purple': {
                    '50': '#f1f3fc',
                    '100': '#e6e9f9',
                    '200': '#d2d7f3',
                    '300': '#b7bdea',
                    '400': '#999ce0',
                    '500': '#8380d4',
                    '600': '#7166c5',
                    '700': '#5c51a6',
                    '800': '#50478c',
                    '900': '#443f70',
                    '950': '#292541',
                },
                'pictureapro-pink': {
                    '50': '#fcf3f7',
                    '100': '#fbe8f2',
                    '200': '#f9d1e5',
                    '300': '#f294c0',
                    '400': '#ed79ad',
                    '500': '#e4508e',
                    '600': '#d2306c',
                    '700': '#b62053',
                    '800': '#961e45',
                    '900': '#7e1d3d',
                    '950': '#4c0b20',
                },
            }
        },
    },

    plugins: [
        require("@tailwindcss/typography"),
        require("daisyui"),
    ],

    daisyui: {
        themes: [
            // "light",
            {
                pictureapro: {
                    ...require("daisyui/src/theming/themes")["light"],
                    "primary": "#5c51a6",  
                    "secondary": "#9c8bd9",
                    "accent": "#f294c0",
                    "info": "#e371a8ff",
                    "success": "#22c55e",
                    "warning": "#facc15",
                    "error": "#dc2626",
                    "details": "#c66996",
                },
            },
        ],
    },
}
