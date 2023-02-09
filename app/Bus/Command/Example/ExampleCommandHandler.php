<?php

namespace App\Bus\Command\Example;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;

class ExampleCommandHandler extends AbstractCommandHandler
{
    /**
     * @param Action|ExampleCommand $action
     * @return void
     */
    public function __invoke(Action $action)
    {
        $this->isSupported($action);

        // ... perform some action
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === ExampleCommand::class,
            'Provided command not supported'
        );
    }
}
