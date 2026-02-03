import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'resources/js/order.js',
                    dest: 'js'
                },
                {
                    src: 'resources/js/printThis.js',
                    dest: 'js'
                },
                {
                    src: 'resources/js/jquery.number.min.js',
                    dest: 'js'
                }
            ]
        })
    ],
    resolve: {
        alias: {
            '$': 'jquery'
        }
    }
});