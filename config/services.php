<?php

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */
return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],
    
    'google' => [
        'client_id' => env('GOOGLE_CLIENTID'),
        'client_secret' => env('GOOGLE_SECRET'),
        'redirect' => 'https://api.coinslide.io/google/callback'
    ],
    'googleMobile' => [
        'client_id' => env('GOOGLE_CLIENTID'),
        'client_secret' => env('GOOGLE_SECRET'),
        'redirect' => 'https://api.coinslide.io/googleMobile/callback'
    ],
    'facebook_config' => [
        'app_id' => env('FACEBOOK_APP_ID'),
        'app_secret' => env('FACEBOOK_APP_SECRET'),
        'redirect' => 'https://api.coinslide.io/facebook/callback/',
        'default_graph_version' => 'v3.0'
    ],

    /*
     * The default list of permissions that are
     * requested when authenticating a new user with your app.
     * The fewer, the better! Leaving this empty is the best.
     * You can overwrite this when creating the login link.
     *
     * Example:
     *
     * 'default_scope' => ['email', 'user_birthday'],
     *
     * For a full list of permissions see:
     *
     * https://developers.facebook.com/docs/facebook-login/permissions
     */
    'default_scope' => ['email']
    
];