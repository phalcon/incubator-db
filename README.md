# Phalcon\Incubator\Db

[![Discord](https://img.shields.io/discord/310910488152375297?label=Discord)](http://phalcon.io/discord)
[![Packagist Version](https://img.shields.io/packagist/v/phalcon/incubator-db)](https://packagist.org/packages/phalcon/incubator-db)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/phalcon/incubator-db)](https://packagist.org/packages/phalcon/incubator-db)
[![codecov](https://codecov.io/gh/phalcon/incubator-db/branch/master/graph/badge.svg)](https://codecov.io/gh/phalcon/incubator-db)
[![Packagist](https://img.shields.io/packagist/dd/phalcon/incubator-db)](https://packagist.org/packages/phalcon/incubator-db/stats)

## Issues tracker

https://github.com/phalcon/incubator/issues

## Oracle adapter

```php
use Phalcon\Incubator\Db\Adapter\Pdo\Oracle;

$di->set(
    'db',
    function () {
        return new Oracle(
            [
                'dbname'   => $_ENV['ORACLE_DB_NAME'],
                'username' => $_ENV['ORACLE_DB_USER'],
                'password' => $_ENV['ORACLE_DB_PASS'],
            ]
        );
    }
);
```
