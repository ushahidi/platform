<?php

namespace Ushahidi\App\Console\Commands;

use Illuminate\Console\Command;
use Phinx\Console\Command\SeedRun as PhinxSeedRunCommand;

class SeedCommand extends PhinxSeedRunCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('db:seed');
    }
}
