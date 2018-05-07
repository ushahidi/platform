<?php

namespace Ushahidi\App\Console\Commands;

use Illuminate\Console\Command;
use Phinx\Console\Command\SeedCreate as PhinxSeedCreateCommand;

class SeedMakeCommand extends PhinxSeedCreateCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('make:seeder');
    }
}
