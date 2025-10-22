<?php

require_once __DIR__ . '/../vendor/autoload.php';

use CoMagic\CallApiConfig;
use CoMagic\CallApiClient;

$callApi = new CallApiClient(
    new CallApiConfig(null, null, 'put_access_token_here')
);
var_dump($callApi->listCalls());
var_dump($callApi->metadata());
