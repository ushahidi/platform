<?php

return array(
	'dsn' => env('SENTRY_DSN', false) ?: env('RAVEN_URL', false),

	// capture release as git sha
	// 'release' => trim(exec('git log --pretty="%h" -n1 HEAD')),

	// Capture bindings on SQL queries
	'breadcrumbs.sql_bindings' => true,

	// Capture default user context
	'user_context' => false, // Disabled because it causes requests to fail w/ 401
);
