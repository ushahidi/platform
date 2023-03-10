<?php

namespace Ushahidi\Tests\Integration\Bootstrap\Database;

class FilteredDataset extends Dataset
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
