<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Integration\Bootstrap\Database;

use Tests\Integration\Bootstrap\Database\Operation\Factory;

/**
 * Can be used as a foundation for new DatabaseTesters.
 */
abstract class AbstractTester
{
    /**
     * @var Operation
     */
    protected $setUpOperation;

    /**
     * @var Operation
     */
    protected $tearDownOperation;

    /**
     * @var IDataSet
     */
    protected $dataSet;

    /**
     * @var string
     */
    protected $schema;

    /**
     * Creates a new database tester.
     */
    public function __construct()
    {
        $this->setUp = Factory::CLEAN_INSERT();
        $this->tearDown = Factory::NONE();
    }

    /**
     * Closes the specified connection.
     *
     * @param $connection
     */
    public function closeConnection($connection): void
    {
        $connection->close();
    }

    /**
     * Returns the test dataset.
     *
     * @return IDataSet
     */
    public function getDataSet()
    {
        return $this->dataSet;
    }

    /**
     * TestCases must call this method inside setUp().
     */
    public function onSetUp(): void
    {
        $this->getSetUpOperation()->execute($this->getConnection(), $this->getDataSet());
    }

    /**
     * TestCases must call this method inside tearDown().
     */
    public function onTearDown(): void
    {
        $this->getTearDownOperation()->execute($this->getConnection(), $this->getDataSet());
    }

    /**
     * Sets the test dataset to use.
     *
     * @param $dataSet
     */
    public function setDataSet($dataSet): void
    {
        $this->dataSet = $dataSet;
    }

    /**
     * Sets the schema value.
     *
     * @param string $schema
     */
    public function setSchema($schema): void
    {
        $this->schema = $schema;
    }

    /**
     * Sets the Databaseto call when starting the test.
     *
     * @param $setUpOperation
     */
    public function setSetUpOperation($setUpOperation): void
    {
        $this->setUp= $setUpOperation;
    }

    /**
     * Sets the Databaseto call when ending the test.
     *
     * @param $tearDownOperation
     */
    public function setTearDownOperation($tearDownOperation): void
    {
        $this->tearDown= $tearDownOperation;
    }

    /**
     * Returns the schema value
     *
     * @return string
     */
    protected function getSchema()
    {
        return $this->schema;
    }

    /**
     * Returns the database that will be called when starting the test.
     *
     * @return Operation
     */
    protected function getSetUpOperation()
    {
        return $this->setUpOperation;
    }

    /**
     * Returns the database that will be called when ending the test.
     *
     * @return Operation
     */
    protected function getTearDownOperation()
    {
        return $this->tearDownOperation;
    }
}