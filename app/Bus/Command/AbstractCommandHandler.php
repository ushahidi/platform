<?php

namespace App\Bus\Command;

use InvalidArgumentException;

abstract class AbstractCommandHandler implements CommandHandler
{
    /**
     * @param Command $command
     * @return void
     * @throws InvalidArgumentException
     */
    abstract protected function isSupported(Command $command);
}
