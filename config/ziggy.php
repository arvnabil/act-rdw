<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ziggy Route Groups
    |--------------------------------------------------------------------------
    |
    | Here you can define groups of routes that you would like to be available
    | to Ziggy on the frontend. We filter out administrative routes for security
    | and cleaner source code.
    |
    */
    'except' => [
        'filament.*',
        'admin.*',
        'ignition.*',
        'sanctum.*',
    ],
];
