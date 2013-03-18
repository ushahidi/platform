<?php

return array(

	/**
	 * A mapping of group_connections => db_connection to use
	 */
	'group_connection' => array(),

	/**
	 * The table used to store migrations
	 */
	'table' => 'migrations',

	/**
	 * This specifies which migration should be the "base", in timestamp form.
	 * This migration will not be run when --migrate-down is called
	 *
	 * NULL means all migrations will run
	 */
	'lowest_migration' => NULL,
);
