# Phalcon\Db\Adapter

Usage examples of the adapters available here:

## Pdo\Oracle

Specific functions for the Oracle RDBMS.

```php
use Phalcon\Db\Adapter\Pdo\Oracle;

$di->set(
    'db',
    function () {
        /** @var \Phalcon\DiInterface $this */

        $config = $this->getShared('config');

        $connection = new Oracle(
            [
                'dbname'   => $config->database->dbname,
                'username' => $config->database->username,
                'password' => $config->database->password,
            ]
        );

        return $connection;
    }
);
```
