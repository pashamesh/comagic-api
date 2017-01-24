<?php

require_once __DIR__ . '/../vendor/autoload.php';

use CoMagic\RestApiClient;

$comagic = new RestApiClient([
    'login' => 'put_login_here',
    'password' => 'put_password_here'
]);

var_dump(
    $comagic->call(['date_from' => '2017-01-10', 'date_till' => '2017-01-13'])
);
