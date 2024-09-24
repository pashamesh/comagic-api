# [Uiscom](https://www.uiscom.ru/) & [CoMagic](https://main.comagic.ru/) API PHP client
CoMagic php client for:
- Rest API https://www.comagic.ru/support/api/
- Call API

## Installation
To get started, install package via the Composer package manager:

`composer require pashamesh/comagic-api`

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
$config = [
    // required for Rest API and optional for Call API
    'login' => 'put_login_here',
    'password' => 'put_password_here',
    'endpoint' => [
        'rest_api' => 'https://api.uiscom.ru/api/'
    ],
    // required for Call API if login and password not specified
    'access_token' => 'put_access_token_here',
];

```

Do not forget to add `Call API` permissions to user if you want to use login and
password authorization for Call API.


### Rest API
```php
use CoMagic\RestApiClient;

$restApi = new RestApiClient($config);
var_dump(
    $restApi->call(['date_from' => '2017-01-10', 'date_till' => '2017-01-13'])
);
```

### Call API
API Methods names need to be specified in CamelCase
```php
$callApi = new CallApiClient($config);
var_dump($callApi->listCalls());
```

It's possible to get response metadata after API request is made
```php
var_dump($callApi->metadata());
```