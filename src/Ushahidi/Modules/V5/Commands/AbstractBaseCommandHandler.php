<?php

namespace Ushahidi\Modules\V5\Commands;

use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\command;
use App\Bus\Action;
use InvalidArgumentException;
use Ushahidi\Modules\V5\Common\Errors;

abstract class AbstractBaseCommandHandler extends AbstractCommandHandler
{
    use Errors;
    
    /**
     * @param Command $command
     * @return void
     */
    abstract protected function run(Command $command);

    /**
     * @param Command $command
     * @return void
     * @throws InvalidArgumentException
     */
    abstract protected function isSupported(Command $command);

    public function __invoke(Action $command)
    {
        $this->isSupported($command);
        $this->run($command);
    }
}
