<?php

$useTestingDb = false;
if (env('APP_ENV', 'local') !== 'production') {
    $secret = env('TESTS_SECRET', 'secret');
    if (!empty($_SERVER['HTTP_CUSTOM_TESTS_TOKEN']) && password_verify($secret, $_SERVER['HTTP_CUSTOM_TESTS_TOKEN'])) {
        $useTestingDb = true;
    } elseif (!empty($_SERVER['HTTP_CUSTOM_TESTS_TOKEN']) && $secret === $_SERVER['HTTP_CUSTOM_TESTS_TOKEN']) {
        $useTestingDb = true;
    } elseif (!empty($_COOKIE["Custom-Tests-Token"]) && password_verify($secret, $_COOKIE["Custom-Tests-Token"])) {
        $useTestingDb = true;
    }
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DATABASE_CONNECTION', 'live'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [
        'live' => [
            'driver' => 'mysql',
            'host' => env('DATABASE_HOST', 'mysql'),
            'port' => env('DATABASE_PORT', '3306'),
            'database' => ($useTestingDb ? env('DATABASE_TESTS_NAME', 'testingdb') : env('DATABASE_NAME')),
            'username' => env('DATABASE_USERNAME', ''),
            'password' => env('DATABASE_PASSWORD', ''),
            'timezone' => env('APP_TIMEZONE', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        'tests' => [
            'driver' => 'mysql',
            'host' => env('DATABASE_HOST', 'mysql'),
            'port' => env('DATABASE_PORT', '3306'),
            'database' => env('DATABASE_TESTS_NAME', 'testingdb'),
            'username' => env('DATABASE_USERNAME', ''),
            'password' => env('DATABASE_PASSWORD', ''),
            'timezone' => env('APP_TIMEZONE', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

];
