<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
        'pagespeed_api_key' => env('GOOGLE_PAGESPEED_API_KEY'),
        'analytics_property_id' => env('GOOGLE_ANALYTICS_PROPERTY_ID'),
        'site_verification_token' => env('GOOGLE_SITE_VERIFICATION_TOKEN'),
        'indexing_api_key' => env('GOOGLE_INDEXING_API_KEY'),
    ],

    'magento' => [
        'base_url' => env('MAGENTO_BASE_URL'),
        'store_code' => env('MAGENTO_STORE_CODE', 'default'),
        'website_code' => env('MAGENTO_WEBSITE_CODE'),
        'token' => env('MAGENTO_API_TOKEN'),
        'timeout' => env('MAGENTO_TIMEOUT', 30),
        'verify_ssl' => env('MAGENTO_VERIFY_SSL', true),
    ],

    'serp' => [
        'provider' => env('SERP_PROVIDER', 'demo'),
        'api_key' => env('SERP_API_KEY'),
    ],

];
