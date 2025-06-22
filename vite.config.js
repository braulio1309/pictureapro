import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/fullcalendar.css',
                'resources/css/app.css',
                'resources/css/public.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
});
