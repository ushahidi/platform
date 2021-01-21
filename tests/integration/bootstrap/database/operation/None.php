<?php

namespace Tests\Integration\Bootstrap\Database\Operation;

class None implements Operation
{
    public function execute($connection, $dataSet): void
    {
        // nothing
    }
}