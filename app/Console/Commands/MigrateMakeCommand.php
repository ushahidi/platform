<?php

namespace Ushahidi\App\Console\Commands;

use Illuminate\Console\Command;
use Phinx\Console\Command\Create as PhinxCreateCommand;

class MigrateMakeCommand extends PhinxCreateCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('migrate:make');
    }
}
