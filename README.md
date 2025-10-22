<p align="center">
    <img src="https://github.com/pashamesh/uiscom-api-client/actions/workflows/code_style.yml/badge.svg" alt="Code style">
    <img src="https://github.com/pashamesh/uiscom-api-client/actions/workflows/tests.yml/badge.svg" alt="Tests">
</p>

# [UIScom](https://www.uiscom.ru/) (formerly [CoMagic](https://main.comagic.ru/)) API PHP client
UIS and CoMagic PHP client for the following APIs:
- [Call API](https://www.uiscom.ru/academiya/spravochnyj-centr/dokumentatsiya-api/call_api/)
- [Data API](https://www.uiscom.ru/academiya/spravochnyj-centr/dokumentatsiya-api/data_api/)

## Requirements
This package requires PHP 7.4 or above.

## Installation
To get started, install package via the Composer package manager:

`composer require pashamesh/uiscom-api-client`

## Usage

### Configuring
Array is using to configure Rest API and Call API clients.
```php
$config = [
    // required for Rest API and optional for Call API
    'login' => 'put_login_here',
    'password' => 'put_password_here',
    // required for Call API if login and password not specified
    'access_token' => 'put_access_token_here',
];

```

You also need to change domain if you client of Uiscom by specifying `endpoint`:

```php
use Uiscom\CallApiConfig;

$config = new CallApiConfig('login', 'password', 'access_token')
```

Do not forget to add `Call API` permissions to user if you want to use login and
password authorization for Call API.

### Call API
API Methods names need to be specified in CamelCase

```php
use Uiscom\CallApiConfig;
use Uiscom\CallApiClient;

$config = new CallApiConfig('login', 'password', 'access_token');
$callApi = new CallApiClient($config);
var_dump($callApi->listCalls());
```

It's possible to get response metadata after API request is made
```php
var_dump($callApi->metadata());
```

### Data API

API Methods names need to be specified in CamelCase

```php
use Uiscom\DataApiConfig;
use Uiscom\DataApiClient;

$config = new DataApiConfig('access_token');
$dataApi = new DataApiClient($config);
var_dump(
    $dataApi->getCallsReport([
        'date_from' => '2025-01-10 00:00:00',
        'date_till' => '2025-01-13 23:59:59'
    ])
);
```

It's possible to get response metadata after API request is made

```php
var_dump($dataApi->metadata());
```
