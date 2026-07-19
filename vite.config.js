import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin.css',
                'resources/css/responsive.css',
                'resources/js/app.js',
                'resources/js/admin.js',
                'resources/js/admin-product-gallery.js',
                'resources/js/admin-dashboard.js',
                'resources/js/catalog-page.js',
                'resources/js/checkout.js',
                'resources/js/home-page.js',
                'resources/js/product-page.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
