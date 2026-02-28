import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/stream.js', 'resources/js/watch.js'], 
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        // host: '0.0.0.0',
        // hmr: {
        //     host: '8j4tbfq5-80.asse.devtunnels.ms',
        // }
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        
    },
});
