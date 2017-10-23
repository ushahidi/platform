<?php

$di = service();

$di->set('authorizer.console', $di->lazyNew('Ushahidi\Console\Authorizer\ConsoleAuthorizer'));
    
    
// Post Exporter
$di->setter['Ushahidi\Console\Application']['injectCommands'][] =
$di->lazyNew('Ushahidi\Console\Command\PostExporter');
$di->setter['Ushahidi\Console\Command\PostExporter']['setPostExportRepo'] = $di->lazyGet('repository.posts_export');
$di->setter['Ushahidi\Console\Command\PostExporter']['setDataFactory'] = $di->lazyGet('factory.data');

