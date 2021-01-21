<?php

namespace Tests\Integration\Bootstrap\Database;

class TableIterator
{
    protected $tableNames;
    protected $reverse;
    protected $dataSet;

    public function __construct($tableNames, $dataSet, $reverse = false)
    {
        $this->tableNames = $tableNames;
        $this->dataSet    = $dataSet;
        $this->reverse    = $reverse;
        $this->rewind();
    }

    public function getTable()
    {
        return $this->current();
    }

    public function getTableMetaData()
    {
        return $this->current()->getTableMetaData();
    }

    public function current()
    {
        $tableName = \current($this->tableNames);

        return $this->dataSet->getTable($tableName);
    }

    public function key()
    {
        return $this->current()->getTableMetaData()->getTableName();
    }

    public function next(): void
    {
        if ($this->reverse) {
            \prev($this->tableNames);
        } else {
            \next($this->tableNames);
        }
    }

    public function rewind(): void
    {
        if ($this->reverse) {
            \end($this->tableNames);
        } else {
            \reset($this->tableNames);
        }
    }

    public function valid()
    {
        return \current($this->tableNames) !== false;
    }
}