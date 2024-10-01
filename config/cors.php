<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // 'paths' => ['*'],

    'paths' => [ 'storage/uploads/*','api/*', 'sanctum/csrf-cookie'], //ngrok

    'allowed_methods' => ['*'],

    // 'allowed_origins' => ['*'],
    
    'allowed_origins' => [  //ngrok
        'https://cow-expert-plainly.ngrok-free.app',
        'http://localhost:5173',
        'http://localhost:8000',
        'http://localhost:8888',
        'https://amoebabiolab.netlify.app',     
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
