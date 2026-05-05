<?php

return [
    'demo_mode' => env('SEO_DEMO_MODE', true),
    'lookback_days' => env('SEO_LOOKBACK_DAYS', 30),
    'crawler' => [
        'timeout' => env('SEO_CRAWLER_TIMEOUT', 20),
        'verify_ssl' => env('SEO_CRAWLER_VERIFY_SSL', true),
    ],
];
