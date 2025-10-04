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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
 
    'tripadvisor16' => [
        'key'  => env('RAPIDAPI_TRIP16_KEY'),
        'host' => env('RAPIDAPI_TRIP16_HOST'),
        'base' => env('RAPIDAPI_TRIP16_BASE'),
    ],

    'tripadvisor_com1' => [
        'key'  => env('RAPIDAPI_TRCOM1_KEY'),
        'host' => env('RAPIDAPI_TRCOM1_HOST'),
        'base' => env('RAPIDAPI_TRCOM1_BASE'),
    ],
   
    'rapidapi_cache' => [
        'ttl' => (int) env('RAPIDAPI_CACHE_TTL_SECONDS', 900),
        'geo_ttl' => (int) env('RAPIDAPI_GEOID_TTL_SECONDS', 86400),
    ],
];

