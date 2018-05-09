<?php

namespace Ushahidi\App\Console\Commands;

use Illuminate\Console\Command;
use Phinx\Console\Command\Status as PhinxStatusCommand;

class MigrateStatusCommand extends PhinxStatusCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('migrate:status');
    }
}
