<?php

return array(

    'providers' => [
        // Other service providers...
        Laravel\Socialite\SocialiteServiceProvider::class,
    ],

    'aliases' => [

        // ...
        'Socialite' => Laravel\Socialite\Facades\Socialite::class,

    ],
);
