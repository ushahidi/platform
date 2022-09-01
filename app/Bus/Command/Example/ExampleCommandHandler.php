<?php

namespace Ushahidi\App\Bus\Command\Example;

use Ushahidi\App\Bus\Action;
use Ushahidi\App\Bus\Command\AbstractCommandHandler;
use Ushahidi\App\Bus\Command\Command;

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
