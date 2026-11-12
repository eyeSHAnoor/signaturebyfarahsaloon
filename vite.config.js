import { fileURLToPath, URL } from 'node:url';
import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

const iconResolver = {
    type: 'component',
    resolve(name) {
        if (name === 'Icon') {
            return {
                name: 'Icon',
                from: '@iconify/vue',
            };
        }
    },
};

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
        AutoImport({
            imports: [
                'vue',
                {
                    '@inertiajs/vue3': ['router', 'useForm', 'usePage'],
                },
                '@vueuse/core',
            ],
            dirs: ['resources/js/composables', 'resources/js/stores'],
            dts: false,
        }),
        Components({
            dirs: ['resources/js/components'],
            deep: true,
            extensions: ['vue'],
            resolvers: [iconResolver],
            dts: false,
        }),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },
});
