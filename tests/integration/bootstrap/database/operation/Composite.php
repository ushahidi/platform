<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Integration\Bootstrap\Database\Operation;

/**
 * This class facilitates combining database operations. To create a composite
 * operation pass an array of classes that implement
 * PHPUnit_Extensions_Database_Operation_IDatabaseOperation and they will be
 * executed in that order against all data sets.
 */
class Composite implements Operation
{
    /**
     * @var array
     */
    protected $operations = [];

    /**
     * Creates a composite operation.
     *
     * @param array $operations
     */
    public function __construct(array $operations)
    {
        foreach ($operations as $operation) {
            if ($operation instanceof Operation) {
                $this->operations[] = $operation;
            } else {
                throw new \Exception('Only database operation instances can be passed to a composite database operation.');
            }
        }
    }

    public function execute($connection, $dataSet): void
    {
        try {
            foreach ($this->operations as $operation) {
                /* @var $operation Operation */
                $operation->execute($connection, $dataSet);
            }
        } catch (\Exception $e) {
            throw new \Exception("COMPOSITE[{$e->getOperation()}]", $e->getQuery(), $e->getArgs(), $e->getTable(), $e->getError());
        }
    }
}