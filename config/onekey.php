<?php

return [

    /*
     |--------------------------------------------------------------------------
     | CAS Configuration
     |--------------------------------------------------------------------------
     |
     | Change the value below via your .env file to override the default
     | service url for the CAS client.
     */
    'debug' => env('ONE_KEY_DEBUG',false),

    'callback_url' => env('ONE_KEY_CALLBACK_URL', env('APP_URL') . '/onekey/callback'),
];
