<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'notify_url' => env('MIDTRANS_NOTIFY_URL'),
    'simulate' => (bool) env('MIDTRANS_SIMULATE', env('APP_ENV', 'production') !== 'production'),
];
