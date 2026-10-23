<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Account
    |--------------------------------------------------------------------------
    |
    | The single admin (portfolio owner) is created by the AdminSeeder from
    | these values. The email also receives new contact form messages.
    |
    */

    'admin' => [
        'name' => env('ADMIN_NAME', 'Wayan'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

];
