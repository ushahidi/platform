<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Integration\Bootstrap\Database\Operation;


/**
 * This class provides functionality for inserting rows from a dataset into a database.
 */
class Insert extends RowBased
{
    protected $operationName = 'INSERT';

    protected function buildOperationQuery($databaseTableMetaData, $table, $connection)
    {
        $columnCount = \count($table->getTableMetaData()->getColumns());

        if ($columnCount > 0) {
            $placeHolders = \implode(', ', \array_fill(0, $columnCount, '?'));

            $columns = '';

            foreach ($table->getTableMetaData()->getColumns() as $column) {
                $columns .= $connection->quoteSchemaObject($column) . ', ';
            }

            $columns = \substr($columns, 0, -2);

            $query = "
                INSERT INTO {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
                ({$columns})
                VALUES
                ({$placeHolders})
            ";

            return $query;
        }

        return false;
    }

    protected function buildOperationArguments($databaseTableMetaData, $table, $row)
    {
        $args = [];

        foreach ($table->getTableMetaData()->getColumns() as $columnName) {
            $args[] = $table->getValue($row, $columnName);
        }

        return $args;
    }

    protected function disablePrimaryKeys($databaseTableMetaData, $table, $connection)
    {
        if (\count($databaseTableMetaData->getPrimaryKeys())) {
            return true;
        }

        return false;
    }
}