<?php

require_once __DIR__ . '/../vendor/autoload.php';

use CoMagic\CallApiConfig;
use CoMagic\RestApiClient;
use CoMagic\CallApiClient;

$config = [
    // required for Rest API and optional for Call API
    'login' => 'put_login_here',
    'password' => 'put_password_here',
    // required for Call API if login and password not specified
    'access_token' => 'put_access_token_here',
];

$restApi = new RestApiClient($config);
var_dump(
    $restApi->call(['date_from' => '2025-01-10', 'date_till' => '2025-01-13'])
);

$callApi = new CallApiClient(
    new CallApiConfig(null, null, 'put_access_token_here')
);
var_dump($callApi->listCalls());
var_dump($callApi->metadata());
