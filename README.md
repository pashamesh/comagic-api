# CoMagic API php client
CoMagic API php client for https://www.comagic.ru/support/api/

## Installation
To get started, install package via the Composer package manager:

`composer require pashamesh/comagic-api`

## Usage

### Init RestAPI client
```php
use CoMagic\RestApiClient;

$restClient = new RestApiClient([
    'login' => 'put_login_here',
    'password' => 'put_password_here'
]);
```
### Do RestAPI request
Example how to get calls info:
```php
$data = $restClient->call(['date_from' => '2017-01-10', 'date_till' => '2017-01-13']);
```
