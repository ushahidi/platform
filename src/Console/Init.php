<?php

$di = service();

// Console application is used for command line tools.
$di->set('app.console', $di->lazyNew('Ushahidi\Console\Application'));

// Any command can be registered with the console app.
$di->params['Ushahidi\Console\Application']['injectCommands'] = [];

$di->set('authorizer.console', $di->lazyNew('Ushahidi\Console\Authorizer\ConsoleAuthorizer'));
    
    
// Post Exporter
$di->setter['Ushahidi\Console\Application']['injectCommands'][] =
$di->lazyNew('Ushahidi\Console\Command\PostExporter');
$di->setter['Ushahidi\Console\Command\PostExporter']['setPostExportRepo'] = $di->lazyGet('repository.posts_export');
$di->setter['Ushahidi\Console\Command\PostExporter']['setDataFactory'] = $di->lazyGet('factory.data');

