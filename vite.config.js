import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/artplayer-init.js',
                'resources/js/artplayer-reel-init.js',
                'resources/js/reel-show.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
