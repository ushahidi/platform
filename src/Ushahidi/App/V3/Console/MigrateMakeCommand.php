<?php

namespace Ushahidi\App\V3\Console;

use Illuminate\Console\Command;
use Phinx\Console\Command\Create as PhinxCreateCommand;

class MigrateMakeCommand extends PhinxCreateCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('phinx:migrate:make');
    }
}
