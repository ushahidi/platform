<?php

namespace Tests\Integration\Bootstrap\Database;

use PDO;
use Tests\Integration\Bootstrap\Database\Metadata\AbstractMetadata;

class DefaultConnection
{
    protected $connection;
    protected $metaData;

    public function __construct($connection, $schema = '')
    {
        $this->connection = $connection;
        $this->metaData = AbstractMetadata::createMetaData($connection, $schema);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }

    public function close(): void
    {
        unset($this->connection, $this->metaData);
    }

    public function getMetaData()
    {
        return $this->metaData;
    }

    public function getSchema()
    {
        return $this->getMetaData()->getSchema();
    }

    public function createDataSet(array $tableNames = null)
    {
        if (empty($tableNames)) {
            return new DataSet($this);
        }

        return new FilteredDataSet($this, $tableNames);
    }

    public function getConfig(): void
    {
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function getRowCount($tableName, $whereClause = null)
    {
        $query = 'SELECT COUNT(*) FROM ' . $this->quoteSchemaObject($tableName);

        if (isset($whereClause)) {
            $query .= " WHERE {$whereClause}";
        }

        return (int) $this->connection->query($query)->fetchColumn();
    }

    public function quoteSchemaObject($object)
    {
        return $this->getMetaData()->quoteSchemaObject($object);
    }

    public function getTruncateCommand()
    {
        return $this->getMetaData()->getTruncateCommand();
    }

    public function allowsCascading()
    {
        return $this->getMetaData()->allowsCascading();
    }

    public function disablePrimaryKeys($tableName)
    {
        $this->getMetaData()->disablePrimaryKeys($tableName);
    }

    public function enablePrimaryKeys($tableName)
    {
        $this->getMetaData()->enablePrimaryKeys($tableName);
    }
}