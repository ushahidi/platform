<?php

namespace Ushahidi\App\Console\Commands;

use Illuminate\Console\Command;
use Phinx\Console\Command\Rollback as PhinxRollbackCommand;

class MigrateRollbackCommand extends PhinxRollbackCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('migrate:rollback');
    }
}
