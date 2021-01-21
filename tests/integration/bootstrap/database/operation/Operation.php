<?php

namespace Tests\Integration\Bootstrap\Database\Operation;

/**
 * Provides a basic interface and functionality for executing database
 * operations against a connection using a specific dataSet.
 */
interface Operation
{
    /**
     * Executes the database operation against the given $connection for the
     * given $dataSet.
     */
    public function execute($connection, $dataSet);
}