# [UIS](https://www.uiscom.ru/) & [CoMagic](https://main.comagic.ru/) API PHP client
UIS and CoMagic PHP client for the following APIs:
- [Call API](https://www.uiscom.ru/academiya/spravochnyj-centr/dokumentatsiya-api/call_api/)

## Requirements
This package requires PHP 7.4 or above.

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
use CoMagic\CallApiConfig;

$config = new CallApiConfig('login', 'password', 'access_token')
```

Do not forget to add `Call API` permissions to user if you want to use login and
password authorization for Call API.

### Call API
API Methods names need to be specified in CamelCase
```php
use CoMagic\CallApiConfig;
use CoMagic\CallApiClient;

$config = new CallApiConfig('login', 'password', 'access_token');
$callApi = new CallApiClient($config);
var_dump($callApi->listCalls());
```

It's possible to get response metadata after API request is made
```php
var_dump($callApi->metadata());
```
