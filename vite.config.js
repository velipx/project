import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    resolve: {
        alias: {
            'inertia-modal': path.resolve('vendor/emargareten/inertia-modal'),
        },
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: '0.0.0.0',      // Omogućava Vite serveru da bude dostupan spolja
        port: 5173,           // Vite port, zamenite ako koristite drugi
        hmr: {
            host: '192.168.1.9', // IP adresa vašeg glavnog računara
            port: 5173
        }
    }

});
