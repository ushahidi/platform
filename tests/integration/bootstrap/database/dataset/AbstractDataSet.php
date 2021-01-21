<?php

namespace Tests\Integration\Bootstrap\Database\DataSet;

abstract class AbstractDataSet
{
    public function __toString()
    {
        $iterator = $this->getIterator();

        $dataSetString = '';

        foreach ($iterator as $table) {
            $dataSetString .= $table->__toString();
        }

        return $dataSetString;
    }

    public function getTableNames()
    {
        $tableNames = [];

        foreach ($this->getIterator() as $table) {
            $tableNames[] = $table->getTableMetaData()->getTableName();
        }

        return $tableNames;
    }

    public function getTableMetaData($tableName)
    {
        return $this->getTable($tableName)->getTableMetaData();
    }

    public function getTable($tableName)
    {
        foreach ($this->getIterator() as $table) {
            if ($table->getTableMetaData()->getTableName() == $tableName) {
                return $table;
            }
        }
    }

    public function getIterator()
    {
        return $this->createIterator();
    }

    public function getReverseIterator()
    {
        return $this->createIterator(true);
    }

    public function matches($other)
    {
        $thisTableNames  = $this->getTableNames();
        $otherTableNames = $other->getTableNames();

        \sort($thisTableNames);
        \sort($otherTableNames);

        if ($thisTableNames != $otherTableNames) {
            return false;
        }

        foreach ($thisTableNames as $tableName) {
            $table = $this->getTable($tableName);

            if (!$table->matches($other->getTable($tableName))) {
                return false;
            }
        }

        return true;
    }

    abstract protected function createIterator($reverse = false);
}