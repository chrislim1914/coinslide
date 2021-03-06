<?php

return array(

    'default' => 'mysql',

    'connections' => array(

        # primary database connection
        'mysql' => array(
            'driver'    => env('DB_MYSQL_DRIVER'),
            'host'      => env('DB_MYSQL_HOST'),
            'database'  => env('DB_MYSQL_NAME'),
            'username'  => env('DB_MYSQL_USERNAME'),
            'password'  => env('DB_MYSQL_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),

        # secondary database connection
        'mongodb' => array(
            'driver' => 'mongodb',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', 27017),
            'database' => env('DB_DATABASE'),
            'options' => [
                'database' => env('DB_DATABASE')
            ]
        ),

        #third database connection predis
        'redis' => [

            'client' => 'predis',
        
            'default' => [
                'host' => env('REDIS_HOST', 'localhost'),
                'password' => env('REDIS_PASS', null),
                'port' => env('REDIS_PORT', 6379),
                'database' => 0,
            ],
        
        ],
    ),

    'migrations' => 'migrations'
);