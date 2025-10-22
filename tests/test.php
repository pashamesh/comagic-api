<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Uiscom\CallApiConfig;
use Uiscom\CallApiClient;
use Uiscom\DataApiClient;
use Uiscom\DataApiConfig;

$callApi = new CallApiClient(
    new CallApiConfig(null, null, 'put_access_token_here')
);
var_dump($callApi->listCalls());
var_dump($callApi->metadata());

$dataApi = new DataApiClient(
    new DataApiConfig('put_access_token_here')
);
var_dump($dataApi->getCallsReport([
    'date_from' => '2025-01-10 00:00:00',
    'date_till' => '2025-01-13 00:00:00',
]));
var_dump($dataApi->metadata());
