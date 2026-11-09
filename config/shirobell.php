<?php

return [

    /*
    | Initial admin account, read only by AdminSeeder.
    */
    'admin' => [
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

];
