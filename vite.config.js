import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/app.css',
                'resources/sass/login.scss',
                'resources/sass/productos.scss',
                'resources/sass/settings.scss',
                'resources/sass/alumnos.scss',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/image/background-login-3.svg',
                'resources/image/IVAN.jpg',
                'resources/image/pic-login-3.svg',
            ],
            refresh: true,
        }),
    ],
    build: {
        sourcemap: false, // ❌ No generar archivos .map en producción
    },
});
