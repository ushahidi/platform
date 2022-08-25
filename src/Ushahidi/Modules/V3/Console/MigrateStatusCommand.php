<?php

namespace Ushahidi\Modules\V3\Console;

use Illuminate\Console\Command;
use Phinx\Console\Command\Status as PhinxStatusCommand;

class MigrateStatusCommand extends PhinxStatusCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('phinx:migrate:status');
    }
}
