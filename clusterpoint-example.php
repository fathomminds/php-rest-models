<?php

/*
|--------------------------------------------------------------------------
| API Connections config file
|--------------------------------------------------------------------------
|
| You can set connection by passing the right connection name as
| a string when initializing API. Host, Account ID, Username and Password are
| required fields. Debug value is false by default.
|
| If Debug mode is on, API will print detailed information about
| requests made with Clusterpoint PHP API.
|
| EU host : "https://api-eu.clusterpoint.com/v4"
| US host : "https://api-us.clusterpoint.com/v4"
| UK host : "https://api-uk.clusterpoint.com/v4"
|
*/

return [
    'default' => [
        'host' => getenv('CLUSTERPOINT_HOST'),
        'account_id' => getenv('CLUSTERPOINT_ACCOUNT_ID'),
        'username' => getenv('CLUSTERPOINT_USERNAME'),
        'password' => getenv('CLUSTERPOINT_PASSWORD'),
        'debug' => getenv('CLUSTERPOINT_DEBUG_MODE'),
    ],
    'development' => [
        'host' => 'https://api-eu.clusterpoint.com/v4/',
        'account_id' => 'ACCOUNT_ID',
        'username' => 'USERNAME',
        'password' => 'PASSWORD',
        'debug' => false
    ],
    'eu' => [
        'host' => 'https://api-eu.clusterpoint.com/v4/',
        'account_id' => 'ACCOUNT_ID',
        'username' => 'USERNAME',
        'password' => 'PASSWORD',
        'debug' => false
    ],
    'local' => [
        'host' => 'http://127.0.0.1:5580/v4/',
        'account_id' => 'ACCOUNT_ID',
        'username' => 'USERNAME',
        'password' => 'PASSWORD',
        'debug' => false
    ]
];
