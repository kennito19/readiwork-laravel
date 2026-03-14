<?php

return [
    'base_url'    => env('METROPOL_BASE_URL', 'https://api.metropol.co.ke'),
    'port'        => env('METROPOL_PORT', '22225'),
    'version'     => env('METROPOL_VERSION', 'v2_1'),
    'public_key'  => env('METROPOL_PUBLIC_KEY', ''),
    'private_key' => env('METROPOL_PRIVATE_KEY', ''),
    'sandbox'     => env('METROPOL_SANDBOX', false),
];
