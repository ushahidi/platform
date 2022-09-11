<?php

namespace Ushahidi\App\Bus\Command\Example;

use Ushahidi\App\Bus\Command\Command;

/**
 * This class is not meant to be used. Treat it only as an example.
 */
class ExampleCommand implements Command
{
    /**
     * @var string
     */
    private $foo;

    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return string
     */
    public function getFoo(): string
    {
        return $this->foo;
    }
}
