<?php

$di = service();

$di->set('authorizer.console', $di->lazyNew('Ushahidi\Console\Authorizer\ConsoleAuthorizer'));
