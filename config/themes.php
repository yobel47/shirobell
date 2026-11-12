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
        'pastel' => 'Pastel',
        'vintage' => 'Vintage',
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
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'bordered', 'ornament' => 'kawung', 'pattern' => 'kawung', 'fonts' => ['cinzel', 'pinyon', 'lora'], 'illustration' => 'joglo'],
            'variants' => [
                'sogan' => ['name' => 'Sogan', 'colors' => ['bg' => '#f7f0e4', 'surface' => '#fffaf2', 'soft' => '#ecdcc2', 'primary' => '#6b4423', 'accent' => '#b08a4f', 'ink' => '#3b2a1c', 'on' => '#fffaf2']],
                'hitam' => ['name' => 'Hitam Emas', 'colors' => ['bg' => '#1d1813', 'surface' => '#28211a', 'soft' => '#342a20', 'primary' => '#c9a463', 'accent' => '#c9a463', 'ink' => '#f1e6d2', 'on' => '#1d1813']],
                'hijau' => ['name' => 'Hijau Pupus', 'colors' => ['bg' => '#f3f1e6', 'surface' => '#fbfaf3', 'soft' => '#dfe0c8', 'primary' => '#4c5a32', 'accent' => '#a68c4e', 'ink' => '#2f3322', 'on' => '#fbfaf3']],
            ],
        ],
        'parang-sekar' => [
            'name' => 'Parang Sekar', 'category' => 'adat', 'sort' => 11,
            'description' => 'Garis parang yang tegas berpadu bingkai klasik keraton.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'caps', 'sections' => 'cards', 'ornament' => 'parang', 'pattern' => 'parang', 'fonts' => ['cinzel', 'great-vibes', 'cormorant'], 'illustration' => 'gunungan'],
            'variants' => [
                'coklat' => ['name' => 'Coklat Keraton', 'colors' => ['bg' => '#f4ece0', 'surface' => '#fffaf3', 'soft' => '#e6d5bb', 'primary' => '#5a3a22', 'accent' => '#a37b3f', 'ink' => '#33241a', 'on' => '#fffaf3']],
                'merah' => ['name' => 'Merah Bata', 'colors' => ['bg' => '#f6ede6', 'surface' => '#fffaf6', 'soft' => '#ead3c6', 'primary' => '#7e2f22', 'accent' => '#b38443', 'ink' => '#3b2019', 'on' => '#fffaf6']],
                'biru' => ['name' => 'Biru Indigo', 'colors' => ['bg' => '#eef0f4', 'surface' => '#fafbfd', 'soft' => '#d6dbe6', 'primary' => '#24345a', 'accent' => '#a98a4e', 'ink' => '#1d2436', 'on' => '#fafbfd']],
            ],
        ],
        'mega-mendung' => [
            'name' => 'Mega Mendung', 'category' => 'adat', 'sort' => 12,
            'description' => 'Awan berlapis khas Cirebon dengan gradasi biru yang menenangkan.',
            'engine' => ['hero' => 'photo', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'cloud', 'pattern' => 'cloud', 'fonts' => ['marcellus', 'parisienne', 'lora'], 'illustration' => 'sawah'],
            'variants' => [
                'biru' => ['name' => 'Biru Laut', 'colors' => ['bg' => '#eef3f8', 'surface' => '#ffffff', 'soft' => '#d4e2ef', 'primary' => '#1f4e79', 'accent' => '#c0392b', 'ink' => '#1b2a3a', 'on' => '#ffffff']],
                'merah' => ['name' => 'Merah Cirebon', 'colors' => ['bg' => '#f8efed', 'surface' => '#ffffff', 'soft' => '#efd6d1', 'primary' => '#8e2a24', 'accent' => '#1f4e79', 'ink' => '#3a1d1a', 'on' => '#ffffff']],
                'hijau' => ['name' => 'Hijau Toska', 'colors' => ['bg' => '#ecf5f3', 'surface' => '#ffffff', 'soft' => '#cfe6e1', 'primary' => '#1d5f58', 'accent' => '#b5893f', 'ink' => '#173331', 'on' => '#ffffff']],
            ],
        ],
        'pasundan-asri' => [
            'name' => 'Pasundan Asri', 'category' => 'adat', 'sort' => 13,
            'description' => 'Sejuk dan asri bernuansa Sunda, dengan sentuhan daun dan bambu.',
            'engine' => ['hero' => 'editorial', 'shape' => 'rounded', 'heading' => 'script', 'sections' => 'plain', 'ornament' => 'leaf', 'pattern' => 'leaf', 'fonts' => ['cormorant', 'alex-brush', 'josefin'], 'illustration' => 'bambu'],
            'variants' => [
                'hijau' => ['name' => 'Hijau Bambu', 'colors' => ['bg' => '#f2f4ec', 'surface' => '#fbfcf7', 'soft' => '#dde4cf', 'primary' => '#3f5a36', 'accent' => '#9d8a52', 'ink' => '#2a3326', 'on' => '#fbfcf7']],
                'krem' => ['name' => 'Krem Kawung', 'colors' => ['bg' => '#f8f3ea', 'surface' => '#fffdf8', 'soft' => '#ebe1cd', 'primary' => '#6f5634', 'accent' => '#8e9b5a', 'ink' => '#3a2f22', 'on' => '#fffdf8']],
                'biru' => ['name' => 'Biru Kabut', 'colors' => ['bg' => '#f0f3f4', 'surface' => '#fbfcfc', 'soft' => '#d8e1e3', 'primary' => '#35535c', 'accent' => '#9a8a5e', 'ink' => '#243236', 'on' => '#fbfcfc']],
            ],
        ],
        'puspa-dewata' => [
            'name' => 'Puspa Dewata', 'category' => 'adat', 'sort' => 14,
            'description' => 'Teratai dan warna tanah liat, terinspirasi keindahan Pulau Dewata.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'lotus', 'pattern' => 'dots', 'fonts' => ['cinzel', 'great-vibes', 'lora'], 'illustration' => 'candi'],
            'variants' => [
                'terakota' => ['name' => 'Terakota', 'colors' => ['bg' => '#f7eee6', 'surface' => '#fffaf5', 'soft' => '#efd8c6', 'primary' => '#9a4a2c', 'accent' => '#c8a14f', 'ink' => '#3f241a', 'on' => '#fffaf5']],
                'putih' => ['name' => 'Putih Kuning', 'colors' => ['bg' => '#fbf9f2', 'surface' => '#ffffff', 'soft' => '#f1ead2', 'primary' => '#6b5a2c', 'accent' => '#d4a63b', 'ink' => '#3a321c', 'on' => '#ffffff']],
                'hitam' => ['name' => 'Hitam Poleng', 'colors' => ['bg' => '#1a1a18', 'surface' => '#242420', 'soft' => '#2f2e28', 'primary' => '#d6b25e', 'accent' => '#d6b25e', 'ink' => '#f1ecdf', 'on' => '#1a1a18']],
            ],
        ],
        'ulos-tenun' => [
            'name' => 'Ulos Tenun', 'category' => 'adat', 'sort' => 15,
            'description' => 'Garis tenun tegas berwarna merah, hitam, dan putih khas Batak.',
            'engine' => ['hero' => 'editorial', 'shape' => 'square', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'tenun', 'pattern' => 'tenun', 'fonts' => ['marcellus', 'great-vibes', 'montserrat'], 'illustration' => 'pegunungan'],
            'variants' => [
                'merah' => ['name' => 'Merah Ulos', 'colors' => ['bg' => '#f6f0ec', 'surface' => '#fffbf8', 'soft' => '#ecd9d2', 'primary' => '#7d1f1a', 'accent' => '#1f1a18', 'ink' => '#2b1c19', 'on' => '#fffbf8']],
                'hitam' => ['name' => 'Hitam', 'colors' => ['bg' => '#171515', 'surface' => '#221f1f', 'soft' => '#2d2828', 'primary' => '#c7362b', 'accent' => '#e9e1d8', 'ink' => '#efe8e1', 'on' => '#ffffff']],
                'emas' => ['name' => 'Emas', 'colors' => ['bg' => '#f8f3ea', 'surface' => '#fffdf8', 'soft' => '#ede1c9', 'primary' => '#5b2a1f', 'accent' => '#b78b3d', 'ink' => '#2e1e17', 'on' => '#fffdf8']],
            ],
        ],
        'songket-minang' => [
            'name' => 'Songket Minang', 'category' => 'adat', 'sort' => 16,
            'description' => 'Kilau songket emas di atas warna marun yang megah.',
            'engine' => ['hero' => 'frame', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'songket', 'pattern' => 'songket', 'fonts' => ['cinzel', 'pinyon', 'cormorant'], 'illustration' => 'rumah-gadang'],
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
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['playfair', 'great-vibes', 'inter'], 'illustration' => 'bouquet'],
            'variants' => [
                'pink' => ['name' => 'Pink', 'colors' => ['bg' => '#fdf4f5', 'surface' => '#ffffff', 'soft' => '#f8dfe3', 'primary' => '#b4566a', 'accent' => '#d79aa6', 'ink' => '#4a2c33', 'on' => '#ffffff']],
                'koral' => ['name' => 'Koral', 'colors' => ['bg' => '#fff6f2', 'surface' => '#ffffff', 'soft' => '#fbe0d5', 'primary' => '#c45b45', 'accent' => '#e9a38f', 'ink' => '#4d2c24', 'on' => '#ffffff']],
                'ungu' => ['name' => 'Ungu Muda', 'colors' => ['bg' => '#f8f4fb', 'surface' => '#ffffff', 'soft' => '#e8ddf1', 'primary' => '#7c5a9b', 'accent' => '#b9a0d0', 'ink' => '#3a2c47', 'on' => '#ffffff']],
            ],
        ],
        'lavender-field' => [
            'name' => 'Lavender Field', 'category' => 'floral', 'sort' => 21,
            'description' => 'Ladang lavender yang lembut dengan tata letak ala majalah.',
            'engine' => ['hero' => 'editorial', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'plain', 'ornament' => 'leaf', 'pattern' => 'none', 'fonts' => ['cormorant', 'parisienne', 'josefin'], 'illustration' => 'wildflowers'],
            'variants' => [
                'lavender' => ['name' => 'Lavender', 'colors' => ['bg' => '#f6f4fa', 'surface' => '#ffffff', 'soft' => '#e4def0', 'primary' => '#5f4d86', 'accent' => '#a99ac9', 'ink' => '#2f2a3d', 'on' => '#ffffff']],
                'lilac' => ['name' => 'Lilac', 'colors' => ['bg' => '#faf4f8', 'surface' => '#ffffff', 'soft' => '#f0dde9', 'primary' => '#8a4f78', 'accent' => '#c79ab9', 'ink' => '#3d2737', 'on' => '#ffffff']],
                'abu' => ['name' => 'Abu Lembut', 'colors' => ['bg' => '#f5f5f6', 'surface' => '#ffffff', 'soft' => '#e3e3e8', 'primary' => '#4f5065', 'accent' => '#a3a0b8', 'ink' => '#2b2c37', 'on' => '#ffffff']],
            ],
        ],
        'wildflower-meadow' => [
            'name' => 'Wildflower Meadow', 'category' => 'floral', 'sort' => 22,
            'description' => 'Bunga liar warna-warni untuk pernikahan yang hangat dan santai.',
            'engine' => ['hero' => 'photo', 'shape' => 'circle', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'floral', 'pattern' => 'dots', 'fonts' => ['lora', 'alex-brush', 'inter'], 'illustration' => 'wildflowers'],
            'variants' => [
                'cerah' => ['name' => 'Cerah', 'colors' => ['bg' => '#fbf8f0', 'surface' => '#ffffff', 'soft' => '#f2ead3', 'primary' => '#a3502f', 'accent' => '#d8a53c', 'ink' => '#3c2d22', 'on' => '#ffffff']],
                'pastel' => ['name' => 'Pastel', 'colors' => ['bg' => '#f9f7f9', 'surface' => '#ffffff', 'soft' => '#ece5ee', 'primary' => '#80607e', 'accent' => '#9fb59a', 'ink' => '#382d38', 'on' => '#ffffff']],
                'senja' => ['name' => 'Senja', 'colors' => ['bg' => '#fbf3ee', 'surface' => '#ffffff', 'soft' => '#f3dccf', 'primary' => '#b2513b', 'accent' => '#7d8c55', 'ink' => '#41271f', 'on' => '#ffffff']],
            ],
        ],
        'magnolia-white' => [
            'name' => 'Magnolia White', 'category' => 'floral', 'sort' => 23,
            'description' => 'Magnolia putih dan hijau sage dalam bingkai yang bersih.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'bordered', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['cormorant', 'pinyon', 'lora'], 'illustration' => 'eucalyptus'],
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
            'engine' => ['hero' => 'photo', 'shape' => 'square', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'deco', 'pattern' => 'deco', 'fonts' => ['cinzel', 'pinyon', 'montserrat'], 'illustration' => 'kota'],
            'variants' => [
                'emas' => ['name' => 'Emas', 'colors' => ['bg' => '#111111', 'surface' => '#1a1a1a', 'soft' => '#222222', 'primary' => '#d4af37', 'accent' => '#d4af37', 'ink' => '#eeeae0', 'on' => '#111111']],
                'perak' => ['name' => 'Perak', 'colors' => ['bg' => '#121417', 'surface' => '#1b1e22', 'soft' => '#24282d', 'primary' => '#c9ced6', 'accent' => '#c9ced6', 'ink' => '#eceef1', 'on' => '#121417']],
                'rosegold' => ['name' => 'Rose Gold', 'colors' => ['bg' => '#161112', 'surface' => '#21191a', 'soft' => '#2b2123', 'primary' => '#d9a38f', 'accent' => '#d9a38f', 'ink' => '#f3e7e3', 'on' => '#161112']],
            ],
        ],
        'emerald-velvet' => [
            'name' => 'Emerald Velvet', 'category' => 'luxury', 'sort' => 31,
            'description' => 'Hijau zamrud seperti beludru dengan aksen emas.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'bordered', 'ornament' => 'deco', 'pattern' => 'deco', 'fonts' => ['dm-serif', 'great-vibes', 'lora'], 'illustration' => 'taman'],
            'variants' => [
                'zamrud' => ['name' => 'Zamrud', 'colors' => ['bg' => '#0f2a22', 'surface' => '#14352b', 'soft' => '#1a4034', 'primary' => '#d6b46a', 'accent' => '#d6b46a', 'ink' => '#efe8d6', 'on' => '#0f2a22']],
                'safir' => ['name' => 'Safir', 'colors' => ['bg' => '#0f1a33', 'surface' => '#142241', 'soft' => '#1a2b4f', 'primary' => '#d6b46a', 'accent' => '#d6b46a', 'ink' => '#ece8dc', 'on' => '#0f1a33']],
                'ruby' => ['name' => 'Ruby', 'colors' => ['bg' => '#2e0f16', 'surface' => '#3b141d', 'soft' => '#481a25', 'primary' => '#dcb877', 'accent' => '#dcb877', 'ink' => '#f4e8dc', 'on' => '#2e0f16']],
            ],
        ],
        'marble-ivory' => [
            'name' => 'Marble Ivory', 'category' => 'luxury', 'sort' => 32,
            'description' => 'Gading dan abu marmer dengan sentuhan emas tipis.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'caps', 'sections' => 'cards', 'ornament' => 'line', 'pattern' => 'none', 'fonts' => ['cinzel', 'parisienne', 'cormorant'], 'illustration' => 'bouquet'],
            'variants' => [
                'gading' => ['name' => 'Gading', 'colors' => ['bg' => '#f7f5f0', 'surface' => '#ffffff', 'soft' => '#ebe7de', 'primary' => '#3d3a35', 'accent' => '#b59a62', 'ink' => '#2c2a26', 'on' => '#ffffff']],
                'abu' => ['name' => 'Abu Marmer', 'colors' => ['bg' => '#f2f2f1', 'surface' => '#ffffff', 'soft' => '#e2e2df', 'primary' => '#4a4a48', 'accent' => '#a8a8a2', 'ink' => '#2b2b2a', 'on' => '#ffffff']],
                'champagne' => ['name' => 'Champagne', 'colors' => ['bg' => '#f8f3ea', 'surface' => '#fffdf8', 'soft' => '#eee3cf', 'primary' => '#6b5639', 'accent' => '#c5a46a', 'ink' => '#382d20', 'on' => '#fffdf8']],
            ],
        ],
        'burgundy-royale' => [
            'name' => 'Burgundy Royale', 'category' => 'luxury', 'sort' => 33,
            'description' => 'Burgundy pekat dengan rangkaian bunga yang elegan.',
            'engine' => ['hero' => 'photo', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['playfair', 'pinyon', 'lora'], 'illustration' => 'bouquet'],
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
            'engine' => ['hero' => 'editorial', 'shape' => 'square', 'heading' => 'caps', 'sections' => 'plain', 'ornament' => 'line', 'pattern' => 'none', 'fonts' => ['dm-serif', 'parisienne', 'montserrat'], 'illustration' => 'kota'],
            'variants' => [
                'putih' => ['name' => 'Putih', 'colors' => ['bg' => '#ffffff', 'surface' => '#f6f6f6', 'soft' => '#eeeeee', 'primary' => '#111111', 'accent' => '#7a7a7a', 'ink' => '#1a1a1a', 'on' => '#ffffff']],
                'hitam' => ['name' => 'Hitam', 'colors' => ['bg' => '#0e0e0e', 'surface' => '#181818', 'soft' => '#202020', 'primary' => '#f2f2f2', 'accent' => '#9a9a9a', 'ink' => '#ededed', 'on' => '#0e0e0e']],
                'krem' => ['name' => 'Krem', 'colors' => ['bg' => '#f5f1ea', 'surface' => '#fbf9f5', 'soft' => '#e9e3d8', 'primary' => '#1d1b18', 'accent' => '#8c8070', 'ink' => '#24221e', 'on' => '#fbf9f5']],
            ],
        ],
        'terracotta-sun' => [
            'name' => 'Terracotta Sun', 'category' => 'modern', 'sort' => 41,
            'description' => 'Bentuk lengkung dan warna terakota yang hangat dan modern.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'line', 'pattern' => 'dots', 'fonts' => ['dm-serif', 'alex-brush', 'josefin'], 'illustration' => 'pegunungan'],
            'variants' => [
                'terakota' => ['name' => 'Terakota', 'colors' => ['bg' => '#f8efe7', 'surface' => '#fffaf6', 'soft' => '#f0d9c8', 'primary' => '#b2532f', 'accent' => '#e0a26e', 'ink' => '#43271b', 'on' => '#fffaf6']],
                'mustard' => ['name' => 'Mustard', 'colors' => ['bg' => '#faf4e6', 'surface' => '#fffcf4', 'soft' => '#f1e2bd', 'primary' => '#9a6a12', 'accent' => '#cf9f3c', 'ink' => '#3d2e14', 'on' => '#fffcf4']],
                'zaitun' => ['name' => 'Zaitun', 'colors' => ['bg' => '#f3f2e8', 'surface' => '#fbfbf5', 'soft' => '#e0dfc8', 'primary' => '#5d6131', 'accent' => '#b68b4f', 'ink' => '#2f301c', 'on' => '#fbfbf5']],
            ],
        ],
        'dusty-blue' => [
            'name' => 'Dusty Blue', 'category' => 'minimalis', 'sort' => 42,
            'description' => 'Biru berdebu yang kalem dengan ruang lega dan rapi.',
            'engine' => ['hero' => 'arch', 'shape' => 'circle', 'heading' => 'serif', 'sections' => 'plain', 'ornament' => 'line', 'pattern' => 'none', 'fonts' => ['cormorant', 'great-vibes', 'inter'], 'illustration' => 'eucalyptus'],
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
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'star', 'pattern' => 'star', 'fonts' => ['marcellus', 'pinyon', 'lora'], 'illustration' => 'masjid'],
            'variants' => [
                'hijau' => ['name' => 'Hijau', 'colors' => ['bg' => '#f1f5f0', 'surface' => '#ffffff', 'soft' => '#d9e6d6', 'primary' => '#24543d', 'accent' => '#b8964e', 'ink' => '#1c2e24', 'on' => '#ffffff']],
                'putih' => ['name' => 'Putih Emas', 'colors' => ['bg' => '#fbf9f4', 'surface' => '#ffffff', 'soft' => '#f0eadb', 'primary' => '#6b5530', 'accent' => '#c3a15c', 'ink' => '#33291b', 'on' => '#ffffff']],
                'biru' => ['name' => 'Biru Teduh', 'colors' => ['bg' => '#f0f4f6', 'surface' => '#ffffff', 'soft' => '#d6e2e8', 'primary' => '#1f4a5e', 'accent' => '#b8964e', 'ink' => '#1a2c35', 'on' => '#ffffff']],
            ],
        ],
        'lentera-malam' => [
            'name' => 'Lentera Malam', 'category' => 'islami', 'sort' => 51,
            'description' => 'Malam biru tua dengan ornamen geometri berkilau seperti lentera.',
            'engine' => ['hero' => 'photo', 'shape' => 'arch', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'star', 'pattern' => 'star', 'fonts' => ['cinzel', 'great-vibes', 'cormorant'], 'illustration' => 'masjid'],
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
            'engine' => ['hero' => 'arch', 'shape' => 'rounded', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'leaf', 'pattern' => 'leaf', 'fonts' => ['lora', 'alex-brush', 'josefin'], 'illustration' => 'taman'],
            'variants' => [
                'kraft' => ['name' => 'Kraft', 'colors' => ['bg' => '#efe4d3', 'surface' => '#f8f1e6', 'soft' => '#e2d1b7', 'primary' => '#5b4129', 'accent' => '#8a8a4e', 'ink' => '#3a2a1c', 'on' => '#f8f1e6']],
                'hijau' => ['name' => 'Hijau Hutan', 'colors' => ['bg' => '#eef0e6', 'surface' => '#f8f9f2', 'soft' => '#d9dec9', 'primary' => '#36492d', 'accent' => '#a3814d', 'ink' => '#263020', 'on' => '#f8f9f2']],
                'karat' => ['name' => 'Karat', 'colors' => ['bg' => '#f4e9de', 'surface' => '#fbf5ee', 'soft' => '#e9d3bf', 'primary' => '#8b4325', 'accent' => '#7c7f4c', 'ink' => '#3c2418', 'on' => '#fbf5ee']],
            ],
        ],
        'pantai-senja' => [
            'name' => 'Pantai Senja', 'category' => 'rustic', 'sort' => 61,
            'description' => 'Ombak dan langit senja untuk pernikahan di tepi pantai.',
            'engine' => ['hero' => 'photo', 'shape' => 'rounded', 'heading' => 'script', 'sections' => 'plain', 'ornament' => 'wave', 'pattern' => 'wave', 'fonts' => ['playfair', 'parisienne', 'josefin'], 'illustration' => 'pantai'],
            'variants' => [
                'senja' => ['name' => 'Senja', 'colors' => ['bg' => '#fbf3ec', 'surface' => '#ffffff', 'soft' => '#f6ddcc', 'primary' => '#c0603f', 'accent' => '#3f8a93', 'ink' => '#3b2a24', 'on' => '#ffffff']],
                'laut' => ['name' => 'Laut', 'colors' => ['bg' => '#eef6f7', 'surface' => '#ffffff', 'soft' => '#d3e8eb', 'primary' => '#1f6670', 'accent' => '#e19a6b', 'ink' => '#1b3236', 'on' => '#ffffff']],
                'pasir' => ['name' => 'Pasir', 'colors' => ['bg' => '#f8f3ea', 'surface' => '#ffffff', 'soft' => '#eee2cc', 'primary' => '#7c6440', 'accent' => '#6a9ea4', 'ink' => '#382e20', 'on' => '#ffffff']],
            ],
        ],
        'tropical-palm' => [
            'name' => 'Tropical Palm', 'category' => 'rustic', 'sort' => 62,
            'description' => 'Daun palem tropis yang segar dan rileks.',
            'engine' => ['hero' => 'editorial', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'leaf', 'pattern' => 'leaf', 'fonts' => ['dm-serif', 'great-vibes', 'montserrat'], 'illustration' => 'pantai'],
            'variants' => [
                'hijau' => ['name' => 'Hijau Tropis', 'colors' => ['bg' => '#f1f6f1', 'surface' => '#ffffff', 'soft' => '#d7e8d8', 'primary' => '#1f5a3f', 'accent' => '#e0a24e', 'ink' => '#1c2f25', 'on' => '#ffffff']],
                'pasir' => ['name' => 'Pasir Putih', 'colors' => ['bg' => '#faf7f0', 'surface' => '#ffffff', 'soft' => '#efe7d4', 'primary' => '#4b6b3c', 'accent' => '#c98f4b', 'ink' => '#2f3324', 'on' => '#ffffff']],
                'malam' => ['name' => 'Malam Tropis', 'colors' => ['bg' => '#10241c', 'surface' => '#163026', 'soft' => '#1c3b2f', 'primary' => '#e6b766', 'accent' => '#e6b766', 'ink' => '#eaf0e8', 'on' => '#10241c']],
            ],
        ],
        'golden-hour' => [
            'name' => 'Golden Hour', 'category' => 'modern', 'sort' => 44,
            'description' => 'Foto besar bermandikan cahaya sore dengan tipografi elegan.',
            'engine' => ['hero' => 'photo', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'plain', 'ornament' => 'line', 'pattern' => 'none', 'fonts' => ['playfair', 'great-vibes', 'inter'], 'illustration' => 'wildflowers'],
            'variants' => [
                'emas' => ['name' => 'Emas Sore', 'colors' => ['bg' => '#fbf6ee', 'surface' => '#ffffff', 'soft' => '#f3e5cc', 'primary' => '#8a5a1f', 'accent' => '#d3a35a', 'ink' => '#3b2b18', 'on' => '#ffffff']],
                'mawar' => ['name' => 'Mawar Sore', 'colors' => ['bg' => '#fbf3f1', 'surface' => '#ffffff', 'soft' => '#f3dcd6', 'primary' => '#9a4b44', 'accent' => '#d79a83', 'ink' => '#3f2522', 'on' => '#ffffff']],
                'tembaga' => ['name' => 'Tembaga', 'colors' => ['bg' => '#f7efe8', 'surface' => '#fffaf6', 'soft' => '#ecd8c8', 'primary' => '#7f4426', 'accent' => '#c88a5c', 'ink' => '#3a2418', 'on' => '#fffaf6']],
            ],
        ],
        'botanical-script' => [
            'name' => 'Botanical Script', 'category' => 'minimalis', 'sort' => 45,
            'description' => 'Ilustrasi daun garis tipis dan huruf tegak bersambung.',
            'engine' => ['hero' => 'frame', 'shape' => 'circle', 'heading' => 'script', 'sections' => 'plain', 'ornament' => 'leaf', 'pattern' => 'none', 'fonts' => ['cormorant', 'alex-brush', 'josefin'], 'illustration' => 'eucalyptus'],
            'variants' => [
                'hijau' => ['name' => 'Hijau Daun', 'colors' => ['bg' => '#f7f8f3', 'surface' => '#ffffff', 'soft' => '#e6ebdc', 'primary' => '#3f5b3a', 'accent' => '#8fa57f', 'ink' => '#28331f', 'on' => '#ffffff']],
                'zaitun' => ['name' => 'Zaitun', 'colors' => ['bg' => '#f6f5ee', 'surface' => '#ffffff', 'soft' => '#e6e4d2', 'primary' => '#5b5b34', 'accent' => '#a4a273', 'ink' => '#30301d', 'on' => '#ffffff']],
                'tinta' => ['name' => 'Tinta', 'colors' => ['bg' => '#f8f8f6', 'surface' => '#ffffff', 'soft' => '#e8e8e3', 'primary' => '#2f3433', 'accent' => '#8d9690', 'ink' => '#262a29', 'on' => '#ffffff']],
            ],
        ],

        // Batch v0.3: 36 themes with illustrations (generated palettes, see ADR 009).
        'joglo-agung' => [
            'name' => 'Joglo Agung', 'category' => 'adat', 'sort' => 100,
            'description' => 'Rumah joglo dan motif kawung untuk pernikahan adat Jawa yang agung.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'kawung', 'pattern' => 'parang', 'fonts' => ['cinzel', 'pinyon', 'lora'], 'illustration' => 'joglo'],
            'variants' => [
                'sogan' => ['name' => 'Sogan', 'colors' => ['bg' => '#f5ecdd', 'surface' => '#fcf8f3', 'soft' => '#e0d3c2', 'primary' => '#5c3a1e', 'accent' => '#b48a4a', 'ink' => '#34271d', 'on' => '#ffffff']],
                'malam' => ['name' => 'Malam', 'colors' => ['bg' => '#1c1610', 'surface' => '#2a241e', 'soft' => '#35302a', 'primary' => '#d1a964', 'accent' => '#d1a964', 'ink' => '#f1e8da', 'on' => '#1c1610']],
                'hijau' => ['name' => 'Hijau Daun', 'colors' => ['bg' => '#eef0e2', 'surface' => '#f9faf5', 'soft' => '#d5d9c8', 'primary' => '#3e4f2a', 'accent' => '#a88b4c', 'ink' => '#292f21', 'on' => '#ffffff']],
            ],
        ],
        'kayon-sakral' => [
            'name' => 'Kayon Sakral', 'category' => 'adat', 'sort' => 101,
            'description' => 'Gunungan wayang sebagai lambang awal kehidupan baru.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'parang', 'pattern' => 'kawung', 'fonts' => ['marcellus', 'great-vibes', 'cormorant'], 'illustration' => 'gunungan'],
            'variants' => [
                'emas' => ['name' => 'Emas Tua', 'colors' => ['bg' => '#f6eee0', 'surface' => '#fcf9f4', 'soft' => '#e3d3c4', 'primary' => '#6b2f1a', 'accent' => '#c19a4b', 'ink' => '#3a231b', 'on' => '#ffffff']],
                'hitam' => ['name' => 'Hitam', 'colors' => ['bg' => '#17130f', 'surface' => '#25211d', 'soft' => '#312d29', 'primary' => '#d4ad62', 'accent' => '#d4ad62', 'ink' => '#f1e9da', 'on' => '#17130f']],
                'biru' => ['name' => 'Biru Wayang', 'colors' => ['bg' => '#eef1f5', 'surface' => '#f9fafc', 'soft' => '#d1d7e0', 'primary' => '#22355e', 'accent' => '#b7924c', 'ink' => '#1e2635', 'on' => '#ffffff']],
            ],
        ],
        'melati-keraton' => [
            'name' => 'Melati Keraton', 'category' => 'adat', 'sort' => 102,
            'description' => 'Roncean melati yang harum dan anggun ala pengantin Jawa.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'bordered', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['cormorant', 'pinyon', 'lora'], 'illustration' => 'melati'],
            'variants' => [
                'putih' => ['name' => 'Putih Melati', 'colors' => ['bg' => '#faf8f2', 'surface' => '#fdfdfa', 'soft' => '#e2e2d8', 'primary' => '#4d5b3a', 'accent' => '#b9a06a', 'ink' => '#2f3427', 'on' => '#ffffff']],
                'marun' => ['name' => 'Marun', 'colors' => ['bg' => '#f7efee', 'surface' => '#fcf9f9', 'soft' => '#e4d2d3', 'primary' => '#6e1f2a', 'accent' => '#c09a5e', 'ink' => '#3b1d21', 'on' => '#ffffff']],
                'hijau' => ['name' => 'Hijau Tua', 'colors' => ['bg' => '#132a20', 'surface' => '#21372d', 'soft' => '#2d4139', 'primary' => '#e0c58c', 'accent' => '#e0c58c', 'ink' => '#f2ecdf', 'on' => '#132a20']],
            ],
        ],
        'sunda-pakuan' => [
            'name' => 'Sunda Pakuan', 'category' => 'adat', 'sort' => 103,
            'description' => 'Rumpun bambu dan kesejukan tanah Pasundan.',
            'engine' => ['hero' => 'editorial', 'shape' => 'rounded', 'heading' => 'script', 'sections' => 'plain', 'ornament' => 'leaf', 'pattern' => 'dots', 'fonts' => ['cormorant', 'alex-brush', 'josefin'], 'illustration' => 'bambu'],
            'variants' => [
                'hijau' => ['name' => 'Hijau Bambu', 'colors' => ['bg' => '#f1f4ea', 'surface' => '#fafbf8', 'soft' => '#d7ded0', 'primary' => '#36552d', 'accent' => '#a2914f', 'ink' => '#263222', 'on' => '#ffffff']],
                'krem' => ['name' => 'Krem', 'colors' => ['bg' => '#f8f2e6', 'surface' => '#fdfaf6', 'soft' => '#e4dbcc', 'primary' => '#6a4f2c', 'accent' => '#7f9a55', 'ink' => '#3a2f22', 'on' => '#ffffff']],
                'kabut' => ['name' => 'Kabut', 'colors' => ['bg' => '#eff3f3', 'surface' => '#f9fbfb', 'soft' => '#d4dcde', 'primary' => '#31525a', 'accent' => '#a08e5c', 'ink' => '#243134', 'on' => '#ffffff']],
            ],
        ],
        'bali-kamboja' => [
            'name' => 'Bali Kamboja', 'category' => 'adat', 'sort' => 104,
            'description' => 'Bunga kamboja dan teratai dari Pulau Dewata.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'lotus', 'pattern' => 'dots', 'fonts' => ['playfair', 'parisienne', 'lora'], 'illustration' => 'frangipani'],
            'variants' => [
                'kuning' => ['name' => 'Kuning Kamboja', 'colors' => ['bg' => '#fbf7ec', 'surface' => '#fefcf8', 'soft' => '#e9e1cf', 'primary' => '#7a5a1d', 'accent' => '#e0b14a', 'ink' => '#40341c', 'on' => '#ffffff']],
                'putih' => ['name' => 'Putih', 'colors' => ['bg' => '#f9f9f6', 'surface' => '#fdfdfc', 'soft' => '#dfe3de', 'primary' => '#3f5a4b', 'accent' => '#d6b46c', 'ink' => '#29342e', 'on' => '#ffffff']],
                'terakota' => ['name' => 'Terakota', 'colors' => ['bg' => '#f7ece4', 'surface' => '#fcf8f6', 'soft' => '#e9d4ca', 'primary' => '#93442a', 'accent' => '#d6a35a', 'ink' => '#492b21', 'on' => '#ffffff']],
            ],
        ],
        'candi-bentar' => [
            'name' => 'Candi Bentar', 'category' => 'adat', 'sort' => 105,
            'description' => 'Gerbang candi bentar yang megah sebagai pintu kebahagiaan.',
            'engine' => ['hero' => 'frame', 'shape' => 'square', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'lotus', 'pattern' => 'songket', 'fonts' => ['cinzel', 'great-vibes', 'cormorant'], 'illustration' => 'candi'],
            'variants' => [
                'bata' => ['name' => 'Bata Merah', 'colors' => ['bg' => '#f5ebe3', 'surface' => '#fcf8f5', 'soft' => '#e4d1c8', 'primary' => '#7e3221', 'accent' => '#c79b4f', 'ink' => '#41241e', 'on' => '#ffffff']],
                'batu' => ['name' => 'Batu Abu', 'colors' => ['bg' => '#f0efeb', 'surface' => '#faf9f8', 'soft' => '#d7d6d2', 'primary' => '#3f3f3a', 'accent' => '#b49a62', 'ink' => '#292927', 'on' => '#ffffff']],
                'malam' => ['name' => 'Malam', 'colors' => ['bg' => '#1a1715', 'surface' => '#282523', 'soft' => '#33312f', 'primary' => '#d7b25f', 'accent' => '#d7b25f', 'ink' => '#f1e9d9', 'on' => '#1a1715']],
            ],
        ],
        'minang-rang-kayo' => [
            'name' => 'Minang Rang Kayo', 'category' => 'adat', 'sort' => 106,
            'description' => 'Gonjong rumah gadang dan kilau songket Minangkabau.',
            'engine' => ['hero' => 'frame', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'songket', 'pattern' => 'songket', 'fonts' => ['cinzel', 'pinyon', 'cormorant'], 'illustration' => 'rumah-gadang'],
            'variants' => [
                'merah' => ['name' => 'Merah', 'colors' => ['bg' => '#f7eeea', 'surface' => '#fcf9f8', 'soft' => '#e6d1ce', 'primary' => '#7c1d24', 'accent' => '#c99a43', 'ink' => '#401c1f', 'on' => '#ffffff']],
                'marun' => ['name' => 'Marun Gelap', 'colors' => ['bg' => '#32101a', 'surface' => '#3e1e28', 'soft' => '#492a33', 'primary' => '#dcb66c', 'accent' => '#dcb66c', 'ink' => '#f2eadb', 'on' => '#32101a']],
                'hitam' => ['name' => 'Hitam Emas', 'colors' => ['bg' => '#15110d', 'surface' => '#231f1c', 'soft' => '#2f2b28', 'primary' => '#d6b064', 'accent' => '#d6b064', 'ink' => '#f1e9da', 'on' => '#15110d']],
            ],
        ],
        'batak-gorga' => [
            'name' => 'Batak Gorga', 'category' => 'adat', 'sort' => 107,
            'description' => 'Gorga merah putih hitam dengan panorama Danau Toba.',
            'engine' => ['hero' => 'editorial', 'shape' => 'square', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'tenun', 'pattern' => 'tenun', 'fonts' => ['marcellus', 'great-vibes', 'montserrat'], 'illustration' => 'pegunungan'],
            'variants' => [
                'merah' => ['name' => 'Merah Gorga', 'colors' => ['bg' => '#f6f0eb', 'surface' => '#fcfaf8', 'soft' => '#e5d2cd', 'primary' => '#7a1a16', 'accent' => '#1e1a18', 'ink' => '#401b1a', 'on' => '#ffffff']],
                'hitam' => ['name' => 'Hitam', 'colors' => ['bg' => '#161414', 'surface' => '#242222', 'soft' => '#302e2e', 'primary' => '#cf3a2e', 'accent' => '#ebe2d8', 'ink' => '#f4efe8', 'on' => '#161414']],
                'putih' => ['name' => 'Putih', 'colors' => ['bg' => '#fbf9f6', 'surface' => '#fefdfc', 'soft' => '#dedbd8', 'primary' => '#2a2522', 'accent' => '#a3291f', 'ink' => '#211f1e', 'on' => '#ffffff']],
            ],
        ],
        'bugis-pinisi' => [
            'name' => 'Bugis Pinisi', 'category' => 'adat', 'sort' => 108,
            'description' => 'Kapal pinisi berlayar menuju samudra kehidupan bersama.',
            'engine' => ['hero' => 'photo', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'wave', 'pattern' => 'wave', 'fonts' => ['dm-serif', 'great-vibes', 'montserrat'], 'illustration' => 'pinisi'],
            'variants' => [
                'laut' => ['name' => 'Biru Laut', 'colors' => ['bg' => '#eef4f7', 'surface' => '#f9fbfc', 'soft' => '#d1dce3', 'primary' => '#1d4a66', 'accent' => '#c79a52', 'ink' => '#1c2d38', 'on' => '#ffffff']],
                'sutra' => ['name' => 'Sutra Bugis', 'colors' => ['bg' => '#f8eff4', 'surface' => '#fdf9fb', 'soft' => '#e6d3de', 'primary' => '#7a2a5a', 'accent' => '#c9a15d', 'ink' => '#402134', 'on' => '#ffffff']],
                'senja' => ['name' => 'Senja', 'colors' => ['bg' => '#f9efe6', 'surface' => '#fdf9f6', 'soft' => '#ecd7cb', 'primary' => '#9b4425', 'accent' => '#2f6f7a', 'ink' => '#4c2b1f', 'on' => '#ffffff']],
            ],
        ],
        'betawi-gigi-balang' => [
            'name' => 'Betawi Gigi Balang', 'category' => 'adat', 'sort' => 109,
            'description' => 'Lisplang gigi balang dan warna ceria khas Betawi.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'songket', 'pattern' => 'dots', 'fonts' => ['dm-serif', 'alex-brush', 'josefin'], 'illustration' => 'taman'],
            'variants' => [
                'merah' => ['name' => 'Merah Ceria', 'colors' => ['bg' => '#fbf3ea', 'surface' => '#fefbf8', 'soft' => '#efd6cd', 'primary' => '#a3261d', 'accent' => '#d99a2b', 'ink' => '#4f201c', 'on' => '#ffffff']],
                'hijau' => ['name' => 'Hijau', 'colors' => ['bg' => '#f2f6ee', 'surface' => '#fafcf9', 'soft' => '#d7e2d5', 'primary' => '#2f6a3a', 'accent' => '#e0a83a', 'ink' => '#233a27', 'on' => '#ffffff']],
                'oranye' => ['name' => 'Oranye', 'colors' => ['bg' => '#fdf4ea', 'surface' => '#fefbf8', 'soft' => '#f3ddcd', 'primary' => '#b4501c', 'accent' => '#2f6a5a', 'ink' => '#56301c', 'on' => '#ffffff']],
            ],
        ],
        'peranakan-lampion' => [
            'name' => 'Peranakan Lampion', 'category' => 'adat', 'sort' => 110,
            'description' => 'Lampion merah dan bunga mei untuk pernikahan Tionghoa Peranakan.',
            'engine' => ['hero' => 'frame', 'shape' => 'circle', 'heading' => 'serif', 'sections' => 'bordered', 'ornament' => 'cloud', 'pattern' => 'cloud', 'fonts' => ['marcellus', 'pinyon', 'lora'], 'illustration' => 'lampion'],
            'variants' => [
                'merah' => ['name' => 'Merah Emas', 'colors' => ['bg' => '#fbf2ec', 'surface' => '#fefaf8', 'soft' => '#eed4d0', 'primary' => '#a11d22', 'accent' => '#d4a440', 'ink' => '#4f1c1e', 'on' => '#ffffff']],
                'hitam' => ['name' => 'Hitam Merah', 'colors' => ['bg' => '#171112', 'surface' => '#251f20', 'soft' => '#312b2c', 'primary' => '#d9433c', 'accent' => '#d9a94a', 'ink' => '#f2e8d7', 'on' => '#171112']],
                'giok' => ['name' => 'Giok', 'colors' => ['bg' => '#eef5f1', 'surface' => '#f9fcfa', 'soft' => '#d1dfd9', 'primary' => '#1f5a46', 'accent' => '#c9473c', 'ink' => '#1d342c', 'on' => '#ffffff']],
            ],
        ],
        'palembang-limas' => [
            'name' => 'Palembang Limas', 'category' => 'adat', 'sort' => 111,
            'description' => 'Rumah limas dan songket Palembang yang berkilau.',
            'engine' => ['hero' => 'frame', 'shape' => 'rounded', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'songket', 'pattern' => 'songket', 'fonts' => ['cinzel', 'parisienne', 'cormorant'], 'illustration' => 'joglo'],
            'variants' => [
                'emas' => ['name' => 'Emas Merah', 'colors' => ['bg' => '#f8efe6', 'surface' => '#fdf9f6', 'soft' => '#e9d2ca', 'primary' => '#8a1f1f', 'accent' => '#cfa244', 'ink' => '#461d1d', 'on' => '#ffffff']],
                'ungu' => ['name' => 'Ungu', 'colors' => ['bg' => '#f5eff6', 'surface' => '#fcf9fc', 'soft' => '#ded3e0', 'primary' => '#4f2459', 'accent' => '#cda55a', 'ink' => '#2f1f33', 'on' => '#ffffff']],
                'gelap' => ['name' => 'Gelap', 'colors' => ['bg' => '#1d1210', 'surface' => '#2b201e', 'soft' => '#362c2a', 'primary' => '#dfb560', 'accent' => '#dfb560', 'ink' => '#f2ead9', 'on' => '#1d1210']],
            ],
        ],
        'rose-cascade' => [
            'name' => 'Rose Cascade', 'category' => 'floral', 'sort' => 112,
            'description' => 'Rangkaian mawar menjuntai yang romantis.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['playfair', 'great-vibes', 'inter'], 'illustration' => 'bouquet'],
            'variants' => [
                'rose' => ['name' => 'Rose', 'colors' => ['bg' => '#fcf3f3', 'surface' => '#fefbfb', 'soft' => '#f0dade', 'primary' => '#a3445a', 'accent' => '#d9939f', 'ink' => '#4f2b34', 'on' => '#ffffff']],
                'blush' => ['name' => 'Blush', 'colors' => ['bg' => '#fdf6f2', 'surface' => '#fefcfa', 'soft' => '#f3e1db', 'primary' => '#b3604f', 'accent' => '#e3a690', 'ink' => '#55362f', 'on' => '#ffffff']],
                'anggur' => ['name' => 'Anggur', 'colors' => ['bg' => '#f8f1f4', 'surface' => '#fdfafb', 'soft' => '#e5d4db', 'primary' => '#6d2440', 'accent' => '#c48a9e', 'ink' => '#3b1f2a', 'on' => '#ffffff']],
            ],
        ],
        'anggrek-bulan' => [
            'name' => 'Anggrek Bulan', 'category' => 'floral', 'sort' => 113,
            'description' => 'Anggrek bulan putih ungu, bunga nasional yang anggun.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'bordered', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['cormorant', 'pinyon', 'lora'], 'illustration' => 'bouquet'],
            'variants' => [
                'ungu' => ['name' => 'Ungu', 'colors' => ['bg' => '#f8f4fa', 'surface' => '#fdfbfd', 'soft' => '#e2d8e6', 'primary' => '#5e2d6e', 'accent' => '#b998c9', 'ink' => '#35223b', 'on' => '#ffffff']],
                'putih' => ['name' => 'Putih', 'colors' => ['bg' => '#fbfbf9', 'surface' => '#fefefd', 'soft' => '#e2e3e5', 'primary' => '#4b4f6b', 'accent' => '#c7b6d6', 'ink' => '#2e2f3a', 'on' => '#ffffff']],
                'magenta' => ['name' => 'Magenta', 'colors' => ['bg' => '#fcf3f7', 'surface' => '#fefbfc', 'soft' => '#ecd6e1', 'primary' => '#8a2457', 'accent' => '#d68fb2', 'ink' => '#461f32', 'on' => '#ffffff']],
            ],
        ],
        'garden-party' => [
            'name' => 'Garden Party', 'category' => 'floral', 'sort' => 114,
            'description' => 'Pesta kebun dengan lengkung bunga dan lampu gantung.',
            'engine' => ['hero' => 'photo', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'leaf', 'pattern' => 'none', 'fonts' => ['dm-serif', 'great-vibes', 'inter'], 'illustration' => 'taman'],
            'variants' => [
                'hijau' => ['name' => 'Hijau Segar', 'colors' => ['bg' => '#f3f7f0', 'surface' => '#fbfcfa', 'soft' => '#d8e1d7', 'primary' => '#2f5b3b', 'accent' => '#d6a650', 'ink' => '#233428', 'on' => '#ffffff']],
                'pink' => ['name' => 'Pink', 'colors' => ['bg' => '#fbf4f4', 'surface' => '#fefbfb', 'soft' => '#eddcde', 'primary' => '#9a4559', 'accent' => '#7a9a6a', 'ink' => '#4c2c33', 'on' => '#ffffff']],
                'malam' => ['name' => 'Malam', 'colors' => ['bg' => '#13201a', 'surface' => '#212d28', 'soft' => '#2d3933', 'primary' => '#e3bd6a', 'accent' => '#e3bd6a', 'ink' => '#f3ebdb', 'on' => '#13201a']],
            ],
        ],
        'babys-breath' => [
            'name' => 'Baby\'s Breath', 'category' => 'floral', 'sort' => 115,
            'description' => 'Bunga kecil yang lembut dan bersih.',
            'engine' => ['hero' => 'arch', 'shape' => 'circle', 'heading' => 'script', 'sections' => 'plain', 'ornament' => 'leaf', 'pattern' => 'dots', 'fonts' => ['cormorant', 'alex-brush', 'josefin'], 'illustration' => 'wildflowers'],
            'variants' => [
                'putih' => ['name' => 'Putih', 'colors' => ['bg' => '#fafaf8', 'surface' => '#fdfdfd', 'soft' => '#e3e5e0', 'primary' => '#55634f', 'accent' => '#b9c2a8', 'ink' => '#32372f', 'on' => '#ffffff']],
                'krem' => ['name' => 'Krem', 'colors' => ['bg' => '#faf6ef', 'surface' => '#fdfcf9', 'soft' => '#e6e0d7', 'primary' => '#6e5c41', 'accent' => '#c9b48a', 'ink' => '#3b342a', 'on' => '#ffffff']],
                'biru' => ['name' => 'Biru Muda', 'colors' => ['bg' => '#f4f7fa', 'surface' => '#fbfcfd', 'soft' => '#dbe1e8', 'primary' => '#43597a', 'accent' => '#a9bcd2', 'ink' => '#2b3340', 'on' => '#ffffff']],
            ],
        ],
        'tulip-pastel' => [
            'name' => 'Tulip Pastel', 'category' => 'floral', 'sort' => 116,
            'description' => 'Taman tulip warna pastel yang ceria.',
            'engine' => ['hero' => 'editorial', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['dm-serif', 'parisienne', 'montserrat'], 'illustration' => 'wildflowers'],
            'variants' => [
                'pink' => ['name' => 'Pink', 'colors' => ['bg' => '#fdf5f5', 'surface' => '#fefcfc', 'soft' => '#f2dee1', 'primary' => '#b04f63', 'accent' => '#f0b7c0', 'ink' => '#542f37', 'on' => '#ffffff']],
                'kuning' => ['name' => 'Kuning', 'colors' => ['bg' => '#fdf9ec', 'surface' => '#fefdf8', 'soft' => '#efe6ce', 'primary' => '#9b7419', 'accent' => '#ebc864', 'ink' => '#4c3d1b', 'on' => '#ffffff']],
                'ungu' => ['name' => 'Ungu', 'colors' => ['bg' => '#f8f5fb', 'surface' => '#fdfcfe', 'soft' => '#e5dded', 'primary' => '#6d4d94', 'accent' => '#c4b0e0', 'ink' => '#3b2f4a', 'on' => '#ffffff']],
            ],
        ],
        'kamboja-putih' => [
            'name' => 'Kamboja Putih', 'category' => 'floral', 'sort' => 117,
            'description' => 'Kamboja putih yang tenang dan suci.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'bordered', 'ornament' => 'lotus', 'pattern' => 'none', 'fonts' => ['cormorant', 'pinyon', 'lora'], 'illustration' => 'frangipani'],
            'variants' => [
                'hijau' => ['name' => 'Hijau', 'colors' => ['bg' => '#f4f7f2', 'surface' => '#fbfcfa', 'soft' => '#dae1da', 'primary' => '#3b5946', 'accent' => '#d9b45c', 'ink' => '#28332c', 'on' => '#ffffff']],
                'gading' => ['name' => 'Gading', 'colors' => ['bg' => '#fbf8f1', 'surface' => '#fefdfa', 'soft' => '#e7e2d7', 'primary' => '#6b5a38', 'accent' => '#e2bf63', 'ink' => '#3a3427', 'on' => '#ffffff']],
                'biru' => ['name' => 'Biru', 'colors' => ['bg' => '#f2f6f8', 'surface' => '#fafcfd', 'soft' => '#d7dee3', 'primary' => '#2e4e63', 'accent' => '#e1bb6a', 'ink' => '#232f37', 'on' => '#ffffff']],
            ],
        ],
        'cotton-candy' => [
            'name' => 'Cotton Candy', 'category' => 'pastel', 'sort' => 118,
            'description' => 'Pink dan biru pastel yang manis seperti gulali.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'floral', 'pattern' => 'dots', 'fonts' => ['playfair', 'great-vibes', 'josefin'], 'illustration' => 'bouquet'],
            'variants' => [
                'pink' => ['name' => 'Pink Biru', 'colors' => ['bg' => '#fdf4f7', 'surface' => '#fefbfc', 'soft' => '#f4dee6', 'primary' => '#c0587a', 'accent' => '#8fb8d8', 'ink' => '#5a3340', 'on' => '#ffffff']],
                'biru' => ['name' => 'Biru Pink', 'colors' => ['bg' => '#f3f8fc', 'surface' => '#fbfdfe', 'soft' => '#dbe6f0', 'primary' => '#4a7aa8', 'accent' => '#eba9bf', 'ink' => '#2d4051', 'on' => '#ffffff']],
                'lilac' => ['name' => 'Lilac', 'colors' => ['bg' => '#f8f4fc', 'surface' => '#fdfbfe', 'soft' => '#e9dff1', 'primary' => '#8a5fb0', 'accent' => '#f2b6cc', 'ink' => '#463554', 'on' => '#ffffff']],
            ],
        ],
        'mint-macaron' => [
            'name' => 'Mint Macaron', 'category' => 'pastel', 'sort' => 119,
            'description' => 'Hijau mint segar dengan sentuhan peach.',
            'engine' => ['hero' => 'arch', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'leaf', 'pattern' => 'dots', 'fonts' => ['dm-serif', 'alex-brush', 'josefin'], 'illustration' => 'eucalyptus'],
            'variants' => [
                'mint' => ['name' => 'Mint', 'colors' => ['bg' => '#f1f8f5', 'surface' => '#fafdfc', 'soft' => '#d8e6e1', 'primary' => '#3b7a66', 'accent' => '#f0b08e', 'ink' => '#284038', 'on' => '#ffffff']],
                'peach' => ['name' => 'Peach', 'colors' => ['bg' => '#fdf5f0', 'surface' => '#fefcfa', 'soft' => '#f4e2d9', 'primary' => '#c06c4c', 'accent' => '#8cc4ae', 'ink' => '#5a3a2e', 'on' => '#ffffff']],
                'sage' => ['name' => 'Sage', 'colors' => ['bg' => '#f4f6f0', 'surface' => '#fbfcfa', 'soft' => '#dee3d9', 'primary' => '#5a6e4a', 'accent' => '#e9b8a0', 'ink' => '#343b2d', 'on' => '#ffffff']],
            ],
        ],
        'lilac-dream' => [
            'name' => 'Lilac Dream', 'category' => 'pastel', 'sort' => 120,
            'description' => 'Lilac lembut seperti mimpi.',
            'engine' => ['hero' => 'frame', 'shape' => 'circle', 'heading' => 'script', 'sections' => 'bordered', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['cormorant', 'parisienne', 'lora'], 'illustration' => 'wildflowers'],
            'variants' => [
                'lilac' => ['name' => 'Lilac', 'colors' => ['bg' => '#f8f5fc', 'surface' => '#fdfcfe', 'soft' => '#e5deee', 'primary' => '#6e4f99', 'accent' => '#c8b4e6', 'ink' => '#3b2f4c', 'on' => '#ffffff']],
                'abu' => ['name' => 'Abu Lilac', 'colors' => ['bg' => '#f6f5f8', 'surface' => '#fcfcfd', 'soft' => '#e0dfe5', 'primary' => '#5a5672', 'accent' => '#bdb3d1', 'ink' => '#34323d', 'on' => '#ffffff']],
                'pink' => ['name' => 'Pink Lilac', 'colors' => ['bg' => '#fcf5f9', 'surface' => '#fefcfd', 'soft' => '#eddee7', 'primary' => '#93507a', 'accent' => '#dcb0cf', 'ink' => '#493040', 'on' => '#ffffff']],
            ],
        ],
        'peach-sorbet' => [
            'name' => 'Peach Sorbet', 'category' => 'pastel', 'sort' => 121,
            'description' => 'Peach dan krem yang hangat dan segar.',
            'engine' => ['hero' => 'editorial', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'plain', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['playfair', 'alex-brush', 'inter'], 'illustration' => 'bouquet'],
            'variants' => [
                'peach' => ['name' => 'Peach', 'colors' => ['bg' => '#fdf5ef', 'surface' => '#fefcf9', 'soft' => '#f3dfd6', 'primary' => '#b4593c', 'accent' => '#f0b593', 'ink' => '#563328', 'on' => '#ffffff']],
                'aprikot' => ['name' => 'Aprikot', 'colors' => ['bg' => '#fdf7ec', 'surface' => '#fefcf8', 'soft' => '#f1e3cf', 'primary' => '#a9671c', 'accent' => '#f0c27f', 'ink' => '#52381c', 'on' => '#ffffff']],
                'koral' => ['name' => 'Koral', 'colors' => ['bg' => '#fdf3f1', 'surface' => '#fefbfa', 'soft' => '#f3dad8', 'primary' => '#b4443f', 'accent' => '#f3a89c', 'ink' => '#562b29', 'on' => '#ffffff']],
            ],
        ],
        'royal-gold' => [
            'name' => 'Royal Gold', 'category' => 'luxury', 'sort' => 122,
            'description' => 'Emas dan krem bak undangan kerajaan.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'deco', 'pattern' => 'deco', 'fonts' => ['cinzel', 'pinyon', 'cormorant'], 'illustration' => 'taman'],
            'variants' => [
                'krem' => ['name' => 'Krem Emas', 'colors' => ['bg' => '#f8f2e6', 'surface' => '#fdfaf6', 'soft' => '#e2daca', 'primary' => '#5a4320', 'accent' => '#c6a050', 'ink' => '#342b1e', 'on' => '#ffffff']],
                'hitam' => ['name' => 'Hitam Emas', 'colors' => ['bg' => '#121110', 'surface' => '#201f1e', 'soft' => '#2c2b2a', 'primary' => '#d8b765', 'accent' => '#d8b765', 'ink' => '#f2eada', 'on' => '#121110']],
                'putih' => ['name' => 'Putih Emas', 'colors' => ['bg' => '#fcfbf8', 'surface' => '#fefefd', 'soft' => '#e1e0dc', 'primary' => '#3d3a33', 'accent' => '#c9a65a', 'ink' => '#292725', 'on' => '#ffffff']],
            ],
        ],
        'midnight-sapphire' => [
            'name' => 'Midnight Sapphire', 'category' => 'luxury', 'sort' => 123,
            'description' => 'Biru malam pekat dengan kilau perak.',
            'engine' => ['hero' => 'photo', 'shape' => 'square', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'deco', 'pattern' => 'deco', 'fonts' => ['cinzel', 'great-vibes', 'montserrat'], 'illustration' => 'kota'],
            'variants' => [
                'safir' => ['name' => 'Safir', 'colors' => ['bg' => '#0e1630', 'surface' => '#1c243c', 'soft' => '#293047', 'primary' => '#c9d2e3', 'accent' => '#c9d2e3', 'ink' => '#f0ede9', 'on' => '#0e1630']],
                'emas' => ['name' => 'Safir Emas', 'colors' => ['bg' => '#0e1630', 'surface' => '#1c243c', 'soft' => '#293047', 'primary' => '#d9b968', 'accent' => '#d9b968', 'ink' => '#f2eada', 'on' => '#0e1630']],
                'ungu' => ['name' => 'Ungu Malam', 'colors' => ['bg' => '#1a1230', 'surface' => '#28203c', 'soft' => '#332c47', 'primary' => '#d4c3ee', 'accent' => '#d4c3ee', 'ink' => '#f1ebea', 'on' => '#1a1230']],
            ],
        ],
        'champagne-toast' => [
            'name' => 'Champagne Toast', 'category' => 'luxury', 'sort' => 124,
            'description' => 'Sampanye, mutiara, dan bunga putih.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['cormorant', 'pinyon', 'lora'], 'illustration' => 'bouquet'],
            'variants' => [
                'sampanye' => ['name' => 'Sampanye', 'colors' => ['bg' => '#f9f4ea', 'surface' => '#fdfbf8', 'soft' => '#e5ded1', 'primary' => '#6c5634', 'accent' => '#cdb07a', 'ink' => '#3a3225', 'on' => '#ffffff']],
                'mutiara' => ['name' => 'Mutiara', 'colors' => ['bg' => '#f7f6f3', 'surface' => '#fcfcfb', 'soft' => '#dfdedb', 'primary' => '#4e4b45', 'accent' => '#bfb6a6', 'ink' => '#2f2e2c', 'on' => '#ffffff']],
                'rose' => ['name' => 'Rose Gold', 'colors' => ['bg' => '#faf2ef', 'surface' => '#fdfaf9', 'soft' => '#eadbd7', 'primary' => '#8a4f45', 'accent' => '#d7a292', 'ink' => '#462f2c', 'on' => '#ffffff']],
            ],
        ],
        'black-orchid' => [
            'name' => 'Black Orchid', 'category' => 'luxury', 'sort' => 125,
            'description' => 'Anggrek gelap yang misterius dan elegan.',
            'engine' => ['hero' => 'photo', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'bordered', 'ornament' => 'floral', 'pattern' => 'none', 'fonts' => ['playfair', 'pinyon', 'lora'], 'illustration' => 'bouquet'],
            'variants' => [
                'ungu' => ['name' => 'Ungu Hitam', 'colors' => ['bg' => '#140f17', 'surface' => '#221d25', 'soft' => '#2e2931', 'primary' => '#d6b3e3', 'accent' => '#c79ad9', 'ink' => '#efe7e8', 'on' => '#140f17']],
                'merah' => ['name' => 'Merah Hitam', 'colors' => ['bg' => '#160e10', 'surface' => '#241c1e', 'soft' => '#30292a', 'primary' => '#e0a0a6', 'accent' => '#d08a92', 'ink' => '#f1e5df', 'on' => '#160e10']],
                'emas' => ['name' => 'Emas Hitam', 'colors' => ['bg' => '#121010', 'surface' => '#201e1e', 'soft' => '#2c2a2a', 'primary' => '#e0c27c', 'accent' => '#e0c27c', 'ink' => '#f2ebdd', 'on' => '#121010']],
            ],
        ],
        'sakinah' => [
            'name' => 'Sakinah', 'category' => 'islami', 'sort' => 126,
            'description' => 'Siluet masjid dan geometri islami yang menenangkan.',
            'engine' => ['hero' => 'frame', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'star', 'pattern' => 'star', 'fonts' => ['marcellus', 'pinyon', 'lora'], 'illustration' => 'masjid'],
            'variants' => [
                'hijau' => ['name' => 'Hijau', 'colors' => ['bg' => '#f0f5f1', 'surface' => '#fafcfa', 'soft' => '#d3dfd8', 'primary' => '#1f5640', 'accent' => '#c19c52', 'ink' => '#1d322a', 'on' => '#ffffff']],
                'krem' => ['name' => 'Krem', 'colors' => ['bg' => '#faf6ee', 'surface' => '#fdfcf9', 'soft' => '#e4ded3', 'primary' => '#5e4a2b', 'accent' => '#c3a35f', 'ink' => '#352d22', 'on' => '#ffffff']],
                'biru' => ['name' => 'Biru', 'colors' => ['bg' => '#eff4f7', 'surface' => '#f9fbfc', 'soft' => '#d2dce2', 'primary' => '#1e4a63', 'accent' => '#c19c52', 'ink' => '#1d2d37', 'on' => '#ffffff']],
            ],
        ],
        'bulan-sabit' => [
            'name' => 'Bulan Sabit', 'category' => 'islami', 'sort' => 127,
            'description' => 'Bulan sabit dan bintang di langit malam.',
            'engine' => ['hero' => 'photo', 'shape' => 'arch', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'star', 'pattern' => 'star', 'fonts' => ['cinzel', 'great-vibes', 'cormorant'], 'illustration' => 'masjid'],
            'variants' => [
                'navy' => ['name' => 'Navy', 'colors' => ['bg' => '#0f1a33', 'surface' => '#1d283f', 'soft' => '#293349', 'primary' => '#e3c787', 'accent' => '#e3c787', 'ink' => '#f3ecde', 'on' => '#0f1a33']],
                'hijau' => ['name' => 'Hijau Malam', 'colors' => ['bg' => '#0d241e', 'surface' => '#1c312c', 'soft' => '#283c37', 'primary' => '#e3c787', 'accent' => '#e3c787', 'ink' => '#f3ecde', 'on' => '#0d241e']],
                'ungu' => ['name' => 'Ungu Malam', 'colors' => ['bg' => '#1c1533', 'surface' => '#2a233f', 'soft' => '#352f49', 'primary' => '#e6cb8d', 'accent' => '#e6cb8d', 'ink' => '#f3ecdf', 'on' => '#1c1533']],
            ],
        ],
        'taman-firdaus' => [
            'name' => 'Taman Firdaus', 'category' => 'islami', 'sort' => 128,
            'description' => 'Taman bunga dan lengkung mihrab yang teduh.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'star', 'pattern' => 'none', 'fonts' => ['cormorant', 'pinyon', 'lora'], 'illustration' => 'taman'],
            'variants' => [
                'hijau' => ['name' => 'Hijau Taman', 'colors' => ['bg' => '#f2f6f0', 'surface' => '#fafcfa', 'soft' => '#d7e0d7', 'primary' => '#2f5a3f', 'accent' => '#cba65a', 'ink' => '#233429', 'on' => '#ffffff']],
                'pink' => ['name' => 'Pink', 'colors' => ['bg' => '#fbf4f4', 'surface' => '#fefbfb', 'soft' => '#eadadd', 'primary' => '#7e3b4f', 'accent' => '#c9a362', 'ink' => '#41282f', 'on' => '#ffffff']],
                'putih' => ['name' => 'Putih', 'colors' => ['bg' => '#fafaf7', 'surface' => '#fdfdfc', 'soft' => '#e0e1dd', 'primary' => '#3f4a3f', 'accent' => '#c9a362', 'ink' => '#292d29', 'on' => '#ffffff']],
            ],
        ],
        'city-lights' => [
            'name' => 'City Lights', 'category' => 'modern', 'sort' => 129,
            'description' => 'Gemerlap lampu kota untuk pasangan urban.',
            'engine' => ['hero' => 'editorial', 'shape' => 'square', 'heading' => 'caps', 'sections' => 'plain', 'ornament' => 'line', 'pattern' => 'none', 'fonts' => ['dm-serif', 'parisienne', 'montserrat'], 'illustration' => 'kota'],
            'variants' => [
                'malam' => ['name' => 'Malam', 'colors' => ['bg' => '#101218', 'surface' => '#1e2026', 'soft' => '#2a2c31', 'primary' => '#e6c07a', 'accent' => '#e6c07a', 'ink' => '#f3ebdd', 'on' => '#101218']],
                'ungu' => ['name' => 'Ungu Neon', 'colors' => ['bg' => '#14101c', 'surface' => '#221e2a', 'soft' => '#2e2a35', 'primary' => '#e2a6e6', 'accent' => '#9ad1e8', 'ink' => '#eaedea', 'on' => '#14101c']],
                'terang' => ['name' => 'Terang', 'colors' => ['bg' => '#f6f6f4', 'surface' => '#fcfcfb', 'soft' => '#d8d8d9', 'primary' => '#1d2230', 'accent' => '#c79a52', 'ink' => '#1c1e24', 'on' => '#ffffff']],
            ],
        ],
        'minimal-arch' => [
            'name' => 'Minimal Arch', 'category' => 'minimalis', 'sort' => 130,
            'description' => 'Lengkung sederhana dan tipografi bersih.',
            'engine' => ['hero' => 'arch', 'shape' => 'arch', 'heading' => 'serif', 'sections' => 'plain', 'ornament' => 'line', 'pattern' => 'none', 'fonts' => ['cormorant', 'great-vibes', 'inter'], 'illustration' => null],
            'variants' => [
                'pasir' => ['name' => 'Pasir', 'colors' => ['bg' => '#f6f2ec', 'surface' => '#fcfaf8', 'soft' => '#ded9d2', 'primary' => '#4a3f33', 'accent' => '#b9a27c', 'ink' => '#2d2925', 'on' => '#ffffff']],
                'abu' => ['name' => 'Abu', 'colors' => ['bg' => '#f4f4f3', 'surface' => '#fbfbfb', 'soft' => '#d9d9d8', 'primary' => '#333333', 'accent' => '#9c9c94', 'ink' => '#252525', 'on' => '#ffffff']],
                'sage' => ['name' => 'Sage', 'colors' => ['bg' => '#f2f4ef', 'surface' => '#fafbf9', 'soft' => '#daded6', 'primary' => '#46553f', 'accent' => '#a7b096', 'ink' => '#2c3229', 'on' => '#ffffff']],
            ],
        ],
        'bold-serif' => [
            'name' => 'Bold Serif', 'category' => 'modern', 'sort' => 131,
            'description' => 'Huruf besar dan tegas, gaya majalah.',
            'engine' => ['hero' => 'editorial', 'shape' => 'square', 'heading' => 'serif', 'sections' => 'plain', 'ornament' => 'line', 'pattern' => 'none', 'fonts' => ['dm-serif', 'alex-brush', 'montserrat'], 'illustration' => null],
            'variants' => [
                'merah' => ['name' => 'Merah', 'colors' => ['bg' => '#f7f2ee', 'surface' => '#fcfaf9', 'soft' => '#ead6d2', 'primary' => '#9a2b24', 'accent' => '#1e1e1e', 'ink' => '#4c221f', 'on' => '#ffffff']],
                'hijau' => ['name' => 'Hijau', 'colors' => ['bg' => '#f2f4f0', 'surface' => '#fafbfa', 'soft' => '#d5dcd5', 'primary' => '#20452f', 'accent' => '#c08b3c', 'ink' => '#1e2c23', 'on' => '#ffffff']],
                'hitam' => ['name' => 'Hitam', 'colors' => ['bg' => '#111111', 'surface' => '#1f1f1f', 'soft' => '#2b2b2b', 'primary' => '#f1ece4', 'accent' => '#c9a15a', 'ink' => '#f0e7d9', 'on' => '#111111']],
            ],
        ],
        'pine-ridge' => [
            'name' => 'Pine Ridge', 'category' => 'rustic', 'sort' => 132,
            'description' => 'Pegunungan pinus untuk pernikahan di alam terbuka.',
            'engine' => ['hero' => 'photo', 'shape' => 'rounded', 'heading' => 'serif', 'sections' => 'cards', 'ornament' => 'leaf', 'pattern' => 'leaf', 'fonts' => ['lora', 'alex-brush', 'josefin'], 'illustration' => 'pegunungan'],
            'variants' => [
                'hutan' => ['name' => 'Hutan', 'colors' => ['bg' => '#eff2ec', 'surface' => '#f9faf8', 'soft' => '#d4dad2', 'primary' => '#2e4a33', 'accent' => '#b08a4e', 'ink' => '#232d25', 'on' => '#ffffff']],
                'kabut' => ['name' => 'Kabut', 'colors' => ['bg' => '#f0f2f3', 'surface' => '#fafafb', 'soft' => '#d7dbde', 'primary' => '#3a4f5c', 'accent' => '#a99269', 'ink' => '#272f34', 'on' => '#ffffff']],
                'senja' => ['name' => 'Senja', 'colors' => ['bg' => '#f8efe6', 'surface' => '#fdf9f6', 'soft' => '#e5d6cb', 'primary' => '#6e3d24', 'accent' => '#d39a5b', 'ink' => '#3b291f', 'on' => '#ffffff']],
            ],
        ],
        'sawah-desa' => [
            'name' => 'Sawah Desa', 'category' => 'rustic', 'sort' => 133,
            'description' => 'Hamparan sawah berundak dan kelapa melambai.',
            'engine' => ['hero' => 'arch', 'shape' => 'rounded', 'heading' => 'script', 'sections' => 'cards', 'ornament' => 'leaf', 'pattern' => 'dots', 'fonts' => ['lora', 'parisienne', 'josefin'], 'illustration' => 'sawah'],
            'variants' => [
                'hijau' => ['name' => 'Hijau Padi', 'colors' => ['bg' => '#f2f6ea', 'surface' => '#fafcf8', 'soft' => '#d9e0ce', 'primary' => '#3e5a24', 'accent' => '#c7a24a', 'ink' => '#29341f', 'on' => '#ffffff']],
                'emas' => ['name' => 'Emas Panen', 'colors' => ['bg' => '#faf4e4', 'surface' => '#fdfbf6', 'soft' => '#e8dec8', 'primary' => '#7a5a1c', 'accent' => '#a3b55a', 'ink' => '#40341c', 'on' => '#ffffff']],
                'pagi' => ['name' => 'Pagi', 'colors' => ['bg' => '#f1f5f6', 'surface' => '#fafcfc', 'soft' => '#d6dfe1', 'primary' => '#2f5560', 'accent' => '#d1a65a', 'ink' => '#233236', 'on' => '#ffffff']],
            ],
        ],
        'vintage-letter' => [
            'name' => 'Vintage Letter', 'category' => 'vintage', 'sort' => 134,
            'description' => 'Surat lama, perangko, dan bunga kering.',
            'engine' => ['hero' => 'frame', 'shape' => 'square', 'heading' => 'serif', 'sections' => 'bordered', 'ornament' => 'line', 'pattern' => 'dots', 'fonts' => ['lora', 'pinyon', 'cormorant'], 'illustration' => 'bouquet'],
            'variants' => [
                'sepia' => ['name' => 'Sepia', 'colors' => ['bg' => '#f1e8d8', 'surface' => '#faf7f1', 'soft' => '#dcd1bf', 'primary' => '#5a4128', 'accent' => '#a1804f', 'ink' => '#342a21', 'on' => '#ffffff']],
                'dusty' => ['name' => 'Dusty Rose', 'colors' => ['bg' => '#f3e7e3', 'surface' => '#fbf7f5', 'soft' => '#e1d0cc', 'primary' => '#73413f', 'accent' => '#b78c7f', 'ink' => '#3d2a29', 'on' => '#ffffff']],
                'biru' => ['name' => 'Biru Pudar', 'colors' => ['bg' => '#e9edee', 'surface' => '#f7f9f9', 'soft' => '#d0d7d9', 'primary' => '#3a4f5a', 'accent' => '#9c8a6a', 'ink' => '#272f34', 'on' => '#ffffff']],
            ],
        ],
        'retro-film' => [
            'name' => 'Retro Film', 'category' => 'vintage', 'sort' => 135,
            'description' => 'Nuansa film lawas dan garis art deco.',
            'engine' => ['hero' => 'editorial', 'shape' => 'square', 'heading' => 'caps', 'sections' => 'bordered', 'ornament' => 'deco', 'pattern' => 'deco', 'fonts' => ['cinzel', 'parisienne', 'montserrat'], 'illustration' => 'kota'],
            'variants' => [
                'sepia' => ['name' => 'Sepia', 'colors' => ['bg' => '#efe5d3', 'surface' => '#f9f6f0', 'soft' => '#d6cbb9', 'primary' => '#3c2a1c', 'accent' => '#b4874a', 'ink' => '#28211c', 'on' => '#ffffff']],
                'hitam' => ['name' => 'Hitam Putih', 'colors' => ['bg' => '#151515', 'surface' => '#232323', 'soft' => '#2f2f2f', 'primary' => '#eeeeee', 'accent' => '#9a9a9a', 'ink' => '#eae7e0', 'on' => '#151515']],
                'hijau' => ['name' => 'Hijau Tua', 'colors' => ['bg' => '#e9ede4', 'surface' => '#f7f9f6', 'soft' => '#cdd4ca', 'primary' => '#24382b', 'accent' => '#b48d4c', 'ink' => '#1f2722', 'on' => '#ffffff']],
            ],
        ],
    ],
];
