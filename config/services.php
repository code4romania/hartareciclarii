<?php

declare(strict_types=1);

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
    'maps' => [
        'defaults' => [
            'latitude' => 44.4268,
            'longitude' => 26.1025,
        ],
    ],
    'nominatim' => [
        //'url'=>'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat={latitude}&lon={longitude}',
        'url' => 'https://nominatim.openstreetmap.org',
        'reverse' => '/reverse?format=jsonv2&lat={latitude}&lon={longitude}',
    ],

];
