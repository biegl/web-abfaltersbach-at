<?php

return [

    /*
     * The view id of which you want to display data.
     */
    'property_id' => env('ANALYTICS_VIEW_ID', '12345678'),

    /*
     * Path to the client secret json file. Take a look at the README of this package
     * to learn how to get this file. You can also pass the credentials as an array
     * instead of a file path.
     */
    'service_account_credentials_json' => [
        'type' => env('ANALYTICS_TYPE', 'service_account'),
        'project_id' => env('ANALYTICS_PROJECT_ID', 'your-project-id'),
        'private_key_id' => env('ANALYTICS_PRIVATE_KEY_ID', 'your-private-key-id'),
        'private_key' => env('ANALYTICS_PRIVATE_KEY', 'your-private-key'),
        'client_email' => env('ANALYTICS_CLIENT_EMAIL', 'your-client-email'),
        'client_id' => env('ANALYTICS_CLIENT_ID', 'your-client-id'),
        'auth_uri' => env('ANALYTICS_AUTH_URI', 'https://accounts.google.com/o/oauth2/auth'),
        'token_uri' => env('ANALYTICS_TOKEN_URI', 'https://oauth2.googleapis.com/token'),
        'auth_provider_x509_cert_url' => env('ANALYTICS_AUTH_PROVIDER_X509_CERT_URL', 'https://www.googleapis.com/oauth2/v1/certs'),
        'client_x509_cert_url' => env('ANALYTICS_CLIENT_X509_CERT_URL', 'your-client-x509-cert-url'),
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
