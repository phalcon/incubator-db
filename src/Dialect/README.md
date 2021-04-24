# Phalcon\Db\Dialect

## Oracle

Generates database specific SQL for the Oracle RDBMS.

```php
use Phalcon\Db\Adapter\Pdo\Oracle;
use Phalcon\Db\Adapter\Pdo\Oracle as Connection;

$di->set(
    'db',
    function () {
        return new Connection(
            [
                'dbname'       => '//localhost/enigma',
                'username'     => 'oracle',
                'password'     => 'secret',
                'dialectClass' => Oracle::class,
            ]
        );
    }
);
```
