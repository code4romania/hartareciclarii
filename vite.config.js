import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import manifestSRI from 'vite-plugin-manifest-sri';
import { createSvgIconsPlugin } from 'vite-plugin-svg-icons';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            detectTls: true,
            refresh: true,
        }),
        // manifestSRI(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        createSvgIconsPlugin({
            iconDirs: [
                path.resolve(process.cwd(), 'resources/svg'),
            ],
            symbolId: 'icon-[name]',
        }),
    ],
});
