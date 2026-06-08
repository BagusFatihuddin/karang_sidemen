<?php

return [

    'paths' => [
        'api/*',
    ],

    'allowed_methods' => ['*'],

'allowed_origins' => explode(',', env(
    'APP_ALLOWED_ORIGINS',
    'http://localhost:5173,http://localhost:4173'
)),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];