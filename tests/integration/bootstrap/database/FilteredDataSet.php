<?php

namespace Tests\Integration\Bootstrap\Database;

class FilteredDataSet extends DataSet
{
    protected $tableNames;

    public function __construct($databaseConnection, array $tableNames)
    {
        parent::__construct($databaseConnection);
        $this->tableNames = $tableNames;
    }

    public function getTableNames()
    {
        return $this->tableNames;
    }
}