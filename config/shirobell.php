<?php

return [

    /*
    | Initial admin account, read only by AdminSeeder.
    */
    'admin' => [
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    /*
    | Invitation limits that are product rules rather than admin settings.
    | gallery_max_photos and pricing live in the settings table instead.
    */
    'invitation' => [
        'max_drafts_per_user' => 5,
        'max_events' => 5,
        'max_stories' => 10,
        'image_max_kb' => 8192,
        'image_max_px' => 1600,
        'music_max_kb' => 10240,
    ],

    /*
    | Subdomains a customer can never claim.
    */
    'reserved_slugs' => [
        'admin', 'administrator', 'api', 'app', 'assets', 'billing', 'blog', 'cdn', 'checkout', 'cpanel',
        'dashboard', 'demo', 'dev', 'docs', 'email', 'ftp', 'help', 'imap', 'invoice', 'login', 'mail',
        'media', 'ns1', 'ns2', 'order', 'pay', 'payment', 'pop', 'register', 'root', 'shirobell', 'shop',
        'smtp', 'staging', 'static', 'status', 'storage', 'support', 'test', 'webhook', 'webhooks',
        'webmail', 'www',
    ],

];
