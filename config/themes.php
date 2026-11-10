<?php

/*
| Theme registry (ADR 009).
|
| The themes table holds what the admin controls (active, order, badge,
| thumbnail). This file holds what the code needs: category, color variants
| and, for engine themes, the layout recipe.
|
| Colors are applied at render time as CSS custom properties, so a new
| variant never needs an asset rebuild.
|
| - Handcrafted themes (own folder in resources/views/themes/{slug}) map
|   their own tokens, e.g. 'rg-deep' => '#8a4a52'.
| - Engine themes (resources/views/themes/_engine) use the shared tokens:
|   bg, surface, soft, primary, accent, ink, on (text on primary).
|
| The first variant is the default. Run ThemeSeeder after adding a theme.
*/

return [

    'categories' => [
        'minimalis' => 'Minimalis',
        'floral' => 'Floral',
        'adat' => 'Adat & Tradisional',
        'luxury' => 'Luxury',
        'islami' => 'Islami',
        'modern' => 'Modern',
        'rustic' => 'Rustic & Alam',
    ],

    /*
    | Font stacks used by engine themes. Every family listed here must be
    | registered in vite.config.js (non-preloaded families are fetched by the
    | browser only when a theme actually uses them).
    */
    'fonts' => [
        'playfair' => "'Playfair Display', Georgia, serif",
        'cormorant' => "'Cormorant Garamond', Georgia, serif",
        'cinzel' => "'Cinzel', Georgia, serif",
        'marcellus' => "'Marcellus', Georgia, serif",
        'dm-serif' => "'DM Serif Display', Georgia, serif",
        'lora' => "'Lora', Georgia, serif",
        'inter' => "'Inter', ui-sans-serif, system-ui, sans-serif",
        'josefin' => "'Josefin Sans', ui-sans-serif, system-ui, sans-serif",
        'montserrat' => "'Montserrat', ui-sans-serif, system-ui, sans-serif",
        'great-vibes' => "'Great Vibes', cursive",
        'pinyon' => "'Pinyon Script', cursive",
        'alex-brush' => "'Alex Brush', cursive",
        'parisienne' => "'Parisienne', cursive",
    ],

    /*
    | Font pairs a customer can pick per invitation (engine themes only),
    | stored in invitations.customization.font_pair. Keys of 'fonts'.
    */
    'font_pairs' => [
        'klasik' => ['name' => 'Klasik', 'fonts' => ['playfair', 'great-vibes', 'inter']],
        'anggun' => ['name' => 'Anggun', 'fonts' => ['cormorant', 'pinyon', 'lora']],
        'keraton' => ['name' => 'Keraton', 'fonts' => ['cinzel', 'parisienne', 'cormorant']],
        'modern' => ['name' => 'Modern', 'fonts' => ['dm-serif', 'alex-brush', 'montserrat']],
        'lembut' => ['name' => 'Lembut', 'fonts' => ['marcellus', 'parisienne', 'josefin']],
        'tegas' => ['name' => 'Tegas', 'fonts' => ['cinzel', 'great-vibes', 'montserrat']],
    ],

    /*
    | Engine recipe options (see resources/views/themes/_engine/index.blade.php):
    | hero: arch | photo | editorial | frame
    | shape: arch | circle | rounded | square
    | heading: script | serif | caps
    | sections: cards | plain | bordered
    | ornament: floral | leaf | kawung | parang | cloud | lotus | tenun | songket | deco | star | wave | line
    | pattern: none | kawung | parang | cloud | tenun | songket | star | deco | dots | leaf | wave
    | fonts: [heading, script, body] keys from 'fonts'
    */
    'themes' => [

        // Handcrafted themes.
        'rose-garden' => [
            'category' => 'floral',
            'variants' => [
                'mawar' => ['name' => 'Mawar', 'colors' => []],
                'dusty' => ['name' => 'Dusty Pink', 'colors' => ['rg-cream' => '#fbf6f4', 'rg-blush' => '#efdcdc', 'rg-rose' => '#b88a8f', 'rg-deep' => '#7a4f57', 'rg-ink' => '#43363a', 'rg-gold' => '#a8896a']],
                'peach' => ['name' => 'Peach', 'colors' => ['rg-cream' => '#fff8f3', 'rg-blush' => '#fbe3d6', 'rg-rose' => '#e0957a', 'rg-deep' => '#a3563f', 'rg-ink' => '#4d3229', 'rg-gold' => '#c39a62']],
            ],
            'swatch' => ['#fdf7f5', '#8a4a52', '#b8955a'],
        ],
        'classic-gold' => [
            'category' => 'luxury',
            'variants' => [
                'emas' => ['name' => 'Emas', 'colors' => []],
                'navy' => ['name' => 'Navy', 'colors' => ['cg-cream' => '#f6f5f1', 'cg-blush' => '#e5e3db', 'cg-rose' => '#9a7b3c', 'cg-deep' => '#17223b', 'cg-ink' => '#1f2738', 'cg-gold' => '#b8955a']],
                'zamrud' => ['name' => 'Zamrud', 'colors' => ['cg-cream' => '#f5f6f1', 'cg-blush' => '#e3e8dc', 'cg-rose' => '#9c8442', 'cg-deep' => '#173a2e', 'cg-ink' => '#1d2e27', 'cg-gold' => '#b9975b']],
            ],
            'swatch' => ['#faf7f0', '#1f1b16', '#b8955a'],
        ],
        'sage-minimalist' => [
            'category' => 'minimalis',
            'variants' => [
                'sage' => ['name' => 'Sage', 'colors' => []],
                'pasir' => ['name' => 'Pasir', 'colors' => ['sm-cream' => '#f9f6f1', 'sm-blush' => '#ece4d8', 'sm-rose' => '#a88e6c', 'sm-deep' => '#5a4a39', 'sm-ink' => '#3a3129', 'sm-gold' => '#b39e78']],
                'abu' => ['name' => 'Abu Biru', 'colors' => ['sm-cream' => '#f5f7f8', 'sm-blush' => '#e1e7ea', 'sm-rose' => '#7d93a0', 'sm-deep' => '#3e505b', 'sm-ink' => '#2c363d', 'sm-gold' => '#9fa7a0']],
            ],
            'swatch' => ['#f7f8f4', '#44564a', '#a7a37c'],
        ],

        // Engine themes: Adat & Tradisional.
        'kawung-ayu' => [
            'name' => 'Kawung Ayu', 'category' => 'adat', 'sort' => 10,
            'description' => 'Motif kawung dengan warna sogan khas Jawa yang hangat dan anggun.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'bordered', 'ornament' => 'kawung', 'pattern' => 'kawung', 'fonts' => ['cinzel', 'pinyon', 'lora']],
            'variants' => [
                'sogan' => ['name' => 'Sogan', 'colors' => ['bg' => '#f7f0e4', 'surface' => '#fffaf2', 'soft' => '#ecdcc2', 'primary' => '#6b4423', 'accent' => '#b08a4f', 'ink' => '#3b2a1c', 'on' => '#fffaf2']],
                'hitam' => ['name' => 'Hitam Emas', 'colors' => ['bg' => '#1d1813', 'surface' => '#28211a', 'soft' => '#342a20', 'primary' => '#c9a463', 'accent' => '#c9a463', 'ink' => '#f1e6d2', 'on' => '#1d1813']],
                'hijau' => ['name' => 'Hijau Pupus', 'colors' => ['bg' => '#f3f1e6', 'surface' => '#fbfaf3', 'soft' => '#dfe0c8', 'primary' => '#4c5a32', 'accent' => '#a68c4e', 'ink' => '#2f3322', 'on' => '#fbfaf3']],
            ],
        ],
        'parang-sekar' => [
            'name' => 'Parang Sekar', 'category' => 'adat', 'sort' => 11,
            'description' => 'Garis parang yang tegas berpadu bingkai klasik keraton.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'caps', 'sections' => 'cards', 'ornament' => 'parang', 'pattern' => 'parang', 'fonts' => ['cinzel', 'great-vibes', 'cormorant']],
            'variants' => [
                'coklat' => ['name' => 'Coklat Keraton', 'colors' => ['bg' => '#f4ece0', 'surface' => '#fffaf3', 'soft' => '#e6d5bb', 'primary' => '#5a3a22', 'accent' => '#a37b3f', 'ink' => '#33241a', 'on' => '#fffaf3']],
                'merah' => ['name' => 'Merah Bata', 'colors' => ['bg' => '#f6ede6', 'surface' => '#fffaf6', 'soft' => '#ead3c6', 'primary' => '#7e2f22', 'accent' => '#b38443', 'ink' => '#3b2019', 'on' => '#fffaf6']],
                'biru' => ['name' => 'Biru Indigo', 'colors' => ['bg' => '#eef0f4', 'surface' => '#fafbfd', 'soft' => '#d6dbe6', 'primary' => '#24345a', 'accent' => '#a98a4e', 'ink' => '#1d2436', 'on' => '#fafbfd']],
            ],
        ],
        'mega-mendung' => [
            'name' => 'Mega Mendung', 'category' => 'adat', 'sort' => 12,
            'description' => 'Awan berlapis khas Cirebon dengan gradasi biru yang menenangkan.',
            'engine' => ['hero' => 'photo', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'cloud', 'pattern' => 'cloud', 'fonts' => ['marcellus', 'parisienne', 'lora']],
            'variants' => [
                'biru' => ['name' => 'Biru Laut', 'colors' => ['bg' => '#eef3f8', 'surface' => '#ffffff', 'soft' => '#d4e2ef', 'primary' => '#1f4e79', 'accent' => '#c0392b', 'ink' => '#1b2a3a', 'on' => '#ffffff']],
                'merah' => ['name' => 'Merah Cirebon', 'colors' => ['bg' => '#f8efed', 'surface' => '#ffffff', 'soft' => '#efd6d1', 'primary' => '#8e2a24', 'accent' => '#1f4e79', 'ink' => '#3a1d1a', 'on' => '#ffffff']],
                'hijau' => ['name' => 'Hijau Toska', 'colors' => ['bg' => '#ecf5f3', 'surface' => '#ffffff', 'soft' => '#cfe6e1', 'primary' => '#1d5f58', 'accent' => '#b5893f', 'ink' => '#173331', 'on' => '#ffffff']],
            ],
        ],
        'pasundan-asri' => [
            'name' => 'Pasundan Asri', 'category' => 'adat', 'sort' => 13,
            'description' => 'Sejuk dan asri bernuansa Sunda, dengan sentuhan daun dan bambu.',
            'engine' => ['hero' => 'editorial', 'shape' => 'rounded', 'heading' => 'script', 'sections' => 'plain', 'ornament' => 'leaf', 'pattern' => 'leaf', 'fonts' => ['cormorant', 'alex-brush', 'josefin']],
            'variants' => [
                'hijau' => ['name' => 'Hijau Bambu', 'colors' => ['bg' => '#f2f4ec', 'surface' => '#fbfcf7', 'soft' => '#dde4cf', 'primary' => '#3f5a36', 'accent' => '#9d8a52', 'ink' => '#2a3326', 'on' => '#fbfcf7']],
                'krem' => ['name' => 'Krem Kawung', 'colors' => ['bg' => '#f8f3ea', 'surface' => '#fffdf8', 'soft' => '#ebe1cd', 'primary' => '#6f5634', 'accent' => '#8e9b5a', 'ink' => '#3a2f22', 'on' => '#fffdf8']],
                'biru' => ['name' => 'Biru Kabut', 'colors' => ['bg' => '#f0f3f4', 'surface' => '#fbfcfc', 'soft' => '#d8e1e3', 'primary' => '#35535c', 'accent' => '#9a8a5e', 'ink' => '#243236', 'on' => '#fbfcfc']],
            ],
        ],
        'puspa-dewata' => [
            'name' => 'Puspa Dewata', 'category' => 'adat', 'sort' => 14,
            'description' => 'Teratai dan warna tanah liat, terinspirasi keindahan Pulau Dewata.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'lotus', 'pattern' => 'dots', 'fonts' => ['cinzel', 'great-vibes', 'lora']],
            'variants' => [
                'terakota' => ['name' => 'Terakota', 'colors' => ['bg' => '#f7eee6', 'surface' => '#fffaf5', 'soft' => '#efd8c6', 'primary' => '#9a4a2c', 'accent' => '#c8a14f', 'ink' => '#3f241a', 'on' => '#fffaf5']],
                'putih' => ['name' => 'Putih Kuning', 'colors' => ['bg' => '#fbf9f2', 'surface' => '#ffffff', 'soft' => '#f1ead2', 'primary' => '#6b5a2c', 'accent' => '#d4a63b', 'ink' => '#3a321c', 'on' => '#ffffff']],
                'hitam' => ['name' => 'Hitam Poleng', 'colors' => ['bg' => '#1a1a18', 'surface' => '#242420', 'soft' => '#2f2e28', 'primary' => '#d6b25e', 'accent' => '#d6b25e', 'ink' => '#f1ecdf', 'on' => '#1a1a18']],
            ],
        ],
        'ulos-tenun' => [
            'name' => 'Ulos Tenun', 'category' => 'adat', 'sort' => 15,
            'description' => 'Garis tenun tegas berwarna merah, hitam, dan putih khas Batak.',
            'engine' => ['hero' => 'editorial', 'shape' => 'square', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'tenun', 'pattern' => 'tenun', 'fonts' => ['marcellus', 'great-vibes', 'montserrat']],
            'variants' => [
                'merah' => ['name' => 'Merah Ulos', 'colors' => ['bg' => '#f6f0ec', 'surface' => '#fffbf8', 'soft' => '#ecd9d2', 'primary' => '#7d1f1a', 'accent' => '#1f1a18', 'ink' => '#2b1c19', 'on' => '#fffbf8']],
                'hitam' => ['name' => 'Hitam', 'colors' => ['bg' => '#171515', 'surface' => '#221f1f', 'soft' => '#2d2828', 'primary' => '#c7362b', 'accent' => '#e9e1d8', 'ink' => '#efe8e1', 'on' => '#ffffff']],
                'emas' => ['name' => 'Emas', 'colors' => ['bg' => '#f8f3ea', 'surface' => '#fffdf8', 'soft' => '#ede1c9', 'primary' => '#5b2a1f', 'accent' => '#b78b3d', 'ink' => '#2e1e17', 'on' => '#fffdf8']],
            ],
        ],
        'songket-minang' => [
            'name' => 'Songket Minang', 'category' => 'adat', 'sort' => 16,
            'description' => 'Kilau songket emas di atas warna marun yang megah.',
            'engine' => ['hero' => 'frame', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'songket', 'pattern' => 'songket', 'fonts' => ['cinzel', 'pinyon', 'cormorant']],
            'variants' => [
                'marun' => ['name' => 'Marun', 'colors' => ['bg' => '#3a1218', 'surface' => '#4a1a21', 'soft' => '#56212a', 'primary' => '#d8b46a', 'accent' => '#d8b46a', 'ink' => '#f6e9d3', 'on' => '#3a1218']],
                'hitam' => ['name' => 'Hitam', 'colors' => ['bg' => '#16120e', 'surface' => '#211b15', 'soft' => '#2b231b', 'primary' => '#d4af63', 'accent' => '#d4af63', 'ink' => '#f2e7d2', 'on' => '#16120e']],
                'krem' => ['name' => 'Krem Emas', 'colors' => ['bg' => '#f8f1e3', 'surface' => '#fffaf0', 'soft' => '#eddcb8', 'primary' => '#6e1f27', 'accent' => '#b88d3e', 'ink' => '#36191c', 'on' => '#fffaf0']],
            ],
        ],

        // Floral.
        'peony-blush' => [
            'name' => 'Peony Blush', 'category' => 'floral', 'sort' => 20,
            'description' => 'Rangkaian peony merah muda yang manis dan ceria.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['playfair', 'great-vibes', 'inter']],
            'variants' => [
                'pink' => ['name' => 'Pink', 'colors' => ['bg' => '#fdf4f5', 'surface' => '#ffffff', 'soft' => '#f8dfe3', 'primary' => '#b4566a', 'accent' => '#d79aa6', 'ink' => '#4a2c33', 'on' => '#ffffff']],
                'koral' => ['name' => 'Koral', 'colors' => ['bg' => '#fff6f2', 'surface' => '#ffffff', 'soft' => '#fbe0d5', 'primary' => '#c45b45', 'accent' => '#e9a38f', 'ink' => '#4d2c24', 'on' => '#ffffff']],
                'ungu' => ['name' => 'Ungu Muda', 'colors' => ['bg' => '#f8f4fb', 'surface' => '#ffffff', 'soft' => '#e8ddf1', 'primary' => '#7c5a9b', 'accent' => '#b9a0d0', 'ink' => '#3a2c47', 'on' => '#ffffff']],
            ],
        ],
        'lavender-field' => [
            'name' => 'Lavender Field', 'category' => 'floral', 'sort' => 21,
            'description' => 'Ladang lavender yang lembut dengan tata letak ala majalah.',
            'engine' => ['hero' => 'editorial', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'plain', 'ornament' => 'leaf', 'pattern' => 'none', 'fonts' => ['cormorant', 'parisienne', 'josefin']],
            'variants' => [
                'lavender' => ['name' => 'Lavender', 'colors' => ['bg' => '#f6f4fa', 'surface' => '#ffffff', 'soft' => '#e4def0', 'primary' => '#5f4d86', 'accent' => '#a99ac9', 'ink' => '#2f2a3d', 'on' => '#ffffff']],
                'lilac' => ['name' => 'Lilac', 'colors' => ['bg' => '#faf4f8', 'surface' => '#ffffff', 'soft' => '#f0dde9', 'primary' => '#8a4f78', 'accent' => '#c79ab9', 'ink' => '#3d2737', 'on' => '#ffffff']],
                'abu' => ['name' => 'Abu Lembut', 'colors' => ['bg' => '#f5f5f6', 'surface' => '#ffffff', 'soft' => '#e3e3e8', 'primary' => '#4f5065', 'accent' => '#a3a0b8', 'ink' => '#2b2c37', 'on' => '#ffffff']],
            ],
        ],
        'wildflower-meadow' => [
            'name' => 'Wildflower Meadow', 'category' => 'floral', 'sort' => 22,
            'description' => 'Bunga liar warna-warni untuk pernikahan yang hangat dan santai.',
            'engine' => ['hero' => 'photo', 'shape' => 'circle', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'floral', 'pattern' => 'dots', 'fonts' => ['lora', 'alex-brush', 'inter']],
            'variants' => [
                'cerah' => ['name' => 'Cerah', 'colors' => ['bg' => '#fbf8f0', 'surface' => '#ffffff', 'soft' => '#f2ead3', 'primary' => '#a3502f', 'accent' => '#d8a53c', 'ink' => '#3c2d22', 'on' => '#ffffff']],
                'pastel' => ['name' => 'Pastel', 'colors' => ['bg' => '#f9f7f9', 'surface' => '#ffffff', 'soft' => '#ece5ee', 'primary' => '#80607e', 'accent' => '#9fb59a', 'ink' => '#382d38', 'on' => '#ffffff']],
                'senja' => ['name' => 'Senja', 'colors' => ['bg' => '#fbf3ee', 'surface' => '#ffffff', 'soft' => '#f3dccf', 'primary' => '#b2513b', 'accent' => '#7d8c55', 'ink' => '#41271f', 'on' => '#ffffff']],
            ],
        ],
        'magnolia-white' => [
            'name' => 'Magnolia White', 'category' => 'floral', 'sort' => 23,
            'description' => 'Magnolia putih dan hijau sage dalam bingkai yang bersih.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'bordered', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['cormorant', 'pinyon', 'lora']],
            'variants' => [
                'sage' => ['name' => 'Sage', 'colors' => ['bg' => '#f6f7f2', 'surface' => '#ffffff', 'soft' => '#e3e8dc', 'primary' => '#56684f', 'accent' => '#b4a77d', 'ink' => '#2f382c', 'on' => '#ffffff']],
                'gading' => ['name' => 'Gading', 'colors' => ['bg' => '#faf8f3', 'surface' => '#ffffff', 'soft' => '#efe9db', 'primary' => '#76643f', 'accent' => '#c3ab73', 'ink' => '#3a3222', 'on' => '#ffffff']],
                'biru' => ['name' => 'Biru Pucat', 'colors' => ['bg' => '#f4f7f9', 'surface' => '#ffffff', 'soft' => '#dfe8ee', 'primary' => '#4a6577', 'accent' => '#a8b8c2', 'ink' => '#28343c', 'on' => '#ffffff']],
            ],
        ],

        // Luxury.
        'noir-gilded' => [
            'name' => 'Noir Gilded', 'category' => 'luxury', 'sort' => 30,
            'description' => 'Hitam pekat dengan garis art deco emas yang dramatis.',
            'engine' => ['hero' => 'photo', 'shape' => 'square', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'deco', 'pattern' => 'deco', 'fonts' => ['cinzel', 'pinyon', 'montserrat']],
            'variants' => [
                'emas' => ['name' => 'Emas', 'colors' => ['bg' => '#111111', 'surface' => '#1a1a1a', 'soft' => '#222222', 'primary' => '#d4af37', 'accent' => '#d4af37', 'ink' => '#eeeae0', 'on' => '#111111']],
                'perak' => ['name' => 'Perak', 'colors' => ['bg' => '#121417', 'surface' => '#1b1e22', 'soft' => '#24282d', 'primary' => '#c9ced6', 'accent' => '#c9ced6', 'ink' => '#eceef1', 'on' => '#121417']],
                'rosegold' => ['name' => 'Rose Gold', 'colors' => ['bg' => '#161112', 'surface' => '#21191a', 'soft' => '#2b2123', 'primary' => '#d9a38f', 'accent' => '#d9a38f', 'ink' => '#f3e7e3', 'on' => '#161112']],
            ],
        ],
        'emerald-velvet' => [
            'name' => 'Emerald Velvet', 'category' => 'luxury', 'sort' => 31,
            'description' => 'Hijau zamrud seperti beludru dengan aksen emas.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'bordered', 'ornament' => 'deco', 'pattern' => 'deco', 'fonts' => ['dm-serif', 'great-vibes', 'lora']],
            'variants' => [
                'zamrud' => ['name' => 'Zamrud', 'colors' => ['bg' => '#0f2a22', 'surface' => '#14352b', 'soft' => '#1a4034', 'primary' => '#d6b46a', 'accent' => '#d6b46a', 'ink' => '#efe8d6', 'on' => '#0f2a22']],
                'safir' => ['name' => 'Safir', 'colors' => ['bg' => '#0f1a33', 'surface' => '#142241', 'soft' => '#1a2b4f', 'primary' => '#d6b46a', 'accent' => '#d6b46a', 'ink' => '#ece8dc', 'on' => '#0f1a33']],
                'ruby' => ['name' => 'Ruby', 'colors' => ['bg' => '#2e0f16', 'surface' => '#3b141d', 'soft' => '#481a25', 'primary' => '#dcb877', 'accent' => '#dcb877', 'ink' => '#f4e8dc', 'on' => '#2e0f16']],
            ],
        ],
        'marble-ivory' => [
            'name' => 'Marble Ivory', 'category' => 'luxury', 'sort' => 32,
            'description' => 'Gading dan abu marmer dengan sentuhan emas tipis.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'caps', 'sections' => 'cards', 'ornament' => 'line', 'pattern' => 'none', 'fonts' => ['cinzel', 'parisienne', 'cormorant']],
            'variants' => [
                'gading' => ['name' => 'Gading', 'colors' => ['bg' => '#f7f5f0', 'surface' => '#ffffff', 'soft' => '#ebe7de', 'primary' => '#3d3a35', 'accent' => '#b59a62', 'ink' => '#2c2a26', 'on' => '#ffffff']],
                'abu' => ['name' => 'Abu Marmer', 'colors' => ['bg' => '#f2f2f1', 'surface' => '#ffffff', 'soft' => '#e2e2df', 'primary' => '#4a4a48', 'accent' => '#a8a8a2', 'ink' => '#2b2b2a', 'on' => '#ffffff']],
                'champagne' => ['name' => 'Champagne', 'colors' => ['bg' => '#f8f3ea', 'surface' => '#fffdf8', 'soft' => '#eee3cf', 'primary' => '#6b5639', 'accent' => '#c5a46a', 'ink' => '#382d20', 'on' => '#fffdf8']],
            ],
        ],
        'burgundy-royale' => [
            'name' => 'Burgundy Royale', 'category' => 'luxury', 'sort' => 33,
            'description' => 'Burgundy pekat dengan rangkaian bunga yang elegan.',
            'engine' => ['hero' => 'photo', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['playfair', 'pinyon', 'lora']],
            'variants' => [
                'burgundy' => ['name' => 'Burgundy', 'colors' => ['bg' => '#f8f0f0', 'surface' => '#ffffff', 'soft' => '#ecd7d8', 'primary' => '#6d1d2b', 'accent' => '#b88a5a', 'ink' => '#36181d', 'on' => '#ffffff']],
                'gelap' => ['name' => 'Gelap', 'colors' => ['bg' => '#2a0f15', 'surface' => '#36141c', 'soft' => '#431a23', 'primary' => '#e4c08a', 'accent' => '#e4c08a', 'ink' => '#f6e9e0', 'on' => '#2a0f15']],
                'plum' => ['name' => 'Plum', 'colors' => ['bg' => '#f6f0f4', 'surface' => '#ffffff', 'soft' => '#e8d7e2', 'primary' => '#5b2348', 'accent' => '#b48d64', 'ink' => '#33182b', 'on' => '#ffffff']],
            ],
        ],

        // Minimalis & Modern.
        'monochrome-edit' => [
            'name' => 'Monochrome Edit', 'category' => 'modern', 'sort' => 40,
            'description' => 'Hitam putih bergaya editorial dengan tipografi besar.',
            'engine' => ['hero' => 'editorial', 'shape' => 'square', 'heading' => 'caps', 'sections' => 'plain', 'ornament' => 'line', 'pattern' => 'none', 'fonts' => ['dm-serif', 'parisienne', 'montserrat']],
            'variants' => [
                'putih' => ['name' => 'Putih', 'colors' => ['bg' => '#ffffff', 'surface' => '#f6f6f6', 'soft' => '#eeeeee', 'primary' => '#111111', 'accent' => '#7a7a7a', 'ink' => '#1a1a1a', 'on' => '#ffffff']],
                'hitam' => ['name' => 'Hitam', 'colors' => ['bg' => '#0e0e0e', 'surface' => '#181818', 'soft' => '#202020', 'primary' => '#f2f2f2', 'accent' => '#9a9a9a', 'ink' => '#ededed', 'on' => '#0e0e0e']],
                'krem' => ['name' => 'Krem', 'colors' => ['bg' => '#f5f1ea', 'surface' => '#fbf9f5', 'soft' => '#e9e3d8', 'primary' => '#1d1b18', 'accent' => '#8c8070', 'ink' => '#24221e', 'on' => '#fbf9f5']],
            ],
        ],
        'terracotta-sun' => [
            'name' => 'Terracotta Sun', 'category' => 'modern', 'sort' => 41,
            'description' => 'Bentuk lengkung dan warna terakota yang hangat dan modern.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'line', 'pattern' => 'dots', 'fonts' => ['dm-serif', 'alex-brush', 'josefin']],
            'variants' => [
                'terakota' => ['name' => 'Terakota', 'colors' => ['bg' => '#f8efe7', 'surface' => '#fffaf6', 'soft' => '#f0d9c8', 'primary' => '#b2532f', 'accent' => '#e0a26e', 'ink' => '#43271b', 'on' => '#fffaf6']],
                'mustard' => ['name' => 'Mustard', 'colors' => ['bg' => '#faf4e6', 'surface' => '#fffcf4', 'soft' => '#f1e2bd', 'primary' => '#9a6a12', 'accent' => '#cf9f3c', 'ink' => '#3d2e14', 'on' => '#fffcf4']],
                'zaitun' => ['name' => 'Zaitun', 'colors' => ['bg' => '#f3f2e8', 'surface' => '#fbfbf5', 'soft' => '#e0dfc8', 'primary' => '#5d6131', 'accent' => '#b68b4f', 'ink' => '#2f301c', 'on' => '#fbfbf5']],
            ],
        ],
        'dusty-blue' => [
            'name' => 'Dusty Blue', 'category' => 'minimalis', 'sort' => 42,
            'description' => 'Biru berdebu yang kalem dengan ruang lega dan rapi.',
            'engine' => ['hero' => 'arch', 'shape' => 'circle', 'heading' => 'serif', 'sections' => 'plain', 'ornament' => 'line', 'pattern' => 'none', 'fonts' => ['cormorant', 'great-vibes', 'inter']],
            'variants' => [
                'biru' => ['name' => 'Biru', 'colors' => ['bg' => '#f3f6f8', 'surface' => '#ffffff', 'soft' => '#dce5ec', 'primary' => '#4d6a82', 'accent' => '#9fb3c4', 'ink' => '#26333e', 'on' => '#ffffff']],
                'navy' => ['name' => 'Navy', 'colors' => ['bg' => '#f2f4f7', 'surface' => '#ffffff', 'soft' => '#d9dfe8', 'primary' => '#253b5b', 'accent' => '#b7a27a', 'ink' => '#1f2938', 'on' => '#ffffff']],
                'sage' => ['name' => 'Sage Biru', 'colors' => ['bg' => '#f2f6f5', 'surface' => '#ffffff', 'soft' => '#d9e5e2', 'primary' => '#4f7069', 'accent' => '#a6bdb6', 'ink' => '#26332f', 'on' => '#ffffff']],
            ],
        ],
        'linen-ink' => [
            'name' => 'Linen & Ink', 'category' => 'minimalis', 'sort' => 43,
            'description' => 'Warna linen dan tinta hitam, sederhana namun berkarakter.',
            'engine' => ['hero' => 'editorial', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'bordered', 'ornament' => 'line', 'pattern' => 'none', 'fonts' => ['lora', 'parisienne', 'inter']],
            'variants' => [
                'linen' => ['name' => 'Linen', 'colors' => ['bg' => '#f4efe6', 'surface' => '#faf7f1', 'soft' => '#e7dfd1', 'primary' => '#22201c', 'accent' => '#9b8a6f', 'ink' => '#2a2722', 'on' => '#faf7f1']],
                'kertas' => ['name' => 'Kertas', 'colors' => ['bg' => '#fbfaf7', 'surface' => '#ffffff', 'soft' => '#efece5', 'primary' => '#2c2c2c', 'accent' => '#b0a48f', 'ink' => '#2c2c2c', 'on' => '#ffffff']],
                'kopi' => ['name' => 'Kopi Susu', 'colors' => ['bg' => '#efe6da', 'surface' => '#f8f2ea', 'soft' => '#e0d2bf', 'primary' => '#4a3524', 'accent' => '#a3825e', 'ink' => '#33261b', 'on' => '#f8f2ea']],
            ],
        ],

        // Islami.
        'nur-hijrah' => [
            'name' => 'Nur Hijrah', 'category' => 'islami', 'sort' => 50,
            'description' => 'Geometri bintang islami dengan nuansa hijau dan emas yang teduh.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'star', 'pattern' => 'star', 'fonts' => ['marcellus', 'pinyon', 'lora']],
            'variants' => [
                'hijau' => ['name' => 'Hijau', 'colors' => ['bg' => '#f1f5f0', 'surface' => '#ffffff', 'soft' => '#d9e6d6', 'primary' => '#24543d', 'accent' => '#b8964e', 'ink' => '#1c2e24', 'on' => '#ffffff']],
                'putih' => ['name' => 'Putih Emas', 'colors' => ['bg' => '#fbf9f4', 'surface' => '#ffffff', 'soft' => '#f0eadb', 'primary' => '#6b5530', 'accent' => '#c3a15c', 'ink' => '#33291b', 'on' => '#ffffff']],
                'biru' => ['name' => 'Biru Teduh', 'colors' => ['bg' => '#f0f4f6', 'surface' => '#ffffff', 'soft' => '#d6e2e8', 'primary' => '#1f4a5e', 'accent' => '#b8964e', 'ink' => '#1a2c35', 'on' => '#ffffff']],
            ],
        ],
        'lentera-malam' => [
            'name' => 'Lentera Malam', 'category' => 'islami', 'sort' => 51,
            'description' => 'Malam biru tua dengan ornamen geometri berkilau seperti lentera.',
            'engine' => ['hero' => 'photo', 'shape' => 'arch', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'star', 'pattern' => 'star', 'fonts' => ['cinzel', 'great-vibes', 'cormorant']],
            'variants' => [
                'navy' => ['name' => 'Navy', 'colors' => ['bg' => '#0f1830', 'surface' => '#16213d', 'soft' => '#1c2a4b', 'primary' => '#e0c27c', 'accent' => '#e0c27c', 'ink' => '#ece6d6', 'on' => '#0f1830']],
                'hijau' => ['name' => 'Hijau Malam', 'colors' => ['bg' => '#0e2620', 'surface' => '#14312a', 'soft' => '#1a3c33', 'primary' => '#e0c27c', 'accent' => '#e0c27c', 'ink' => '#ebe6d6', 'on' => '#0e2620']],
                'ungu' => ['name' => 'Ungu Malam', 'colors' => ['bg' => '#1d1430', 'surface' => '#271b3e', 'soft' => '#30224b', 'primary' => '#e3c584', 'accent' => '#e3c584', 'ink' => '#eee7dc', 'on' => '#1d1430']],
            ],
        ],

        // Rustic & Alam.
        'rustic-kraft' => [
            'name' => 'Rustic Kraft', 'category' => 'rustic', 'sort' => 60,
            'description' => 'Kertas kraft, daun kering, dan suasana pesta kebun.',
            'engine' => ['hero' => 'arch', 'shape' => 'rounded', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'leaf', 'pattern' => 'leaf', 'fonts' => ['lora', 'alex-brush', 'josefin']],
            'variants' => [
                'kraft' => ['name' => 'Kraft', 'colors' => ['bg' => '#efe4d3', 'surface' => '#f8f1e6', 'soft' => '#e2d1b7', 'primary' => '#5b4129', 'accent' => '#8a8a4e', 'ink' => '#3a2a1c', 'on' => '#f8f1e6']],
                'hijau' => ['name' => 'Hijau Hutan', 'colors' => ['bg' => '#eef0e6', 'surface' => '#f8f9f2', 'soft' => '#d9dec9', 'primary' => '#36492d', 'accent' => '#a3814d', 'ink' => '#263020', 'on' => '#f8f9f2']],
                'karat' => ['name' => 'Karat', 'colors' => ['bg' => '#f4e9de', 'surface' => '#fbf5ee', 'soft' => '#e9d3bf', 'primary' => '#8b4325', 'accent' => '#7c7f4c', 'ink' => '#3c2418', 'on' => '#fbf5ee']],
            ],
        ],
        'pantai-senja' => [
            'name' => 'Pantai Senja', 'category' => 'rustic', 'sort' => 61,
            'description' => 'Ombak dan langit senja untuk pernikahan di tepi pantai.',
            'engine' => ['hero' => 'photo', 'shape' => 'rounded', 'heading' => 'script', 'sections' => 'plain', 'ornament' => 'wave', 'pattern' => 'wave', 'fonts' => ['playfair', 'parisienne', 'josefin']],
            'variants' => [
                'senja' => ['name' => 'Senja', 'colors' => ['bg' => '#fbf3ec', 'surface' => '#ffffff', 'soft' => '#f6ddcc', 'primary' => '#c0603f', 'accent' => '#3f8a93', 'ink' => '#3b2a24', 'on' => '#ffffff']],
                'laut' => ['name' => 'Laut', 'colors' => ['bg' => '#eef6f7', 'surface' => '#ffffff', 'soft' => '#d3e8eb', 'primary' => '#1f6670', 'accent' => '#e19a6b', 'ink' => '#1b3236', 'on' => '#ffffff']],
                'pasir' => ['name' => 'Pasir', 'colors' => ['bg' => '#f8f3ea', 'surface' => '#ffffff', 'soft' => '#eee2cc', 'primary' => '#7c6440', 'accent' => '#6a9ea4', 'ink' => '#382e20', 'on' => '#ffffff']],
            ],
        ],
        'tropical-palm' => [
            'name' => 'Tropical Palm', 'category' => 'rustic', 'sort' => 62,
            'description' => 'Daun palem tropis yang segar dan rileks.',
            'engine' => ['hero' => 'editorial', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'leaf', 'pattern' => 'leaf', 'fonts' => ['dm-serif', 'great-vibes', 'montserrat']],
            'variants' => [
                'hijau' => ['name' => 'Hijau Tropis', 'colors' => ['bg' => '#f1f6f1', 'surface' => '#ffffff', 'soft' => '#d7e8d8', 'primary' => '#1f5a3f', 'accent' => '#e0a24e', 'ink' => '#1c2f25', 'on' => '#ffffff']],
                'pasir' => ['name' => 'Pasir Putih', 'colors' => ['bg' => '#faf7f0', 'surface' => '#ffffff', 'soft' => '#efe7d4', 'primary' => '#4b6b3c', 'accent' => '#c98f4b', 'ink' => '#2f3324', 'on' => '#ffffff']],
                'malam' => ['name' => 'Malam Tropis', 'colors' => ['bg' => '#10241c', 'surface' => '#163026', 'soft' => '#1c3b2f', 'primary' => '#e6b766', 'accent' => '#e6b766', 'ink' => '#eaf0e8', 'on' => '#10241c']],
            ],
        ],
        'golden-hour' => [
            'name' => 'Golden Hour', 'category' => 'modern', 'sort' => 44,
            'description' => 'Foto besar bermandikan cahaya sore dengan tipografi elegan.',
            'engine' => ['hero' => 'photo', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'plain', 'ornament' => 'line', 'pattern' => 'none', 'fonts' => ['playfair', 'great-vibes', 'inter']],
            'variants' => [
                'emas' => ['name' => 'Emas Sore', 'colors' => ['bg' => '#fbf6ee', 'surface' => '#ffffff', 'soft' => '#f3e5cc', 'primary' => '#8a5a1f', 'accent' => '#d3a35a', 'ink' => '#3b2b18', 'on' => '#ffffff']],
                'mawar' => ['name' => 'Mawar Sore', 'colors' => ['bg' => '#fbf3f1', 'surface' => '#ffffff', 'soft' => '#f3dcd6', 'primary' => '#9a4b44', 'accent' => '#d79a83', 'ink' => '#3f2522', 'on' => '#ffffff']],
                'tembaga' => ['name' => 'Tembaga', 'colors' => ['bg' => '#f7efe8', 'surface' => '#fffaf6', 'soft' => '#ecd8c8', 'primary' => '#7f4426', 'accent' => '#c88a5c', 'ink' => '#3a2418', 'on' => '#fffaf6']],
            ],
        ],
        'botanical-script' => [
            'name' => 'Botanical Script', 'category' => 'minimalis', 'sort' => 45,
            'description' => 'Ilustrasi daun garis tipis dan huruf tegak bersambung.',
            'engine' => ['hero' => 'frame', 'shape' => 'circle', 'heading' => 'script', 'sections' => 'plain', 'ornament' => 'leaf', 'pattern' => 'none', 'fonts' => ['cormorant', 'alex-brush', 'josefin']],
            'variants' => [
                'hijau' => ['name' => 'Hijau Daun', 'colors' => ['bg' => '#f7f8f3', 'surface' => '#ffffff', 'soft' => '#e6ebdc', 'primary' => '#3f5b3a', 'accent' => '#8fa57f', 'ink' => '#28331f', 'on' => '#ffffff']],
                'zaitun' => ['name' => 'Zaitun', 'colors' => ['bg' => '#f6f5ee', 'surface' => '#ffffff', 'soft' => '#e6e4d2', 'primary' => '#5b5b34', 'accent' => '#a4a273', 'ink' => '#30301d', 'on' => '#ffffff']],
                'tinta' => ['name' => 'Tinta', 'colors' => ['bg' => '#f8f8f6', 'surface' => '#ffffff', 'soft' => '#e8e8e3', 'primary' => '#2f3433', 'accent' => '#8d9690', 'ink' => '#262a29', 'on' => '#ffffff']],
            ],
        ],
    ],
];
