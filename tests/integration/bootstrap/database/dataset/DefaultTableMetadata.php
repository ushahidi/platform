<?php

namespace Tests\Integration\Bootstrap\Database\DataSet;

use Tests\Integration\Bootstrap\Database\DataSet\AbstractTableMetadata;

class DefaultTableMetadata extends AbstractTableMetadata
{
    public function __construct($tableName, array $columns, array $primaryKeys = [])
    {
        $this->tableName   = $tableName;
        $this->columns     = $columns;
        $this->primaryKeys = [];

        foreach ($primaryKeys as $columnName) {
            if (!\in_array($columnName, $this->columns)) {
                throw new \Exception('Primary key column passed that is not in the column list.');
            }
            $this->primaryKeys[] = $columnName;
        }
    }
}