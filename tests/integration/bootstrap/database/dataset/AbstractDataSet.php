<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Integration\Bootstrap\Database\DataSet;

/**
 * Implements the basic functionality of data sets.
 */
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

    /**
     * Returns an array of table names contained in the dataset.
     *
     * @return array
     */
    public function getTableNames()
    {
        $tableNames = [];

        foreach ($this->getIterator() as $table) {
            /* @var $table ITable */
            $tableNames[] = $table->getTableMetaData()->getTableName();
        }

        return $tableNames;
    }

    /**
     * Returns a table meta data object for the given table.
     *
     * @param string $tableName
     *
     * @return ITableMetadata
     */
    public function getTableMetaData($tableName)
    {
        return $this->getTable($tableName)->getTableMetaData();
    }

    /**
     * Returns a table object for the given table.
     *
     * @param string $tableName
     *
     * @return ITable
     */
    public function getTable($tableName)
    {
        foreach ($this->getIterator() as $table) {
            /* @var $table ITable */
            if ($table->getTableMetaData()->getTableName() == $tableName) {
                return $table;
            }
        }
    }

    /**
     * Returns an iterator for all table objects in the given dataset.
     *
     * @return ITableIterator
     */
    public function getIterator()
    {
        return $this->createIterator();
    }

    /**
     * Returns a reverse iterator for all table objects in the given dataset.
     *
     * @return ITableIterator
     */
    public function getReverseIterator()
    {
        return $this->createIterator(true);
    }

    /**
     * Asserts that the given data set matches this data set.
     *
     * @param $other
     */
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

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     *
     * @param bool $reverse
     *
     * @return ITableIterator
     */
    abstract protected function createIterator($reverse = false);
}