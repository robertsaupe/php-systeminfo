# php-systeminfo

[![Minimum PHP version: 8.2](https://img.shields.io/badge/php-8.2%2B-blue.svg?color=blue&style=for-the-badge)](https://packagist.org/packages/robertsaupe/php-systeminfo)
[![Packagist Version](https://img.shields.io/packagist/v/robertsaupe/php-systeminfo?color=blue&style=for-the-badge)](https://packagist.org/packages/robertsaupe/php-systeminfo)
[![Packagist Downloads](https://img.shields.io/packagist/dt/robertsaupe/php-systeminfo?color=blue&style=for-the-badge)](https://packagist.org/packages/robertsaupe/php-systeminfo)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=for-the-badge)](LICENSE)

php library to get useful information about the system

## Supporting

[GitHub](https://github.com/sponsors/robertsaupe) |
[Patreon](https://www.patreon.com/robertsaupe) |
[PayPal](https://www.paypal.com/donate?hosted_button_id=SQMRNY8YVPCZQ) |
[Amazon](https://www.amazon.de/ref=as_li_ss_tl?ie=UTF8&linkCode=ll2&tag=robertsaupe-21&linkId=b79bc86cee906816af515980cb1db95e&language=de_DE)

## Installing

```sh
composer require robertsaupe/php-systeminfo
```

## Getting started

### Check

```php
use robertsaupe\SystemInfo\Check;

var_dump(Check::isSSL());

var_dump(Check::isText('abc'));
var_dump(Check::isText('abc@example.com'));

var_dump(Check::isMail('abc'));
var_dump(Check::isMail('abc@example.com'));

var_dump(Check::isCli());

var_dump(Check::execAvailable());
var_dump(Check::shellexecAvailable());
var_dump(Check::popenAvailable());

var_dump(Check::programAvailable('dd'));
var_dump(Check::programAvailable('programNotExists'));

var_dump(Check::extensionAvailable('json'));
var_dump(Check::extensionAvailable('extensionNotExists'));

include_once('test/res/Interface.php');
include_once('test/res/Class.php');
var_dump(Check::classImplements('Test', 'iTest'));
var_dump(Check::classImplements('Test', 'iTestNotExists'));
```

### Info

```php
use robertsaupe\SystemInfo\Info;

var_dump(Info::getTotalSpace());
var_dump(Info::decodeSizeBinary(Info::getTotalSpace()));
var_dump(Info::decodeSizeDecimal(Info::getTotalSpace()));

var_dump(Info::getFreeSpace());
var_dump(Info::decodeSizeBinary(Info::getFreeSpace()));
var_dump(Info::decodeSizeDecimal(Info::getFreeSpace()));

var_dump(Info::getUsedSpace());
var_dump(Info::decodeSizeBinary(Info::getUsedSpace()));
var_dump(Info::decodeSizeDecimal(Info::getUsedSpace()));

var_dump(Info::getDirectorySize());
var_dump(Info::decodeSizeBinary(Info::getDirectorySize()));
var_dump(Info::decodeSizeDecimal(Info::getDirectorySize()));

var_dump(Info::getDirectorySizeExec());
var_dump(Info::decodeSizeBinary(Info::getDirectorySizeExec()));
var_dump(Info::decodeSizeDecimal(Info::getDirectorySizeExec()));

var_dump(Info::getDirectorySizeNative());
var_dump(Info::decodeSizeBinary(Info::getDirectorySizeNative()));
var_dump(Info::decodeSizeDecimal(Info::getDirectorySizeNative()));

var_dump(Info::getProcessUserID());
var_dump(Info::getProcessUserName());

print_r(Info::getEnv());

print_r(Info::getEnvironment());

print_r(Info::phpInfo());

print_r(Info::phpInfoExec());
```

### OS

```php
use robertsaupe\SystemInfo\OS;

var_dump(OS::getFamily());
var_dump(OS::getType());

var_dump(OS::getInfo());
var_dump(OS::getHostName());
var_dump(OS::getOSName());
var_dump(OS::getReleaseName());
var_dump(OS::getVersion());
var_dump(OS::getMachineType());

var_dump(OS::getLinuxInfo());
```

## Credits

- Ahmad <https://stackoverflow.com/a/42397673> for getOSInformation(), which is used as OS::getLinuxInfo()

## History

- some parts was originally written in 2018
- complete rewritten and published in 2022
