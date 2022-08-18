<?php

namespace Ushahidi\App\V3\Console;

use Illuminate\Console\Command;
use Phinx\Console\Command\Rollback as PhinxRollbackCommand;

class MigrateRollbackCommand extends PhinxRollbackCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('phinx:migrate:rollback');
    }
}
