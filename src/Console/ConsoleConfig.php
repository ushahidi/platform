<?php

namespace Ushahidi\Console;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;

class ConsoleConfig extends ContainerConfig
{
    public function define(Container $di)
    {
        $di->set('authorizer.console', $di->lazyNew('Ushahidi\Console\Authorizer\ConsoleAuthorizer'));
    }
}
