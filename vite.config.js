import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/property-image-validator.js',
                'resources/css/filament/admin/theme.css',
                'resources/css/filament/agent/theme.css',
                'resources/css/filament/agency/theme.css',
                'resources/css/filament/landlord/theme.css',
                'resources/css/filament/tenant/theme.css'
            ],
            refresh: true,
        }),
    ],
});
