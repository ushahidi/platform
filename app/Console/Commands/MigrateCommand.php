<?php

namespace Ushahidi\App\Console\Commands;

use Illuminate\Console\Command;
use Phinx\Console\Command\Migrate as PhinxMigrateCommand;
use Symfony\Component\Console\Input\InputOption;

class MigrateCommand extends PhinxMigrateCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption('force', 'f', InputOption::VALUE_NONE);
    }
}
