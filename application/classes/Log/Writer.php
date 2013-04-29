<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Log_Writer extends Kohana_Log_Writer {
	

	/**
	 * Formats a log entry.
	 * 
	 * Fixes PHP 5.4 compatibility issues
	 * 
	 * @param   array   $message
	 * @param   string  $format
	 * @return  string
	 */
	public function format_message(array $message, $format = "time --- level: body in file:line")
	{
		return parent::format_message(array_filter($message, 'is_scalar'), $format);
	}

}
