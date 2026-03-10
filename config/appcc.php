<?php

return [
    'url' => env('APPCC_URL'),
    'token' => env('APPCC_TOKEN'),
    'timeout' => env('APPCC_TIMEOUT', 30),
    'queue' => env('APPCC_QUEUE', 'default'),
];
