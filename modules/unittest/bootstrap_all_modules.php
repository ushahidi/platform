<?php

include_once('bootstrap.php');

// Enable all modules we can find
$modules_iterator = new DirectoryIterator(MODPATH);

$modules = array();

foreach ($modules_iterator as $module)
{
	if ($module->isDir())
	{
		$modules[$module->getFilename()] = MODPATH.$module->getFilename();
	}
}

Kohana::modules(Kohana::modules() + $modules);

unset ($modules_iterator, $modules, $module);
