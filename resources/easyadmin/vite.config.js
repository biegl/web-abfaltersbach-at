import { viteCommonjs } from '@originjs/vite-plugin-commonjs';

import { defineConfig } from 'vite';

import { createVuePlugin } from 'vite-plugin-vue2';

import { splitVendorChunkPlugin } from 'vite';

import { resolve, join } from 'path';

export default defineConfig({
    plugins: [createVuePlugin(), viteCommonjs(), splitVendorChunkPlugin()],
    resolve: {
        alias: {
            '@': resolve('src'),
            '~@coreui': resolve(__dirname, 'node_modules/@coreui'),
        },
    },
    build: {
        outDir: join(__dirname, '../../public/admin'),
        rollupOptions: {
            input: {
                main: resolve(__dirname, 'index.html'),
            },
            output: {
                manualChunks: function(id) {
                    if (id.includes('/easyadmin/src/')) {
                        return 'app';
                    }
                },
            },
        },
    },
});
