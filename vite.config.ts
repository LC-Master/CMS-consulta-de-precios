import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import tsconfigPaths from 'vite-tsconfig-paths';

export default defineConfig({
    // server:{
    //     host: '0.0.0.0',
    //     hmr:{
    //         host: '172.20.3.36'
    //     }
    // },
    plugins: [
        tsconfigPaths(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx', 'resources/css/custom-scrollbar.css'],
            ssr: 'resources/js/ssr.tsx',
            refresh: true,
        }),
        react({
            babel: {
                plugins: ['babel-plugin-react-compiler'],
            },
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
    ],
    esbuild: {
        jsx: 'automatic',
    },
});
