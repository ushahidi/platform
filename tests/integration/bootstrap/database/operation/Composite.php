<?php

namespace Tests\Integration\Bootstrap\Database\Operation;

class Composite implements Operation
{
    protected $operations = [];

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
                $operation->execute($connection, $dataSet);
            }
        } catch (\Exception $e) {
            throw new \Exception("COMPOSITE[{$e->getOperation()}]", $e->getQuery(), $e->getArgs(), $e->getTable(), $e->getError());
        }
    }
}