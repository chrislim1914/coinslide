<?php

return array(

    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENTID'),
        'client_secret' => env('FACEBOOK_SECRET'),
    ],

    'google+' => [
        'client_id'     => env('1093020262062-o2smndg4hi9v9h0cmfnqevptnr0smttc.apps.googleusercontent.com'),
        'client_secret' => env('pEy-mgCG4Zq68vo1dfJr08ql'),
        'redirect' => 'http://localhost:8000/callback/google',
    ]
);