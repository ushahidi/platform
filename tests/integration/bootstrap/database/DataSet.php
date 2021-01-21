<?php

namespace Tests\Integration\Bootstrap\Database;

use Tests\Integration\Bootstrap\Database\DataSet\AbstractDataSet;
use Tests\Integration\Bootstrap\Database\DataSet\DefaultTableMetadata;


class DataSet extends AbstractDataSet
{
    protected $tables = [];
    protected $databaseConnection;

    public static function buildTableSelect($tableMetaData, $databaseConnection = null)
    {
        if ($tableMetaData->getTableName() == '') {
            $e = new \Exception('Empty Table Name');
            print $e->getTraceAsString();

            throw $e;
        }

        $columns = $tableMetaData->getColumns();

        if ($databaseConnection) {
            $columns = \array_map([$databaseConnection, 'quoteSchemaObject'], $columns);
        }
        $columnList = \implode(', ', $columns);

        if ($databaseConnection) {
            $tableName = $databaseConnection->quoteSchemaObject($tableMetaData->getTableName());
        } else {
            $tableName = $tableMetaData->getTableName();
        }

        $primaryKeys = $tableMetaData->getPrimaryKeys();

        if ($databaseConnection) {
            $primaryKeys = \array_map([$databaseConnection, 'quoteSchemaObject'], $primaryKeys);
        }

        if (\count($primaryKeys)) {
            $orderBy = 'ORDER BY ' . \implode(' ASC, ', $primaryKeys) . ' ASC';
        } else {
            $orderBy = '';
        }

        return "SELECT {$columnList} FROM {$tableName} {$orderBy}";
    }

    public function __construct($databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    public function getTable($tableName)
    {
        if (!\in_array($tableName, $this->getTableNames())) {
            throw new \Exception("$tableName is not a table in the current database.");
        }

        if (empty($this->tables[$tableName])) {
            $this->tables[$tableName] = new Table($this->getTableMetaData($tableName), $this->databaseConnection);
        }

        return $this->tables[$tableName];
    }

    public function getTableMetaData($tableName)
    {
        return new DefaultTableMetadata($tableName, $this->databaseConnection->getMetaData()->getTableColumns($tableName), $this->databaseConnection->getMetaData()->getTablePrimaryKeys($tableName));
    }

    public function getTableNames()
    {
        return $this->databaseConnection->getMetaData()->getTableNames();
    }

    protected function createIterator($reverse = false)
    {
        return new TableIterator($this->getTableNames(), $this, $reverse);
    }
}