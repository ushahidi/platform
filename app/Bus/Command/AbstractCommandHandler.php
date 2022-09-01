<?php

namespace Ushahidi\App\Bus\Command;

use Ushahidi\App\Bus\Action;

abstract class AbstractCommandHandler implements CommandHandler
{
    /**
     * @param Command $command
     * @throws \InvalidArgumentException
     * @return void
     */
    protected abstract function isSupported(Command $command);
}
