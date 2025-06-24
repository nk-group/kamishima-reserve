import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'assets/scss/admin/admin.scss',
                'assets/js/admin.js',
                'assets/scss/customer/customer.scss',
                'assets/js/customer.js'
            ],
            publicDirectory: 'src/public',
            buildDirectory: 'build-vite',
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': 'bootstrap',
        }
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
});
