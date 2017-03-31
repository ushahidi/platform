<?php

// Bootstrap lumen
require __DIR__ . '/../bootstrap/app.php';

// Initialize the Kohana application
require __DIR__ . '/../application/kohana.php';

// when testing, docroot is the source root
define('DOCROOT', realpath(APPPATH . '/../') . DIRECTORY_SEPARATOR);

// Disable output buffering
if (($ob_len = ob_get_length()) !== FALSE)
{
	// flush_end on an empty buffer causes headers to be sent. Only flush if needed.
	if ($ob_len > 0)
	{
		ob_end_flush();
	}
	else
	{
		ob_end_clean();
	}
}

// Enable the unittest module
// Kohana::modules(Kohana::modules() + array('unittest' => MODPATH.'unittest'));
