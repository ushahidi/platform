<?php

namespace Ushahidi\App\Bus\Command;

use InvalidArgumentException;

abstract class AbstractCommandHandler implements CommandHandler
{
    /**
     * @param Command $command
     * @return void
     * @throws InvalidArgumentException
     */
    protected abstract function isSupported(Command $command);
}
