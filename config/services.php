<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sync Service Times
    |--------------------------------------------------------------------------
    |
    | These values determine the times at which the sync service will run. The sync service is responsible for syncing data between the application and
    | external services. The times are set to 5:00 AM and 3:00 PM by default, but can be customized by setting the SYNC_MORNING_HOUR and SYNC_AFTERNOON_HOUR environment variables. The times should be in the format of 'HH:MM' (24-hour format).
    |
    */
    'syncMorningStart' => env('SYNC_MORNING_HOUR', '05:00'),
    'syncAfternoonStart' => env('SYNC_AFTERNOON_HOUR', '12:00'),
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

];
