<?php

return [

    /*
     * The view id of which you want to display data.
     */
    'property_id' => env('ANALYTICS_VIEW_ID'),

    /*
     * Path to the client secret json file. Take a look at the README of this package
     * to learn how to get this file. You can also pass the credentials as an array
     * instead of a file path.
     */
    'service_account_credentials_json' => [
        'type' => env('ANALYTICS_TYPE'),
        'project_id' => env('ANALYTICS_PROJECT_ID'),
        'private_key_id' => env('ANALYTICS_PRIVATE_KEY_ID'),
        'private_key' => env('ANALYTICS_PRIVATE_KEY'),
        'client_email' => env('ANALYTICS_CLIENT_EMAIL'),
        'client_id' => env('ANALYTICS_CLIENT_ID'),
        'auth_uri' => env('ANALYTICS_AUTH_URI'),
        'token_uri' => env('ANALYTICS_TOKEN_URI'),
        'auth_provider_x509_cert_url' => env('ANALYTICS_AUTH_PROVIDER_X509_CERT_URL'),
        'client_x509_cert_url' => env('ANALYTICS_CLIENT_X509_CERT_URL'),
    ],

    /*
     * The amount of minutes the Google API responses will be cached.
     * If you set this to zero, the responses won't be cached at all.
     */
    'cache_lifetime_in_minutes' => 60 * 24,

    /*
     * Here you may configure the "store" that the underlying Google_Client will
     * use to store it's data.  You may also add extra parameters that will
     * be passed on setCacheConfig (see docs for google-api-php-client).
     *
     * Optional parameters: "lifetime", "prefix"
     */
    'cache' => [
        'store' => 'file',
    ],
];
