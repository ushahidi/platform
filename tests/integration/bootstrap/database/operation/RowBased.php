<?php

namespace Tests\Integration\Bootstrap\Database\Operation;

abstract class RowBased implements Operation
{
    const ITERATOR_TYPE_FORWARD = 0;
    const ITERATOR_TYPE_REVERSE = 1;

    protected $operationName;

    protected $iteratorDirection = self::ITERATOR_TYPE_FORWARD;

    public function execute($connection, $dataSet): void
    {
        $databaseDataSet = $connection->createDataSet();

        $dsIterator = $this->iteratorDirection == self::ITERATOR_TYPE_REVERSE ? $dataSet->getReverseIterator() : $dataSet->getIterator();

        while ($dsIterator->valid()) {
            $table = $dsIterator->current();


            $rowCount = $table->getRowCount();

            if ($rowCount !== 0) {
                $databaseTableMetaData = $databaseDataSet->getTableMetaData($table->getTableMetaData()->getTableName());
                $query                 = $this->buildOperationQuery($databaseTableMetaData, $table, $connection);
                $disablePrimaryKeys    = $this->disablePrimaryKeys($databaseTableMetaData, $table, $connection);

                if ($query === false) {
                    throw new \Exception($this->operationName, '', [], $table, 'Rows requested for insert, but no columns provided!');
                } else {
                    if ($disablePrimaryKeys) {
                        $connection->disablePrimaryKeys($databaseTableMetaData->getTableName());
                    }

                    $statement = $connection->getConnection()->prepare($query);

                    for ($i = 0; $i < $rowCount; $i++) {
                        $args = $this->buildOperationArguments($databaseTableMetaData, $table, $i);

                        try {
                            $statement->execute($args);
                        } catch (\Exception $e) {
                            throw new \Exception(
                                $this->operationName,
                                $query,
                                $args,
                                $table,
                                $e->getMessage()
                            );
                        }
                    }

                    if ($disablePrimaryKeys) {
                        $connection->enablePrimaryKeys($databaseTableMetaData->getTableName());
                    }
                }
            }

            $dsIterator->next();
        }
    }

    abstract protected function buildOperationQuery($databaseTableMetaData, $table, $connection);

    abstract protected function buildOperationArguments($databaseTableMetaData, $table, $row);

    protected function disablePrimaryKeys($databaseTableMetaData, $table, $connection)
    {
        return false;
    }

    protected function buildPreparedColumnArray($columns, $connection)
    {
        $columnArray = [];

        foreach ($columns as $columnName) {
            $columnArray[] = "{$connection->quoteSchemaObject($columnName)} = ?";
        }

        return $columnArray;
    }
}