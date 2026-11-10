import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/themes/rose-garden.css', 'resources/css/themes/classic-gold.css', 'resources/css/themes/sage-minimalist.css', 'resources/css/themes/engine.css', 'resources/js/checkin.js'],
            refresh: true,
            fonts: [
                bunny('Inter', {
                    weights: [400, 500, 600, 700],
                }),
                bunny('Playfair Display', {
                    weights: [500, 600, 700],
                    styles: ['normal', 'italic'],
                }),
                bunny('Great Vibes', {
                    weights: [400],
                }),
                // Engine theme fonts (config/themes.php). Not preloaded: the
                // browser only downloads a family when a theme uses it.
                bunny('Cormorant Garamond', { weights: [500, 600], styles: ['normal', 'italic'], preload: false }),
                bunny('Cinzel', { weights: [500, 600], preload: false }),
                bunny('Marcellus', { weights: [400], preload: false }),
                bunny('DM Serif Display', { weights: [400], styles: ['normal', 'italic'], preload: false }),
                bunny('Lora', { weights: [400, 500, 600], styles: ['normal', 'italic'], preload: false }),
                bunny('Josefin Sans', { weights: [300, 400, 600], preload: false }),
                bunny('Montserrat', { weights: [400, 500, 600], preload: false }),
                bunny('Pinyon Script', { weights: [400], preload: false }),
                bunny('Alex Brush', { weights: [400], preload: false }),
                bunny('Parisienne', { weights: [400], preload: false }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
