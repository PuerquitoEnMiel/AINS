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

    'google' => [
        'client_id'          => env('GOOGLE_CLIENT_ID'),
        'client_secret'      => env('GOOGLE_CLIENT_SECRET'),
        'redirect'           => env('GOOGLE_REDIRECT_URI', 'http://127.0.0.1:8000/auth/google/callback'),
        'disable_ssl_verify' => env('GOOGLE_DOCS_DISABLE_SSL_VERIFY', false),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'models'  => [
            'gemini-2.5-flash',
            'gemini-2.0-flash',
            'gemini-1.5-flash',
        ],
        'timeout'      => (int) env('GEMINI_TIMEOUT', 60),
        'chat_timeout' => (int) env('GEMINI_CHAT_TIMEOUT', 25),
    ],
];
