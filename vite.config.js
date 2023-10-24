import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
 
export default defineConfig({
    server: {
        cors: true,
        port: 5575
    },
    build: {
        chunkSizeWarningLimit: 25000,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
            ],
        }),
        vue(
            {
                template: {
                    transformAssetUrls: {
                        includeAbsolute: false,
                    },
                },
            }
        )
    ],
})
