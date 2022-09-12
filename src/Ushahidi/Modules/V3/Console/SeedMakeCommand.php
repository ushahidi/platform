<?php

namespace Ushahidi\Modules\V3\Console;

use Phinx\Console\Command\SeedCreate as PhinxSeedCreateCommand;

class SeedMakeCommand extends PhinxSeedCreateCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('phinx:seed:create');
    }
}
