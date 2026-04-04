import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import tailwindcss from "@tailwindcss/vite";
import { resolve } from 'node:path';

export default defineConfig({
    server: {
        host: '0.0.0.0', // Vite écoute partout dans le conteneur
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost', // Mais le navigateur doit essayer de se connecter à localhost
        },
        watch: {
            usePolling: true, // Crucial si tes fichiers sont sur un volume partagé (Windows/WSL)
        },
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
    // server: {
    //     host: '0.0.0.0',
    //     port: 5173,
    //     strictPort: true,
    //     watch: {
    //         usePolling: true,
    //     },
    // }

});
