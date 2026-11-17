import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/themes/rose-garden.css', 'resources/css/themes/classic-gold.css', 'resources/css/themes/sage-minimalist.css', 'resources/css/themes/engine.css', 'resources/js/checkin.js', 'resources/js/canvas-editor.js'],
            refresh: true,
            fonts: [
                // Preloads are emitted per page: @fonts($aliases) keeps only the
                // families that page uses (platform: Inter + Cormorant; invitations:
                // the theme's own three, see ThemeRegistry::fontAliases).
                bunny('Inter', {
                    weights: [400, 500, 600, 700],
                    preload: [{ weight: 400, style: 'normal' }, { weight: 600, style: 'normal' }],
                }),
                bunny('Playfair Display', {
                    weights: [500, 600, 700],
                    styles: ['normal', 'italic'],
                    preload: [{ weight: 600, style: 'normal' }],
                }),
                bunny('Great Vibes', {
                    weights: [400],
                }),
                // Platform display face (400 headlines, 400 italic accents, 600 logo)
                // and an engine theme font.
                bunny('Cormorant Garamond', {
                    weights: [400, 500, 600],
                    styles: ['normal', 'italic'],
                    preload: [{ weight: 400, style: 'normal' }, { weight: 400, style: 'italic' }, { weight: 600, style: 'normal' }],
                }),
                // Engine theme fonts (config/themes.php): one main weight preloaded,
                // emitted only on invitations whose theme uses the family.
                bunny('Cinzel', { weights: [500, 600], preload: [{ weight: 600, style: 'normal' }] }),
                bunny('Marcellus', { weights: [400] }),
                bunny('DM Serif Display', { weights: [400], styles: ['normal', 'italic'], preload: [{ weight: 400, style: 'normal' }] }),
                bunny('Lora', { weights: [400, 500, 600], styles: ['normal', 'italic'], preload: [{ weight: 400, style: 'normal' }] }),
                bunny('Josefin Sans', { weights: [300, 400, 600], preload: [{ weight: 400, style: 'normal' }] }),
                bunny('Montserrat', { weights: [400, 500, 600], preload: [{ weight: 400, style: 'normal' }] }),
                bunny('Pinyon Script', { weights: [400] }),
                bunny('Alex Brush', { weights: [400] }),
                bunny('Parisienne', { weights: [400] }),
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
