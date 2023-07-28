# PHP Library for epay v2 epay.homebank.kz

### Install
`composer require relesssar/esedo`

# Documentation
https://epayment.kz/docs

### Usage example
```php
use Relesssar\Epay2\Epay2;

$epay = new Epay2($_ENV['EPAY_IS_TEST_MODE'],$_ENV['EPAY_HOST'],$_ENV['EPAY_TERMINALID'],$_ENV['EPAY_CLIENTID'],$_ENV['EPAY_CLIENTSECRET']);
$token =  $epay->getToken_pay($request);
```