<?php

namespace Tests\Integration\Bootstrap\Database\Operation;

use PDO;
use PDOException;

class Truncate implements Operation
{
    protected $useCascade = false;

    public function setCascade($cascade = true): void
    {
        $this->useCascade = $cascade;
    }

    public function execute($connection, $dataSet): void
    {
        $iterator = $dataSet->getReverseIterator();
        while ($iterator->valid()) {
            $table = $iterator->current();
            $query = "
                {$connection->getTruncateCommand()} {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
            ";

            if ($this->useCascade && $connection->allowsCascading()) {
                $query .= ' CASCADE';
            }

            try {
                $this->disableForeignKeyChecksForMysql($connection);
                $connection->getConnection()->query($query);
                $this->enableForeignKeyChecksForMysql($connection);
            } catch (\Exception $e) {
                $this->enableForeignKeyChecksForMysql($connection);

                if ($e instanceof PDOException) {
                    throw new \Exception('TRUNCATE', $query, [], $table, $e->getMessage());
                }

                throw $e;
            }
            $iterator->next();
        }
    }

    private function disableForeignKeyChecksForMysql($connection): void
    {
        if ($this->isMysql($connection)) {
            $connection->getConnection()->query('SET @PHPUNIT_OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS');
            $connection->getConnection()->query('SET FOREIGN_KEY_CHECKS = 0');
        }
    }

    private function enableForeignKeyChecksForMysql($connection): void
    {
        if ($this->isMysql($connection)) {
            $connection->getConnection()->query('SET FOREIGN_KEY_CHECKS=@PHPUNIT_OLD_FOREIGN_KEY_CHECKS');
        }
    }

    private function isMysql($connection)
    {
        return $connection->getConnection()->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql';
    }
}