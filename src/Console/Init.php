<?php

$di = service();

// Console application is used for command line tools.
$di->set('app.console', $di->lazyNew('Ushahidi\Console\Application'));

// Any command can be registered with the console app.
$di->params['Ushahidi\Console\Application']['injectCommands'] = [];

// Set up Import command
$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi\Console\Command\Import');
$di->setter['Ushahidi\Console\Command\Import']['setReaderMap'] = [];
$di->setter['Ushahidi\Console\Command\Import']['setReaderMap']['csv'] = $di->lazyGet('filereader.csv');
$di->setter['Ushahidi\Console\Command\Import']['setTransformer'] = $di->lazyGet('transformer.mapping');
$di->setter['Ushahidi\Console\Command\Import']['setImportUsecase'] = $di->lazy(function () use ($di) {
	return service('factory.usecase')
			->get('posts', 'import')
			// Override authorizer for console
			->setAuthorizer($di->get('authorizer.console'));
});

$di->set('authorizer.console', $di->lazyNew('Ushahidi\Console\Authorizer\ConsoleAuthorizer'));

// Notification Collection command
$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi\Console\Command\Notification');
$di->setter['Ushahidi\Console\Command\Notification']['setDatabase'] = $di->lazyGet('kohana.db');
$di->setter['Ushahidi\Console\Command\Notification']['setPostRepo'] = $di->lazyGet('repository.post');
$di->setter['Ushahidi\Console\Command\Notification']['setMessageRepo'] = $di->lazyGet('repository.message');
$di->setter['Ushahidi\Console\Command\Notification']['setContactRepo'] = $di->lazyGet('repository.contact');
$di->setter['Ushahidi\Console\Command\Notification']['setNotificationQueueRepo'] =
	$di->lazyGet('repository.notification.queue');
$di->setter['Ushahidi\Console\Command\Notification']['setSiteConfig'] = $di->lazyGet('site.config');
$di->setter['Ushahidi\Console\Command\Notification']['setClientUrl'] = $di->lazyGet('clienturl');

// Notification SavedSearch command
$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi\Console\Command\SavedSearch');
$di->setter['Ushahidi\Console\Command\SavedSearch']['setSetRepo'] = $di->lazyGet('repository.savedsearch');
$di->setter['Ushahidi\Console\Command\SavedSearch']['setPostRepo'] = $di->lazyGet('repository.post');
$di->setter['Ushahidi\Console\Command\SavedSearch']['setMessageRepo'] = $di->lazyGet('repository.message');
$di->setter['Ushahidi\Console\Command\SavedSearch']['setContactRepo'] = $di->lazyGet('repository.contact');
$di->setter['Ushahidi\Console\Command\SavedSearch']['setDataFactory'] = $di->lazyGet('factory.data');

 // Post Exporter
 $di->setter['Ushahidi\Console\Application']['injectCommands'][] =
 	$di->lazyNew('Ushahidi\Console\Command\PostExporter');
 $di->setter['Ushahidi\Console\Command\PostExporter']['setPostExportRepo'] = $di->lazyGet('repository.posts_export');
 $di->setter['Ushahidi\Console\Command\PostExporter']['setDataFactory'] = $di->lazyGet('factory.data');

// Webhook command
$di->setter['Ushahidi\Console\Application']['injectCommands'][] = $di->lazyNew('Ushahidi\Console\Command\Webhook');
$di->setter['Ushahidi\Console\Command\Webhook']['setDatabase'] = $di->lazyGet('kohana.db');
$di->setter['Ushahidi\Console\Command\Webhook']['setPostRepo'] = $di->lazyGet('repository.post');
$di->setter['Ushahidi\Console\Command\Webhook']['setWebhookRepo'] = $di->lazyGet('repository.webhook');
$di->setter['Ushahidi\Console\Command\Webhook']['setWebhookJobRepo'] = $di->lazyGet('repository.webhook.job');
