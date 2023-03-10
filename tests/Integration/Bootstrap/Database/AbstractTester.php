<?php

namespace Ushahidi\Tests\Integration\Bootstrap\Database;

use Ushahidi\Tests\Integration\Bootstrap\Database\Operation\Factory;

abstract class AbstractTester
{
    protected $setUpOperation;

    protected $tearDownOperation;

    protected $dataSet;

    protected $schema;

    public function __construct()
    {
        $this->setUpOperation = Factory::CLEAN_INSERT();
        $this->tearDownOperation = Factory::NONE();
    }

    public function closeConnection($connection): void
    {
        $connection->close();
    }

    public function getDataset()
    {
        return $this->dataSet;
    }

    public function onSetUp(): void
    {
        $this->getSetUpOperation()->execute($this->getConnection(), $this->getDataset());
    }

    public function onTearDown(): void
    {
        $this->getTearDownOperation()->execute($this->getConnection(), $this->getDataset());
    }

    public function setDataset($dataSet): void
    {
        $this->dataSet = $dataSet;
    }

    public function setSchema($schema): void
    {
        $this->schema = $schema;
    }

    public function setSetUpOperation($setUpOperation): void
    {
        $this->setUpOperation = $setUpOperation;
    }

    public function setTearDownOperation($tearDownOperation): void
    {
        $this->tearDownOperation = $tearDownOperation;
    }

    protected function getSchema()
    {
        return $this->schema;
    }

    protected function getSetUpOperation()
    {
        return $this->setUpOperation;
    }

    protected function getTearDownOperation()
    {
        return $this->tearDownOperation;
    }
}
