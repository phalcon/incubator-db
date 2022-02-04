<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2016 Phalcon Team (https://www.phalconphp.com)      |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/

namespace Phalcon\Incubator\Db\Adapter\Pdo;

use Phalcon\Db\Adapter\Pdo\AbstractPdo;
use Phalcon\Db\Column;
use Phalcon\Db\ColumnInterface;
use Phalcon\Db\Enum;
use Phalcon\Db\RawValue;

/**
 * Phalcon\Incubator\Db\Adapter\Pdo\Oracle
 *
 * Specific functions for the Oracle RDBMS.
 *
 * <code>
 * use Phalcon\Incubator\Db\Adapter\Pdo\Oracle;
 *
 * $connection = new Oracle([
 *     'dbname'   => '//localhost/dbname',
 *     'username' => 'oracle',
 *     'password' => 'oracle',
 * ]);
 * </code>
 *
 * @property \Phalcon\Incubator\Db\Dialect\Oracle $dialect
 * @package \Phalcon\IncubatorDb\Adapter\Pdo
 */
class Oracle extends AbstractPdo
{
    protected $type = 'oci';
    protected $dialectType = 'oracle';

    /**
     * Returns an array of \Phalcon\Db\Column objects describing a table.
     *
     * <code>
     * var_dump($connection->describeColumns('posts'));
     * </code>
     *
     * @param string $table
     * @param null|string $schema
     *
     * @return ColumnInterface[]
     */
    public function describeColumns(string $table, string $schema = null): array
    {
        $columns = [];
        $oldColumn = null;

        /**
         * 0:column_name,
         * 1:data_type,
         * 2:data_length,
         * 3:data_precision,
         * 4:data_scale,
         * 5:nullable,
         * 6:constraint_type,
         * 7:default,
         * 8:position;
         */
        $sql = $this->dialect->describeColumns($table, $schema);
        $fields = $this->fetchAll($sql, Enum::FETCH_NUM);
        foreach ($fields as $field) {
            $definition = $this->getColumnType(['bindType' => 2], $field[1], $field[2], $field[3], $field[4]);

            if (null === $oldColumn) {
                $definition['first'] = true;
            } else {
                $definition['after'] = $oldColumn;
            }

            /**
             * Check if the field is primary key
             */
            if ('P' === $field[6]) {
                $definition['primary'] = true;
            }

            /**
             * Check if the column allows null values
             */
            if ('N' === $field[5]) {
                $definition['notNull'] = true;
            }

            $columns[] = new Column($field[0], $definition);
            $oldColumn = $field[0];
        }

        return $columns;
    }

    /**
     * Checks the column type to get the correct Phalcon type
     */
    protected function getColumnType(
        array $definition,
        string $columnType,
        $columnSize,
        $columnPrecision,
        $columnScale
    ): array {
        if (false !== strpos($columnType, 'NUMBER')) {
            $definition['type'] = Column::TYPE_DECIMAL;
            $definition['isNumeric'] = true;
            $definition['size'] = $columnPrecision;
            $definition['scale'] = $columnScale;
            $definition['bindType'] = 32;

            return $definition;
        }

        if (false !== strpos($columnType, 'INTEGER')) {
            $definition['type'] = Column::TYPE_INTEGER;
            $definition['isNumeric'] = true;
            $definition['size'] = $columnPrecision;
            $definition['bindType'] = 1;

            return $definition;
        }

        if (false !== strpos($columnType, 'VARCHAR2')) {
            $definition['type'] = Column::TYPE_VARCHAR;
            $definition['size'] = $columnSize;

            return $definition;
        }

        if (false !== strpos($columnType, 'FLOAT')) {
            $definition['type'] = Column::TYPE_FLOAT;
            $definition['isNumeric'] = true;
            $definition['size'] = $columnSize;
            $definition['scale'] = $columnScale;
            $definition['bindType'] = 32;

            return $definition;
        }

        if (false !== strpos($columnType, 'TIMESTAMP')) {
            $definition['type'] = Column::TYPE_TIMESTAMP;

            return $definition;
        }

        if (false !== strpos($columnType, 'DATE')) {
            $definition['type'] = Column::TYPE_DATE;

            return $definition;
        }

        if (false !== strpos($columnType, 'RAW')) {
            $definition['type'] = Column::TYPE_TEXT;

            return $definition;
        }

        if (false !== strpos($columnType, 'BLOB')) {
            $definition['type'] = Column::TYPE_TEXT;

            return $definition;
        }

        if (false !== strpos($columnType, 'CLOB')) {
            $definition['type'] = Column::TYPE_TEXT;

            return $definition;
        }

        if (false !== strpos($columnType, 'CHAR')) {
            $definition['type'] = Column::TYPE_CHAR;
            $definition['size'] = $columnSize;

            return $definition;
        }

        $definition['type'] = Column::TYPE_TEXT;

        return $definition;
    }

    /**
     * Returns the insert id for the auto_increment/serial column inserted in the latest executed SQL statement.
     *
     * <code>
     * // Inserting a new robot
     * $success = $connection->insert(
     *     'robots',
     *     ['Astro Boy', 1952],
     *     ['name', 'year'],
     * );
     *
     * // Getting the generated id
     * $id = $connection->lastInsertId();
     * <code>
     *
     * @param string|null $sequenceName
     *
     * @return int
     */
    public function lastInsertId($sequenceName = null): int
    {
        $sequenceName = $sequenceName ?: 'id';

        return $this->fetchAll('SELECT ' . $sequenceName . '.CURRVAL FROM dual', Enum::FETCH_NUM)[0][0];
    }

    /**
     * Check whether the database system requires an explicit value for identity columns;
     *
     * @return bool
     */
    public function useExplicitIdValue(): bool
    {
        return false;
    }

    /**
     * Return the default identity value to insert in an identity column.
     *
     * @return RawValue
     */
    public function getDefaultIdValue(): RawValue
    {
        return new RawValue('default');
    }

    /**
     * Check whether the database system requires a sequence to produce auto-numeric values.
     *
     * @return bool
     */
    public function supportSequences(): bool
    {
        return true;
    }

    protected function getDsnDefaults(): array
    {
        return [];
    }
}
