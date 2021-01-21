<?php

namespace Tests\Integration\Bootstrap\Database\DataSet;

abstract class AbstractTableMetadata
{
    protected $columns;

    protected $primaryKeys;

    protected $tableName;

    public function getColumns()
    {
        return $this->columns;
    }

    public function getPrimaryKeys()
    {
        return $this->primaryKeys;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function matches($other)
    {
        if ($this->getTableName() != $other->getTableName() ||
            $this->getColumns() != $other->getColumns()
        ) {
            return false;
        }

        return true;
    }
}